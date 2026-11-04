# Locar

Aplicação web e API para locação de veículos, feita em Laravel com PostgreSQL (Neon). O sistema tem três perfis: admin, gestor e cliente.

O valor da diária de cada veículo é 0,15% do seu preço na tabela FIPE, consultado na [API da Invertexto](https://api.invertexto.com/api-tabela-fipe).

## Requisitos

- PHP 8.4 com a extensão `pdo_pgsql`
- Composer
- Banco PostgreSQL ([Neon](https://neon.tech))

## Setup local

### 1. Clonar e instalar

```bash
git clone https://github.com/EJSeguro/Locar.git
cd Locar
composer setup
```

O `composer setup` instala as dependências PHP, cria o `.env` a partir do `.env.example` e gera a `APP_KEY`. Ele também roda as migrations no SQLite local (`database/database.sqlite`). O banco de verdade é configurado no próximo passo.

### 2. Configurar o `.env`

| Variável           | Descrição                                                                                                  |
| ------------------ | ---------------------------------------------------------------------------------------------------------- |
| `DB_CONNECTION`    | `pgsql`                                                                                                    |
| `DB_URL`           | Connection string do Neon. Use o endpoint direto, sem `-pooler` no host: as migrations falham pelo pooler. |
| `INVERTEXTO_TOKEN` | Token da API da Invertexto. Sem ele, as rotas `/fipe` e o cadastro de veículos não funcionam.              |

Peça a connection string e o token para quem administra o projeto. Não commite o `.env`.

### 3. Criar as tabelas e o admin de desenvolvimento

```bash
php artisan migrate
php artisan db:seed
```

O seeder cria os usuários `admin@locar.com`, `gestor@locar.com` e `cliente@locar.com`, todos com a senha `password`, além de 5 veículos e 3 locações do cliente. Pode rodar de novo sem duplicar nada.

### 4. Subir o servidor

```bash
composer dev
```

Esse comando sobe o servidor em `http://localhost:8000`. O front é Blade com CSS estático (`public/css/estilo.css`), sem etapa de build.

### 5. Testar o login

```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@locar.com","password":"password"}'
```

A resposta traz um `token`. Mande esse token no header `Authorization: Bearer <token>` nas outras rotas. Sempre envie `Accept: application/json`, senão os erros voltam como redirect em vez de JSON.

### Problemas comuns

- **`could not find driver`**: falta habilitar a extensão `pdo_pgsql` no `php.ini`.
- **Migrations travam ou falham no Neon**: o `DB_URL` está usando o host com `-pooler`. Troque pelo endpoint direto.
- **Login responde 429**: o login aceita 6 tentativas por minuto. Espere um minuto.

## Produção

Não rode o seeder em produção. Crie o primeiro admin com o comando:

```bash
php artisan migrate --force
php artisan app:create-admin
```

O comando pede nome, email, senha e a confirmação da senha. Em produção, a senha precisa ter no mínimo 12 caracteres, letras maiúsculas e minúsculas, números e símbolos, e não pode constar em vazamentos conhecidos.

Os demais admins e gestores são criados pela API, por um admin.

## Banco de dados

Os scripts SQL ficam em `database/sql` e podem ser rodados direto no PostgreSQL, sem o Laravel, nesta ordem:

| Script             | Conteúdo                                                        |
| ------------------ | --------------------------------------------------------------- |
| `01_estrutura.sql` | Tabelas `users`, `vehicles` e `rentals` com chaves e índices    |
| `02_objetos.sql`   | Functions, stored procedures e triggers                         |
| `03_dados.sql`     | Mesmos usuários, veículos e locações do seeder (senha `password`) |

```bash
psql "$DB_URL" -v ON_ERROR_STOP=1 -f database/sql/01_estrutura.sql -f database/sql/02_objetos.sql -f database/sql/03_dados.sql
```

Pelo Laravel, `php artisan migrate` cria as tabelas e a migration `create_database_objects` aplica o `02_objetos.sql`. Ela só roda no PostgreSQL; no SQLite dos testes é ignorada.

### Índices

| Índice                                         | Uso                                                              |
| ---------------------------------------------- | ---------------------------------------------------------------- |
| `rentals (vehicle_id, start_date, end_date)`   | Busca de conflito de datas ao reservar um veículo                |
| `rentals (status)`                             | Filtro de locações ativas                                        |
| `vehicles (available)`                         | Listagem pública mostra só veículos disponíveis                  |
| `users (role)`                                 | Filtro de usuários por perfil                                    |

### Functions, procedures e triggers

| Objeto                          | Tipo      | Regra de negócio                                                                 | Onde a aplicação usa                                      |
| ------------------------------- | --------- | -------------------------------------------------------------------------------- | --------------------------------------------------------- |
| `fn_calcular_total_locacao`     | function  | Total = diária × dias                                                            | `RentalService::total()` e trigger `trg_rentals_calcular_total` |
| `fn_veiculo_disponivel`         | function  | Veículo habilitado e sem locação ativa no período                                | Trigger `trg_rentals_bloquear_conflito`                   |
| `sp_finalizar_locacao`          | procedure | Finaliza locação ativa, erro se não estiver ativa                                | `RentalService::changeStatus()` (gestor finaliza)         |
| `sp_cancelar_locacao`           | procedure | Cancela locação ativa, erro se não estiver ativa                                 | `RentalService::changeStatus()` (cliente/gestor cancela)  |
| `trg_rentals_calcular_total`    | trigger   | `BEFORE INSERT OR UPDATE` recalcula o `total` com a function                     | Toda locação criada pela web, API ou seeder               |
| `trg_rentals_bloquear_conflito` | trigger   | `BEFORE INSERT` impede locação ativa com veículo desabilitado ou já reservado    | Toda locação criada pela web, API ou seeder               |

Para demonstrar direto no banco:

```sql
SELECT fn_calcular_total_locacao(106.03, '2026-11-10', '2026-11-13');
SELECT fn_veiculo_disponivel(1, current_date, current_date + 3);
CALL sp_finalizar_locacao(1);
```

### Backup e restauração

Precisa do `pg_dump`/`pg_restore` na mesma versão major do Neon (18) ou superior. Sem eles instalados, use o Docker trocando `pg_dump` por `docker run --rm -v "$PWD:/backup" -w /backup postgres:18 pg_dump`.

```bash
pg_dump "$DB_URL" --no-owner --no-acl -Fc -f locar.dump
pg_dump "$DB_URL" --no-owner --no-acl -f locar.sql
```

O `.dump` é o formato do `pg_restore`; o `.sql` é texto puro, legível e entregável como script. Para restaurar, crie uma branch vazia no Neon (ou outro banco) e rode:

```bash
pg_restore --no-owner --no-acl --clean --if-exists -d "$DB_URL_RESTAURO" locar.dump
psql "$DB_URL_RESTAURO" -c "SELECT count(*) FROM rentals;"
```

O script `database/backup_restauracao.sh` faz tudo isso de uma vez pelo Docker: gera o `.dump` e o `.sql`, restaura no banco de destino e salva a evidência com a contagem de registros das duas pontas. Os arquivos ficam na pasta `backups/`, que é ignorada pelo Git.

```bash
DB_URL="postgresql://..." DB_URL_RESTAURO="postgresql://..." bash database/backup_restauracao.sh
```

Não commite os backups: eles contêm os hashes de senha dos usuários.

## Rotas web

| Método | Rota                                   | Acesso        | Descrição                                     |
| ------ | -------------------------------------- | ------------- | --------------------------------------------- |
| GET    | `/`                                    | público       | Página inicial                                |
| GET    | `/veiculos`                            | público       | Veículos disponíveis                          |
| GET    | `/veiculos/{id}`                       | público       | Detalhes do veículo e formulário de locação   |
| GET    | `/cadastro`                            | visitante     | Cadastro de cliente                           |
| GET    | `/login`                               | visitante     | Login                                         |
| GET    | `/minhas-locacoes`                     | cliente       | Locações do cliente                           |
| POST   | `/locacoes`                            | cliente       | Cria locação                                  |
| PATCH  | `/minhas-locacoes/{id}/cancelar`       | cliente       | Cancela locação antes da retirada             |
| GET    | `/admin/usuarios`                      | admin         | Lista, cadastra, edita e exclui usuários      |
| GET    | `/gestor/veiculos`                     | admin, gestor | Lista veículos                                |
| GET    | `/gestor/veiculos/cadastrar`           | admin, gestor | Cadastra veículo pela FIPE                    |
| GET    | `/gestor/veiculos/{id}/editar`         | admin, gestor | Edita placa e diária, exclui veículo          |
| PATCH  | `/gestor/veiculos/{id}/disponibilidade`| admin, gestor | Habilita ou desabilita veículo                |
| GET    | `/gestor/locacoes`                     | admin, gestor | Lista e finaliza locações                     |

## API

Todas as rotas ficam em `/api/v1`. Exceto o login, todas exigem o header `Authorization: Bearer <token>`.

| Método | Rota                          | Acesso              | Descrição                                              |
| ------ | ----------------------------- | ------------------- | ------------------------------------------------------ |
| POST   | `/login`                      | público             | Retorna o token                                        |
| POST   | `/logout`                     | autenticado         | Revoga o token atual                                   |
| GET    | `/me`                         | autenticado         | Usuário logado                                         |
| GET    | `/users`                      | admin               | Lista usuários                                         |
| POST   | `/users`                      | admin               | Cria admin ou gestor                                   |
| GET    | `/users/{id}`                 | admin               | Detalha usuário                                        |
| DELETE | `/users/{id}`                 | admin               | Remove usuário sem locações                            |
| GET    | `/fipe/brands/{tipo}`         | admin, gestor       | Marcas (1 carro, 2 moto, 3 caminhão)                   |
| GET    | `/fipe/models/{marca}`        | admin, gestor       | Modelos da marca                                       |
| GET    | `/fipe/years/{codigo_fipe}`   | admin, gestor       | Anos e preços do modelo                                |
| GET    | `/vehicles`                   | admin, gestor       | Lista veículos                                         |
| POST   | `/vehicles`                   | admin, gestor       | Cadastra veículo                                       |
| GET    | `/vehicles/{id}`              | admin, gestor       | Detalha veículo                                        |
| PUT    | `/vehicles/{id}`              | admin, gestor       | Atualiza `plate`, `daily_rate` e `available`           |
| DELETE | `/vehicles/{id}`              | admin, gestor       | Remove veículo sem locações                            |
| PATCH  | `/vehicles/{id}/availability` | admin, gestor       | Habilita ou desabilita para locação (`available`)      |
| GET    | `/rentals`                    | autenticado         | Lista locações (cliente vê só as suas)                 |
| POST   | `/rentals`                    | cliente             | Aluga veículo (`vehicle_id`, `start_date`, `end_date`) |
| GET    | `/rentals/{id}`               | dono, admin, gestor | Detalha locação                                        |
| PATCH  | `/rentals/{id}/cancel`        | dono, admin, gestor | Cancela locação ativa (cliente só antes da retirada)   |
| PATCH  | `/rentals/{id}/finish`        | admin, gestor       | Finaliza locação ativa                                 |

### Criar gestor

```json
POST /api/v1/users
{
  "name": "Fulano",
  "email": "fulano@locar.com",
  "password": "Senha@12345",
  "password_confirmation": "Senha@12345",
  "role": "gestor"
}
```

### Cadastrar veículo

Use as rotas `/fipe` para descobrir o `fipe_code` e o `year_id`. Marca, modelo, ano, combustível, preço FIPE e diária são preenchidos automaticamente.

```json
POST /api/v1/vehicles
{
  "plate": "ABC1D23",
  "fipe_code": "001420-6",
  "year_id": "2017-5"
}
```

## Testes

```bash
php artisan test
```

Os testes usam SQLite em memória, então não precisam do Neon nem do `.env` preenchido. Nesse modo os testes de `tests/Feature/DatabaseObjectsTest.php` são pulados, porque functions, procedures e triggers só existem no PostgreSQL.

Para rodar tudo contra o PostgreSQL, use um banco descartável: os testes apagam e recriam as tabelas. Nunca aponte para o banco do Neon em uso.

```bash
docker run -d --rm --name locar-pg -e POSTGRES_PASSWORD=teste -e POSTGRES_DB=locar -p 55432:5432 postgres:18
DB_CONNECTION=pgsql DB_URL=postgresql://postgres:teste@127.0.0.1:55432/locar php artisan test
```

No PowerShell, defina as variáveis antes: `$env:DB_CONNECTION='pgsql'; $env:DB_URL='postgresql://postgres:teste@127.0.0.1:55432/locar'`.

Para rodar a mesma checagem do CI (lint, tipos e testes):

```bash
composer ci:check
```
