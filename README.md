# Locar

API para locação de veículos, feita em Laravel. O sistema tem três perfis: admin, gestor e cliente.

O valor da diária de cada veículo é 0,15% do seu preço na tabela FIPE, consultado na [API da Invertexto](https://api.invertexto.com/api-tabela-fipe).

## Requisitos

- PHP 8.4 com a extensão `pdo_pgsql`
- Composer
- Node 22
- Banco PostgreSQL ([Neon](https://neon.tech))

## Instalação

```bash
composer setup
```

Depois, preencha no `.env`:

| Variável | Descrição |
|---|---|
| `DB_CONNECTION` | `pgsql` |
| `DB_URL` | Connection string do Neon. Use o endpoint direto, sem `-pooler` no host: as migrations falham pelo pooler. |
| `INVERTEXTO_TOKEN` | Token da API da Invertexto |

Rode as migrations:

```bash
php artisan migrate
```

Em desenvolvimento, `php artisan db:seed` cria o admin `admin@locar.com` com a senha `password`.

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

| Método | Rota | Acesso | Descrição |
|---|---|---|---|
| POST | `/login` | público | Retorna o token |
| POST | `/logout` | autenticado | Revoga o token atual |
| GET | `/me` | autenticado | Usuário logado |
| GET | `/users` | admin | Lista usuários |
| POST | `/users` | admin | Cria admin ou gestor |
| GET | `/users/{id}` | admin | Detalha usuário |
| DELETE | `/users/{id}` | admin | Remove usuário |
| GET | `/fipe/brands/{tipo}` | admin, gestor | Marcas (1 carro, 2 moto, 3 caminhão) |
| GET | `/fipe/models/{marca}` | admin, gestor | Modelos da marca |
| GET | `/fipe/years/{codigo_fipe}` | admin, gestor | Anos e preços do modelo |
| GET | `/vehicles` | admin, gestor | Lista veículos |
| POST | `/vehicles` | admin, gestor | Cadastra veículo |
| GET | `/vehicles/{id}` | admin, gestor | Detalha veículo |
| DELETE | `/vehicles/{id}` | admin, gestor | Remove veículo |

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

Para rodar a mesma checagem do CI (lint, tipos e testes):

```bash
composer ci:check
```
