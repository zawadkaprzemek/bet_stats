# bet_stats

Środowisko developerskie Docker dla projektu:

- backend: PHP 8.4 FPM + Symfony 7
- frontend: React + TypeScript + Vite + Tailwind CSS
- baza danych: MySQL 8.4
- panel administracyjny: EasyAdmin pod `/admin`
- autoryzacja: sesja + cookie Symfony

Aktualnie repo zawiera przygotowaną aplikację backendową Symfony 7.4, frontend React oraz infrastrukturę Docker. EasyAdmin i sesyjne logowanie użytkowników są już skonfigurowane.

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
- opcjonalnie lokalny Node.js 22 do wsparcia edytora i pracy poza Dockerem

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

## Lokalny Node do edytora

Jeśli edytor zgłasza błędy TypeScript dla frontendu mimo poprawnego działania w Dockerze, doinstaluj lokalnie Node.js 22 i zależności w `frontend/`:

```bash
nvm use
rm -rf frontend/node_modules
cd frontend
npm install
```

Repo zawiera plik `.nvmrc` z wersją `22`.

## Frontend auth

- frontend posiada stronę `/login`
- prywatne trasy wymagają aktywnej sesji Symfony
- sesja jest sprawdzana przez `GET /api/me`
- klient API korzysta z `credentials: include`
- w development Vite proxy przekazuje `/api` do backendu, żeby sesyjne cookie działało bez dodatkowego CORS

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
make backend-test
make frontend-test
make test
make backend-console CMD="cache:clear"
```

Testy backendu używają osobnej bazy `MYSQL_TEST_DATABASE` (domyślnie `bet_stats_test`).
Jeśli wolumen MySQL został utworzony przed dodaniem tej konfiguracji, jednorazowo wykonaj:

```bash
docker compose exec db sh -lc 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS \`bet_stats_test\`; GRANT ALL PRIVILEGES ON \`bet_stats_test\`.* TO '\''bet_stats'\''@'\''%'\''; FLUSH PRIVILEGES;"'
```

## Użytkownicy i logowanie

- `POST /api/login` loguje użytkownika na sesji Symfony i zwraca dane użytkownika bez hasła
- `POST /api/logout` wylogowuje użytkownika
- `GET /api/me` zwraca aktualnie zalogowanego użytkownika
- `GET /api/sports` zwraca aktywne sporty posortowane po `sortOrder`, a potem alfabetycznie
- `GET /api/competitions?sport={id}` zwraca aktywne rozgrywki globalne i prywatne aktualnie zalogowanego użytkownika dla wybranego sportu
- `POST /api/competitions` tworzy prywatną rozgrywkę aktualnie zalogowanego użytkownika
- `/admin` wymaga `ROLE_ADMIN`
- pozostałe endpointy pod `/api` wymagają zalogowanego użytkownika, z wyjątkiem `/api/health` i `/api/login`

Tworzenie pierwszego administratora:

```bash
docker compose exec backend php bin/console doctrine:migrations:migrate --no-interaction
docker compose exec backend php bin/console app:user:create-admin admin@example.com strong-password
docker compose exec backend php bin/console doctrine:fixtures:load --group=sport --no-interaction
```

## Uwagi

- `backend` montuje katalog `./backend` do `/var/www/app`
- `nginx` kieruje ruch HTTP do `backend:9000`
- `frontend` montuje katalog `./frontend` do `/app`
- frontend korzysta z `VITE_API_URL` i domyślnie odpytuje backend przez `/api`
- panel administracyjny jest dostępny pod `http://localhost:8080/admin`
- assety EasyAdmin są publikowane do `backend/public/bundles/` przez `php bin/console assets:install public`
- MySQL jest publikowany domyślnie na porcie `3307`, żeby nie kolidować z innymi lokalnymi instancjami na `3306`
- hostname `db` działa tylko wewnątrz sieci Docker; komendy `php bin/console` uruchamiaj przez `docker compose exec backend ...` albo targety `make`
- `make health` sprawdza publiczny endpoint przez hostowy port `APP_PORT` (domyślnie `8080`)
- frontend pozostaje aktywny nawet przed inicjalizacją aplikacji; po dodaniu `package.json` uruchomi `npm run dev -- --host 0.0.0.0`
- konfiguracja MySQL zawiera healthcheck i trwały wolumen danych
