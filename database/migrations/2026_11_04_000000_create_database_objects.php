<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::getPdo()->exec((string) file_get_contents(database_path('sql/02_objetos.sql')));
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::unprepared(<<<'SQL'
            DROP TRIGGER IF EXISTS trg_rentals_bloquear_conflito ON rentals;
            DROP TRIGGER IF EXISTS trg_rentals_calcular_total ON rentals;
            DROP FUNCTION IF EXISTS trg_fn_rentals_bloquear_conflito();
            DROP FUNCTION IF EXISTS trg_fn_rentals_calcular_total();
            DROP PROCEDURE IF EXISTS sp_cancelar_locacao(bigint);
            DROP PROCEDURE IF EXISTS sp_finalizar_locacao(bigint);
            DROP FUNCTION IF EXISTS fn_veiculo_disponivel(bigint, date, date);
            DROP FUNCTION IF EXISTS fn_calcular_total_locacao(numeric, date, date);
            SQL);
    }
};
