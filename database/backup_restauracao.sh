#!/usr/bin/env bash
set -euo pipefail

: "${DB_URL:?defina DB_URL com a connection string do Neon}"
: "${DB_URL_RESTAURO:?defina DB_URL_RESTAURO com o banco de destino}"

mkdir -p backups
ARQUIVO="locar_$(date +%Y-%m-%d_%H%M)"
PG=(docker run --rm -v "$PWD/backups:/backup" -w /backup postgres:18)

"${PG[@]}" pg_dump "$DB_URL" --no-owner --no-acl -Fc -f "$ARQUIVO.dump"
"${PG[@]}" pg_dump "$DB_URL" --no-owner --no-acl -f "$ARQUIVO.sql"

"${PG[@]}" pg_restore --no-owner --no-acl --clean --if-exists -d "$DB_URL_RESTAURO" "$ARQUIVO.dump"

CONTAGEM="SELECT 'migrations' AS tabela, count(*) FROM migrations
UNION ALL SELECT 'rentals', count(*) FROM rentals
UNION ALL SELECT 'users', count(*) FROM users
UNION ALL SELECT 'vehicles', count(*) FROM vehicles ORDER BY 1;"

{
    echo "== Contagem no Neon (origem) =="
    "${PG[@]}" psql "$DB_URL" -c "$CONTAGEM"
    echo "== Contagem no banco restaurado =="
    "${PG[@]}" psql "$DB_URL_RESTAURO" -c "$CONTAGEM"
} | tee "backups/evidencia_$ARQUIVO.txt"
