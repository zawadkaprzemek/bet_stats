COMPOSE = docker compose
APP_PORT ?= 8080

.PHONY: up down build restart logs backend-shell frontend-shell backend-console composer-install db-create schema-validate health

up:
	$(COMPOSE) up -d

down:
	$(COMPOSE) down

build:
	$(COMPOSE) build

restart:
	$(COMPOSE) down
	$(COMPOSE) up -d

logs:
	$(COMPOSE) logs -f

backend-shell:
	$(COMPOSE) exec backend sh

frontend-shell:
	$(COMPOSE) exec frontend sh

backend-console:
	$(COMPOSE) exec backend php bin/console $(CMD)

composer-install:
	$(COMPOSE) exec backend composer install

db-create:
	$(COMPOSE) exec backend php bin/console doctrine:database:create --if-not-exists

schema-validate:
	$(COMPOSE) exec backend php bin/console doctrine:schema:validate

health:
	python3 -c "import urllib.request; print(urllib.request.urlopen('http://127.0.0.1:$(APP_PORT)/api/health').read().decode())"
