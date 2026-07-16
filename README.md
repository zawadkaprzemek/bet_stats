# bet_stats

Środowisko developerskie Docker dla projektu:

- backend: PHP 8.4 FPM + Symfony 7
- frontend: React + TypeScript + Vite + Tailwind CSS
- baza danych: MySQL 8.4

Aktualnie repo zawiera przygotowaną aplikację backendową Symfony 7.4 oraz infrastrukturę Docker. Frontend React i panel EasyAdmin nie są jeszcze instalowane.

## Struktura

```text
.
├── backend/
│   ├── Dockerfile
│   ├── composer.json
│   └── docker/php/conf.d/
├── docker/nginx/
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

4. Zainstaluj zależności backendu i przygotuj bazę:

```bash
make composer-install
make db-create
make schema-validate
```

5. Sprawdź endpoint health:

```bash
curl http://localhost:8080/api/health
```

6. Frontend jest dostępny pod:

```bash
http://localhost:5173
```

## Dostępne usługi

- `nginx` - serwer HTTP publikujący aplikację backend na `http://localhost:8080`
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
make composer-install
make db-create
make schema-validate
make health
make backend-console CMD="cache:clear"
```

## Uwagi

- `backend` montuje katalog `./backend` do `/var/www/app`
- `nginx` kieruje ruch HTTP do `backend:9000`
- `frontend` montuje katalog `./frontend` do `/app`
- frontend korzysta z `VITE_API_URL` i domyślnie odpytuje backend pod `http://localhost:8080`
- MySQL jest publikowany domyślnie na porcie `3307`, żeby nie kolidować z innymi lokalnymi instancjami na `3306`
- hostname `db` działa tylko wewnątrz sieci Docker; komendy `php bin/console` uruchamiaj przez `docker compose exec backend ...` albo targety `make`
- `make health` sprawdza publiczny endpoint przez hostowy port `APP_PORT` (domyślnie `8080`)
- frontend pozostaje aktywny nawet przed inicjalizacją aplikacji; po dodaniu `package.json` uruchomi `npm run dev -- --host 0.0.0.0`
- konfiguracja MySQL zawiera healthcheck i trwały wolumen danych
