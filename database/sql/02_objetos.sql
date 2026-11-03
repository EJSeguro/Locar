CREATE OR REPLACE FUNCTION fn_calcular_total_locacao(p_diaria numeric, p_inicio date, p_fim date)
RETURNS numeric
LANGUAGE sql
IMMUTABLE
AS $$
    SELECT round(p_diaria * (p_fim - p_inicio), 2);
$$;

CREATE OR REPLACE FUNCTION fn_veiculo_disponivel(p_veiculo_id bigint, p_inicio date, p_fim date)
RETURNS boolean
LANGUAGE sql
STABLE
AS $$
    SELECT EXISTS (SELECT 1 FROM vehicles WHERE id = p_veiculo_id AND available)
       AND NOT EXISTS (
            SELECT 1
            FROM rentals
            WHERE vehicle_id = p_veiculo_id
              AND status = 'ativa'
              AND start_date < p_fim
              AND end_date > p_inicio
       );
$$;

CREATE OR REPLACE PROCEDURE sp_finalizar_locacao(p_locacao_id bigint)
LANGUAGE plpgsql
AS $$
BEGIN
    UPDATE rentals
    SET status = 'finalizada', updated_at = now()
    WHERE id = p_locacao_id AND status = 'ativa';

    IF NOT FOUND THEN
        RAISE EXCEPTION 'Locação % não está ativa.', p_locacao_id;
    END IF;
END;
$$;

CREATE OR REPLACE PROCEDURE sp_cancelar_locacao(p_locacao_id bigint)
LANGUAGE plpgsql
AS $$
BEGIN
    UPDATE rentals
    SET status = 'cancelada', updated_at = now()
    WHERE id = p_locacao_id AND status = 'ativa';

    IF NOT FOUND THEN
        RAISE EXCEPTION 'Locação % não está ativa.', p_locacao_id;
    END IF;
END;
$$;

CREATE OR REPLACE FUNCTION trg_fn_rentals_calcular_total()
RETURNS trigger
LANGUAGE plpgsql
AS $$
BEGIN
    NEW.total := fn_calcular_total_locacao(NEW.daily_rate, NEW.start_date, NEW.end_date);
    RETURN NEW;
END;
$$;

DROP TRIGGER IF EXISTS trg_rentals_calcular_total ON rentals;
CREATE TRIGGER trg_rentals_calcular_total
    BEFORE INSERT OR UPDATE OF daily_rate, start_date, end_date ON rentals
    FOR EACH ROW
    EXECUTE FUNCTION trg_fn_rentals_calcular_total();

CREATE OR REPLACE FUNCTION trg_fn_rentals_bloquear_conflito()
RETURNS trigger
LANGUAGE plpgsql
AS $$
BEGIN
    IF NEW.status = 'ativa' AND NOT fn_veiculo_disponivel(NEW.vehicle_id, NEW.start_date, NEW.end_date) THEN
        RAISE EXCEPTION 'Veículo % indisponível entre % e %.', NEW.vehicle_id, NEW.start_date, NEW.end_date;
    END IF;

    RETURN NEW;
END;
$$;

DROP TRIGGER IF EXISTS trg_rentals_bloquear_conflito ON rentals;
CREATE TRIGGER trg_rentals_bloquear_conflito
    BEFORE INSERT ON rentals
    FOR EACH ROW
    EXECUTE FUNCTION trg_fn_rentals_bloquear_conflito();
