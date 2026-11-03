INSERT INTO users (name, email, password, role, created_at, updated_at) VALUES
    ('Administrador', 'admin@locar.com', '$2y$12$8nmSOOQ2ZA7AbytPSzrxgukg0zlqCo/RKaV99JWcKUUWQw0K7UZui', 'admin', now(), now()),
    ('Gestor', 'gestor@locar.com', '$2y$12$8nmSOOQ2ZA7AbytPSzrxgukg0zlqCo/RKaV99JWcKUUWQw0K7UZui', 'gestor', now(), now()),
    ('Cliente', 'cliente@locar.com', '$2y$12$8nmSOOQ2ZA7AbytPSzrxgukg0zlqCo/RKaV99JWcKUUWQw0K7UZui', 'cliente', now(), now())
ON CONFLICT (email) DO NOTHING;

INSERT INTO vehicles (plate, fipe_code, year_id, brand, model, model_year, fuel, fipe_price, daily_rate, created_at, updated_at) VALUES
    ('RIO2A24', '001510-8', '2024-1', 'Fiat', 'Mobi Like 1.0 Flex', 2024, 'Flex', 68500, 102.75, now(), now()),
    ('SPO3B23', '004461-2', '2023-1', 'Chevrolet', 'Onix 1.0 LT Flex', 2023, 'Flex', 82300, 123.45, now(), now()),
    ('BHZ4C22', '015173-7', '2022-1', 'Hyundai', 'HB20 Comfort 1.0 Flex', 2022, 'Flex', 71900, 107.85, now(), now()),
    ('CWB5D24', '005536-3', '2024-1', 'Volkswagen', 'T-Cross 200 TSI Flex', 2024, 'Flex', 132400, 198.60, now(), now()),
    ('POA6E23', '002201-5', '2023-1', 'Toyota', 'Corolla XEi 2.0 Flex', 2023, 'Flex', 148900, 223.35, now(), now())
ON CONFLICT (plate) DO NOTHING;

INSERT INTO rentals (user_id, vehicle_id, start_date, end_date, daily_rate, total, status, created_at, updated_at)
SELECT u.id, v.id, r.inicio, r.fim, v.daily_rate, 0, r.status, now(), now()
FROM (VALUES
    ('RIO2A24', current_date - 20, current_date - 15, 'finalizada'),
    ('SPO3B23', current_date - 10, current_date - 8, 'cancelada'),
    ('CWB5D24', current_date + 3, current_date + 7, 'ativa')
) AS r (placa, inicio, fim, status)
JOIN vehicles v ON v.plate = r.placa
JOIN users u ON u.email = 'cliente@locar.com'
WHERE NOT EXISTS (SELECT 1 FROM rentals WHERE user_id = u.id);
