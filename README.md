# Onfly - Backend Laravel

Este projeto é um backend em Laravel para gerenciamento de pedidos, incluindo autenticação via Sanctum, envio de emails de atualização de pedidos e estrutura em camadas (Controllers, Services, Repositories, Requests).

Tecnologias utilizadas:
- PHP 8.2
- Laravel 12
- MySQL (Docker)
- Sanctum para autenticação
- Mailtrap para envio de emails de teste
- Docker

---

## Rodando o projeto com Docker

### 1. Clonar o repositório

git clone <repo-url>
cd backend

### 2. Configurar `.env`

Copie o arquivo de exemplo:

cp .env.example .env

Edite as variáveis:

APP_NAME=Onfly
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=onfly_db
DB_USERNAME=laravel
DB_PASSWORD=laravel

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=<SEU_USUARIO_MAILTRAP>
MAIL_PASSWORD=<SUA_SENHA_MAILTRAP>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="Onfly"

> Troque `<SEU_USUARIO_MAILTRAP>` e `<SUA_SENHA_MAILTRAP>` pelos dados da sua conta Mailtrap.

### 3. Build e up do Docker

docker-compose up -d --build

Isso irá subir:
- PHP-FPM + Laravel
- MySQL

Mailtrap não precisa de container, apenas SMTP configurado no `.env`.

### 4. Rodar migrations e seeders

Entre no container do app:

docker exec -it onfly-app bash

Dentro do container, rode:

php artisan migrate

Para criar **usuários default** de teste (ADMIN e local):

php artisan db:seed --class=UserSeeder

Usuários criados:
- `user@adm.test` → ADMIN (senha: 123456)
- `user@local.test` → Usuário normal (senha: 123456)

> Esse seed pode ser rodado quantas vezes quiser, ele verifica se o usuário já existe.

### 5. Rodar a aplicação

php artisan serve --host=0.0.0.0

A aplicação estará disponível em: `http://localhost:8000`

---

## Endpoints principais

Autenticação:

| Método | Rota            | Descrição                  |
|--------|----------------|----------------------------|
| POST   | /api/register   | Criar usuário              |
| POST   | /api/login      | Login e retorna token      |
| GET    | /api/user       | Dados do usuário (Auth)   |
| POST   | /api/logout     | Logout                     |

Pedidos:

| Método | Rota                        | Descrição                       |
|--------|-----------------------------|---------------------------------|
| GET    | /api/pedidos                | Listar pedidos do usuário       |
| POST   | /api/pedidos                | Criar pedido                    |
| GET    | /api/pedidos/{id}           | Detalhar pedido                 |
| PATCH  | /api/pedidos/{id}/status    | Atualizar status (admin only)  |

---

## Envio de emails

- Sempre que um pedido for alterado, um email será enviado ao usuário responsável.
- Configuração feita via Mailtrap para testes.
- Layout do email usa cor base `#009efb` e assinatura `Onfly`.

---

## Testes

- Todos os testes unitários e feature foram criados.
- Para rodar os testes dentro do container:

docker exec -it onfly-app bash
php artisan test

Isso executa **todos os testes unitários e de feature** e mostra o resultado no terminal.

- Lembre-se de rodar o container com o banco e gerar migrations/seeders antes.

---

## Estrutura do projeto

- Controllers: recebimento de requests e retorno de respostas JSON.
- Services: regras de negócio.
- Repositories: acesso ao banco via Eloquent.
- Requests: validação de dados.
- Models: Eloquent Models com relacionamentos.
- Mail: templates de emails.
- Tests: testes unitários e feature tests.

---

## Dicas

- Gere a APP_KEY automaticamente ao build do Docker ou via Tinker:

php artisan key:generate

- Sempre limpe cache de config depois de alterar `.env`:

php artisan config:clear
php artisan cache:clear

- Para testar emails localmente, utilize Mailtrap e verifique Inbox > Messages.

---

## Pronto!

Agora o backend está funcional com:

- Autenticação via Sanctum
- CRUD de Pedidos
- Envio de emails de alteração
- Validações via Requests
- Logs detalhados de erros
- Usuários de teste (ADMIN e local)
- Testes automatizados
