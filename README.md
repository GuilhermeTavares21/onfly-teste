const readme = `
# Onfly - Gerenciador de Pedidos

Este é um projeto full-stack de gerenciamento de pedidos, construído com Laravel (Backend) e Vue.js/Vuetify (Frontend), totalmente containerizado com Docker.

## Principais Funcionalidades

* **Autenticação Completa:** Registro e Login via API com JWT (JSON Web Token).
* **Controle de Acesso (ACL):**
  * **Usuário Admin:** Visualiza todos os pedidos, filtra por usuário e pode alterar o status.
  * **Usuário Comum:** Visualiza e cria apenas seus próprios pedidos.
* **Gerenciamento de Pedidos:** CRUD completo de pedidos de viagem (destino, datas).
* **Notificações por Email:** Disparo automático de emails (via Mailtrap) para o usuário quando o status de seu pedido é alterado por um admin.
* **Dashboard Reativo:** Tabela de pedidos com filtros dinâmicos e feedback visual (loading) em tempo real.

## Tecnologias Utilizadas

| | **Backend (API)** | **Frontend (Cliente)** | 
| :--- | :--- | :--- |
| | PHP 8.2 | Vue 3 (Composition API) | 
| | Laravel 12 | Vuetify 3 | 
| | MySQL | Pinia (Gerenciamento de Estado) | 
| | JWT (tymon/jwt-auth) | Vue Router | 
| | Mailtrap (Testes de Email) | Vite (Build Tool) | 
| | Docker & Docker Compose | Axios | 

## Pré-requisitos

Antes de começar, garanta que você tenha as seguintes ferramentas instaladas:

* [Docker](https://www.docker.com/get-started)
* [Docker Compose](https://docs.docker.com/compose/install/)
* [Node.js](https://nodejs.org/en/) (v20 ou superior)
* NPM (geralmente incluído no Node.js)

## 🚀 Começando (Rodando o Projeto)

Siga os passos abaixo para configurar e rodar a aplicação completa (Backend e Frontend).

### 1. Clonar o Repositório

```bash
git clone <repo-url>
```

### 2. Configuração do Backend (Docker)

O backend roda inteiramente dentro de containers Docker.

```bash
cd onfly-teste/backend
cd onfly-teste/backend
```

**a. Configurar `.env`**

Copie o arquivo de exemplo.

```bash
cp .env.example .env
```

Abra o arquivo `.env` e configure suas credenciais do Mailtrap:

```ini
DB_HOST=db
DB_DATABASE=onfly_db
DB_USERNAME=laravel
DB_PASSWORD=laravel

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=<SEU_USUARIO_MAILTRAP>
MAIL_PASSWORD=<SUA_SENHA_MAILTRAP>
MAIL_FROM_ADDRESS=hello@example.com
```

**b. Build e Up dos Containers**

Suba os serviços (PHP-FPM, MySQL, Nginx) em background:

```bash
docker-compose up -d --build
```

**c. Migrations e Seeders**

Entre no container da aplicação:

```bash
docker exec -it onfly-app bash
```

Dentro do container, rode as migrations e os seeders para popular o banco com usuários de teste:

```bash
php artisan migrate
php artisan db:seed --class=UserSeeder
```

> **Usuários de Teste Criados:**
>
> * **Admin:** `user@adm.test` (senha: `1234a56`)
>
> * **Comum:** `user@local.test` (senha: `123456`)

Saia do container (`exit`).

### 3. Configuração do JWT

O Laravel usa o pacote **tymon/jwt-auth** para autenticação via token JWT.

Esses passos já estão automatizados no container via entrypoint:

```bash
php artisan vendor:publish --provider="TymonJWTAuthProvidersLaravelServiceProvider"
php artisan jwt:secret
```

O comando `jwt:secret` gera uma chave única e a adiciona automaticamente no seu arquivo `.env`:
```env
JWT_SECRET=chave_gerada_automatica
```

O backend usa o guard `auth:api` configurado para JWT no arquivo `config/auth.php`.

### 4. Configuração do Frontend (Local)

O frontend rodará localmente em sua máquina, consumindo a API do Docker.

```bash
cd onfly-teste/frontend
```

**a. Instalar Dependências**

Utilize Node na versão 20 ou superior.

```bash
npm install
```

**b. Rodar o Servidor de Desenvolvimento**

```bash
npm run dev
```

### 5. Aplicação em Execução

Parabéns! A aplicação está pronta:

* **API Backend:** `http://localhost:8000`
* **Aplicação Frontend:** `http://localhost:5173` (ou a porta indicada pelo Vite)

## 🔐 Autenticação com JWT

* Após o login (`POST /api/login`), o backend retorna um **token JWT** e os dados do usuário.
* O frontend salva o token no **localStorage**.
* Todas as requisições autenticadas enviam o header:
  ```http
  Authorization: Bearer <seu_token_jwt>
  ```
* O logout apenas remove o token localmente (não há sessão no servidor).

## Endpoints Principais da API

### Autenticação

| **Método** | **Rota** | **Descrição** | 
| :--- | :--- | :--- |
| POST | `/api/register` | Criar usuário (aceita `is_admin`) | 
| POST | `/api/login` | Login e retorna token JWT | 
| GET | `/api/user` | (Autenticado) Dados do usuário | 
| POST | `/api/logout` | (Autenticado) Invalida o token local | 

### Pedidos (Autenticado)

| **Método** | **Rota** | **Descrição** | 
| :--- | :--- | :--- |
| GET | `/api/pedidos` | Lista pedidos (com filtros) | 
| POST | `/api/pedidos` | Criar novo pedido | 
| GET | `/api/pedidos/{id}` | Detalhar um pedido | 
| PATCH | `/api/pedidos/{id}/status` | **(Admin)** Atualizar status | 

## Estrutura dos Projetos

### Backend (Laravel)

* `app/Http/Controllers`: Recebe requests e retorna JSON.
* `app/Services`: Camada de regras de negócio.
* `app/Repositories`: Camada de acesso ao banco (Eloquent).
* `app/Http/Requests`: Validação de dados de entrada.
* `app/Mail`: Classes de Mailable para notificações.
* `routes/api.php`: Definição dos endpoints.
* `tests/`: Testes unitários e de feature (Pest).

### Frontend (Vue)

* `src/views`: Telas principais (Login.vue, Dashboard.vue).
* `src/components`: Componentes reutilizáveis (PedidosTable.vue, NavBar.vue).
* `src/stores`: Gerenciamento de estado (Pinia) para usuário e auth.
* `src/router`: Rotas do Vue Router.
* `src/axios.js`: Instância do Axios pré-configurada com a URL da API e token JWT.

## Observações e Dicas

* **Emails:** A funcionalidade de envio de emails (alteração de status) só funcionará se as credenciais do Mailtrap estiverem corretas no `.env` do backend.
* **JWT:** Se a aplicação for escalada em múltiplos containers, use o mesmo `JWT_SECRET` em todos.
* **Cache do Laravel:** Se você alterar o `.env` com o container já rodando, limpe o cache de configuração do Laravel:

  ```bash
  docker exec -it onfly-app php artisan config:clear
  ```

* **APP_KEY e JWT_SECRET:** São gerados automaticamente pelo entrypoint do container na primeira execução.
`;

export default readme;
