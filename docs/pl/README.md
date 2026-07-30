# Dokumentacja bet_stats

## Opis projektu

`bet_stats` to aplikacja do statystyk zakładów zbudowana jako system z rozdzielonym backendem i frontendem.

Backend udostępnia REST API oraz panel administracyjny. Frontend konsumuje API i dostarcza aplikację użytkownika do logowania, dostępu do dashboardu oraz przyszłych procesów związanych z zakładami.

Na obecnym etapie repozytorium zawiera już:

- backend Symfony z autoryzacją sesyjną
- frontend React z chronionymi trasami
- panel administracyjny EasyAdmin
- lokalne środowisko Docker Compose
- początkowe moduły domenowe `Sport`, `Competition` i `User`

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

### Baza danych

- MySQL 8.4

### Infrastruktura

- Docker
- Docker Compose

## Struktura projektu

```text
backend/    Aplikacja Symfony, REST API, EasyAdmin, encje Doctrine
frontend/   Aplikacja React, routing, klient API, formularze, pobieranie danych
docker/     Konfiguracja kontenerów Nginx i MySQL
docs/       Dokumentacja projektu
compose.yaml
Makefile
```

## Aktualny zakres

Obecna implementacja udostępnia już:

- `POST /api/login`
- `POST /api/logout`
- `GET /api/me`
- `GET /api/health`
- `GET /api/sports`
- `GET /api/competitions?sport={id}`
- `POST /api/competitions`
- EasyAdmin pod `/admin`

Frontend zawiera obecnie:

- ekran logowania
- shell aplikacji dla zalogowanego użytkownika
- startową stronę dashboardu
- placeholdery dla przyszłych modułów

## Uruchomienie w Dockerze

1. Zbuduj kontenery:

```bash
make build
```

2. Uruchom środowisko:

```bash
make up
```

3. W razie potrzeby zainstaluj zależności backendu:

```bash
make composer-install
```

4. Utwórz bazę danych:

```bash
make db-create
```

5. Zweryfikuj schemat:

```bash
make schema-validate
```

6. Otwórz aplikacje:

```text
Frontend: http://localhost:5173
Backend:  http://localhost:8080
Admin:    http://localhost:8080/admin
```

## Podstawowe komendy

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

## Powiązane dokumenty

- [Architektura](./architecture.md)
- [Zasady domenowe](./domain-rules.md)
- [Roadmapa](./roadmap.md)
