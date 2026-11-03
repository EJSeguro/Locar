# Locar

API para locação de veículos, feita em Laravel. O sistema tem três perfis: admin, gestor e cliente.

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

Os testes usam SQLite em memória, então não precisam do Neon nem do `.env` preenchido.

Para rodar a mesma checagem do CI (lint, tipos e testes):

```bash
composer ci:check
```
