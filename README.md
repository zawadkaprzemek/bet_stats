# bet_stats

Środowisko developerskie Docker dla projektu:

- backend: PHP 8.4 FPM + Symfony 7
- frontend: React + TypeScript + Vite + Tailwind CSS
- baza danych: MySQL 8.4

Aktualnie repo zawiera wyłącznie infrastrukturę. Symfony, React i EasyAdmin nie są jeszcze instalowane.

## Struktura

```text
.
├── backend/
│   ├── Dockerfile
│   └── docker/php/conf.d/
├── frontend/
│   └── Dockerfile
├── compose.yaml
├── .env.example
├── .gitignore
└── Makefile
```

## Wymagania

- Docker
- Docker Compose Plugin (`docker compose`)

## Uruchomienie

1. Skopiuj plik środowiskowy:

```bash
cp .env.example .env
```

2. Zbuduj obrazy:

```bash
make build
```

3. Uruchom kontenery:

```bash
make up
```

## Dostępne usługi

- `backend` - PHP 8.4 FPM z Composerem i rozszerzeniami pod Symfony
- `frontend` - Node.js 22 przygotowany pod Vite/React/Tailwind
- `db` - MySQL 8.4 z persistent volume `mysql_data`

## Przydatne komendy

```bash
make up
make down
make build
make restart
make logs
make backend-shell
make frontend-shell
```

## Uwagi

- `backend` montuje katalog `./backend` do `/var/www/app`
- `frontend` montuje katalog `./frontend` do `/app`
- MySQL jest publikowany domyślnie na porcie `3307`, żeby nie kolidować z innymi lokalnymi instancjami na `3306`
- frontend pozostaje aktywny nawet przed inicjalizacją aplikacji; po dodaniu `package.json` uruchomi `npm run dev -- --host 0.0.0.0`
- konfiguracja MySQL zawiera healthcheck i trwały wolumen danych
