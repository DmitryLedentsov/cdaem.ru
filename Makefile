#PROJECT_NAME = cdaem_ru

USER_ID = $(shell id -u)
GROUP_ID = $(shell id -g)

docker_compose_dev = docker-compose -p dev_cdaem_ru -f ./docker/docker-compose-dev.yml -f ./docker/docker-compose.override.yml

app_run := $(docker_compose_dev) exec -T --user="${USER_ID}" app
node_run := $(docker_compose_dev) run --user="${USER_ID}" --rm node
db_run := $(docker_compose_dev) exec --user=root db


#--- ASSETS ---#

npm:
    ifneq ($(cmd),)
	    $(node_run) sh -c "cd /app/assets && npm $(cmd)"
    else
	    $(node_run) sh -c "cd /app/assets && npm"
    endif


#--- DEV ---#

bootstrap:
	cp ./docker/docker-compose.override.yml.example ./docker/docker-compose.override.yml
	cp ./docker/.docker.env.example ./docker/.docker.env
	cat ./docker/hosts.sh | sudo /bin/sh
	make build
	$(docker_compose_dev) up -d
	make composer cmd="global require 'fxp/composer-asset-plugin:1.4.6'"
	make composer cmd=install
	make php-init
	make dump-dev
	make php-yii cmd="migrate --interactive=0"
	make cron-load
	make cron-start
	sleep 10
	make npm cmd='i'
	make npm-grunt cmd=install
	make npm-grunt cmd='run dev'
	make npm cmd='run dev'

start:
	$(docker_compose_dev) up -d
	make composer cmd=install
	make php-yii cmd="migrate --interactive=0"
	make cron-load
	make cron-start
	make npm cmd='i'
	make npm cmd='run dev'
	make npm-grunt cmd='run dev'
	make npm-grunt cmd='run prod'

stop:
	$(docker_compose_dev) down

restart:
	make stop
	make start

status:
	$(docker_compose_dev) ps

build: ## build all containers
    ifneq ($(cmd),)
	    $(docker_compose_dev) build --build-arg USER_ID=${USER_ID} --build-arg GROUP_ID=${GROUP_ID} --build-arg LOCAL_ENV=local $(cmd)
    else
	    $(docker_compose_dev) build --build-arg USER_ID=${USER_ID} --build-arg GROUP_ID=${GROUP_ID} --build-arg LOCAL_ENV=local
    endif

rebuild: ## rebuild all containers without cache
    ifneq ($(cmd),)
	    $(docker_compose_dev) build --no-cache --build-arg USER_ID=${USER_ID} --build-arg GROUP_ID=${GROUP_ID} --build-arg LOCAL_ENV=local $(cmd)
    else
	    $(docker_compose_dev) build --no-cache --build-arg USER_ID=${USER_ID} --build-arg GROUP_ID=${GROUP_ID} --build-arg LOCAL_ENV=local
    endif

composer:
    ifneq ($(cmd),)
		$(app_run) sh -c "composer $(cmd)"
    else
	    $(app_run) sh -c "composer update"
    endif

npm-grunt:
    ifneq ($(cmd),)
	    $(node_run) sh -c "cd /app/grunt && npm $(cmd)"
    else
	    $(node_run) sh -c "cd /app/grunt && npm"
    endif

app-bash:
	$(app_run) sh

php-yii:
    ifneq ($(cmd),)
	    $(app_run) sh -c "php yii $(cmd)"
    else
	    $(app_run) sh -c "php yii"
    endif

dump-dev:
	$(db_run) sh -c "mysql -u root -p cdaem.ru --password='cdaemru' < /dumps/dev.cdaem.ru.sql"

php-init:
	$(app_run) sh -c "php init"

pull:
	git status
	git pull
	make restart

cron-start:
	$(docker_compose_dev) exec -T --user=root app sh -c "crond"

cron-load:
	$(docker_compose_dev) exec -T --user=root app sh -c "crontab -u www-data /etc/cron"


#--- PROD ---#

docker_compose_prod = docker-compose -p cdaem_ru -f ./docker/docker-compose-prod.yml -f ./docker/docker-compose.override.yml

app_run_prod := $(docker_compose_prod) exec -T --user="${USER_ID}" app
node_run_prod := $(docker_compose_prod) run --user="${USER_ID}" --rm node

prod-bootstrap:
	cp ./docker/docker-compose.override.yml.example ./docker/docker-compose.override.yml
	cp ./docker/.env.example ./docker/.docker.env
	make prod-build
	$(docker_compose_prod) up -d
	make prod-composer cmd="global require "fxp/composer-asset-plugin:^1.3.1""
	make prod-composer cmd=install
	make prod-npm-grunt cmd=install
	make npm cmd='run prod'
	make prod-npm-grunt cmd='run prod'
	make prod-php-init
#	make prod-dump-geo-load
	make prod-php-yii cmd="migrate --interactive=0"
#	make prod-dump-import
	make prod-cron-load
	make prod-cron-start

prod-build:
	$(docker_compose_prod) build --build-arg LOCAL_ENV=prod --build-arg USER_ID=${USER_ID} --build-arg GROUP_ID=${GROUP_ID} --no-cache

prod-composer:
    ifneq ($(cmd),)
		$(app_run_prod) sh -c "composer $(cmd)"
    else
	    $(app_run_prod) sh -c "composer update"
    endif

prod-npm-grunt:
    ifneq ($(cmd),)
	    $(node_run_prod) sh -c "cd /app/grunt && npm $(cmd)"
    else
	    $(node_run_prod) sh -c "cd /app/grunt && npm"
    endif

prod-php-init:
	$(app_run_prod) sh -c "php init"

prod-php-yii:
    ifneq ($(cmd),)
	    $(app_run_prod) sh -c "php yii $(cmd)"
    else
	    $(app_run_prod) sh -c "php yii"
    endif

prod-cron-load:
	$(docker_compose_prod) exec -T --user=root app sh -c "crontab -u www-data /etc/cron"

prod-cron-start:
	$(docker_compose_prod) exec -T --user=root app sh -c "crond"

prod-start:
	$(docker_compose_prod) up -d
	make prod-composer cmd=install
	make prod-php-yii cmd="migrate --interactive=0"
	make npm cmd='run prod'
	make prod-npm-grunt cmd='run prod'
	make prod-cron-load
	make prod-cron-start

prod-stop:
	$(docker_compose_prod) down

prod-restart:
	make prod-stop
	make prod-start

prod-status:
	$(docker_compose_prod) ps

prod-app-bash:
	$(app_run_prod) sh

prod-pull:
	git status
	git pull
	make prod-restart
