# bet_stats Documentation

## Project Overview

`bet_stats` is a betting statistics application built as a split backend and frontend system.

The backend exposes a REST API and an administration panel. The frontend consumes the API and provides the user-facing application for authentication, dashboard access, and future betting workflows.

At the current stage, the repository already includes:

- Symfony backend with session-based authentication
- React frontend with protected routes
- EasyAdmin administration panel
- Docker Compose local environment
- initial domain modules for `Sport`, `Competition`, and `User`

## Stack

### Backend

- Symfony 7
- PHP 8.4
- Doctrine ORM
- Doctrine Migrations
- EasyAdmin

### Frontend

- React
- TypeScript
- Vite
- Tailwind CSS
- TanStack Query
- React Hook Form
- Zod

### Database

- MySQL 8.4

### Infrastructure

- Docker
- Docker Compose

## Project Structure

```text
backend/    Symfony application, REST API, EasyAdmin, Doctrine entities
frontend/   React application, routing, API client, forms, data fetching
docker/     Nginx and MySQL container configuration
docs/       Project documentation
compose.yaml
Makefile
```

## Current Scope

The current implementation already provides:

- `POST /api/login`
- `POST /api/logout`
- `GET /api/me`
- `GET /api/health`
- `GET /api/sports`
- `GET /api/competitions?sport={id}`
- `POST /api/competitions`
- EasyAdmin under `/admin`

The frontend currently includes:

- login screen
- authenticated application shell
- dashboard starter page
- placeholder pages for future modules

## Running With Docker

1. Build containers:

```bash
make build
```

2. Start the environment:

```bash
make up
```

3. Install backend dependencies if needed:

```bash
make composer-install
```

4. Create the database:

```bash
make db-create
```

5. Validate the schema:

```bash
make schema-validate
```

6. Open the applications:

```text
Frontend: http://localhost:5173
Backend:  http://localhost:8080
Admin:    http://localhost:8080/admin
```

## Basic Commands

```bash
make up
make down
make build
make restart
make logs
make backend-shell
make frontend-shell
make backend-console CMD="cache:clear"
make composer-install
make db-create
make schema-validate
make health
make backend-test
make frontend-test
make test
```

## Related Documents

- [Architecture](./architecture.md)
- [Domain Rules](./domain-rules.md)
- [Roadmap](./roadmap.md)
