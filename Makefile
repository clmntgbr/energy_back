#!/usr/bin/env bash

PROJECT_NAME = energy

DOCKER_COMPOSE = docker-compose -p $(PROJECT_NAME)

CONTAINER_NGINX = $$(docker container ls -f "name=$(PROJECT_NAME)_nginx" -q)
CONTAINER_PHP = $$(docker container ls -f "name=$(PROJECT_NAME)_php" -q)
CONTAINER_DB = $$(docker container ls -f "name=$(PROJECT_NAME)_database" -q)

NGINX = docker exec -ti $(CONTAINER_NGINX)
PHP = docker exec -ti $(CONTAINER_PHP)
DATABASE = docker exec -ti $(CONTAINER_DB)

COLOR_RESET			= \033[0m
COLOR_ERROR			= \033[31m
COLOR_INFO			= \033[32m
COLOR_COMMENT		= \033[33m
COLOR_TITLE_BLOCK	= \033[0;44m\033[37m

help:
	@printf "${COLOR_TITLE_BLOCK}Makefile${COLOR_RESET}\n"
	@printf "\n"
	@printf "${COLOR_COMMENT}Usage:${COLOR_RESET}\n"
	@printf " make [target]\n\n"
	@printf "${COLOR_COMMENT}Available targets:${COLOR_RESET}\n"
	@awk '/^[a-zA-Z\-\_0-9\@]+:/ { \
		helpLine = match(lastLine, /^## (.*)/); \
		helpCommand = substr($$1, 0, index($$1, ":")); \
		helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
		printf " ${COLOR_INFO}%-16s${COLOR_RESET} %s\n", helpCommand, helpMessage; \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)

## Kill all containers
kill:
	@$(DOCKER_COMPOSE) kill $(CONTAINER) || true

## Build containers
build:
	@$(DOCKER_COMPOSE) build --pull --no-cache

## Start containers
start:
	@$(DOCKER_COMPOSE) up -d
	@echo "admin is available here: 'https://back.traefik.me'"

## Stop containers
stop:
	@$(DOCKER_COMPOSE) down

restart: stop start

## Init project
init: install update init-bdd

init-bdd: drop create migrate migration migrate migrate bdd migration migrate

cache:
	$(PHP) rm -r var/cache

## Entering php shell
php:
	@$(DOCKER_COMPOSE) exec php sh

## Entering nginx shell
nginx:
	@$(DOCKER_COMPOSE) exec nginx sh

## Entering database shell
database:
	@$(DOCKER_COMPOSE) exec database sh

dump:
	rm -r -f public/data-dump.sql
	$(DATABASE) mysqldump -u random -prandom energy --ignore-table=energy.doctrine_migration_versions > public/data-dump.sql
	git add public/data-dump.sql

## Composer install
install:
	$(PHP) composer install

## Composer update
update:
	$(PHP) composer update

## Drop database
drop:
	$(PHP) bin/console doctrine:database:drop --if-exists --force

## Load fixtures
fixture:
	$(PHP) bin/console hautelook:fixtures:load --env=dev --no-interaction

## Create database
create:
	$(PHP) bin/console doctrine:database:create --if-not-exists

schema:
	$(PHP) bin/console doctrine:schema:update -f --complete

## Making migration file
migration:
	$(PHP) bin/console make:migration

## Applying migration
migrate:
	$(PHP) bin/console doctrine:migration:migrate --no-interaction

entity:
	$(PHP) bin/console make:entity

npm-install:
	$(PHP) npm install

npm-build:
	$(PHP) npm run build

bdd:
	$(PHP) bin/console bdd:import

ev-update:
	$(PHP) bin/console app:ev:update 4

gas-download:
	$(PHP) bin/console app:gas:download

gas-update:
	$(PHP) bin/console app:gas:update

status-update:
	$(PHP) bin/console app:status:update

status-anomaly:
	$(PHP) bin/console app:status:anomaly

## QA
cs-fixer:
	docker run --init -it --rm -v $(PWD):/project -w /project jakzal/phpqa php-cs-fixer fix ./src --rules=@Symfony

cs-fixer-dry:
	docker run --init -it --rm -v $(PWD):/project -w /project jakzal/phpqa php-cs-fixer fix ./src --rules=@Symfony --dry-run

phpcpd:
	docker run --init -it --rm -v $(PWD):/project -w /project jakzal/phpqa phpcpd ./src

phpstan:
	docker run --init -it --rm -v $(PWD):/project -w /project jakzal/phpqa phpstan analyse ./src --level=5

## Starting consumer
consume:
	$(PHP) bin/console messenger:consume async_priority_high async_priority_medium async_priority_low -vv

consume-high:
	$(PHP) bin/console messenger:consume async_priority_high -vv

consume-medium:
	$(PHP) bin/console messenger:consume async_priority_medium -vv

consume-low:
	$(PHP) bin/console messenger:consume async_priority_low -vv
