PROJECT_NAME=cdaem_ru

USER_ID=$(shell id -u)
GROUP_ID=$(shell id -g)

docker_compose = docker-compose -p ${PROJECT_NAME} -f ./docker/docker-compose.yml -f ./docker/docker-compose.override.yml

app_run = $(docker_compose) exec -T --user="${USER_ID}" app
node_run = $(docker_compose) run --user="${USER_ID}" --rm node
db_run = $(docker_compose) exec --user=root db


bootstrap:
	cp ./docker/docker-compose.override.yml.example ./docker/docker-compose.override.yml
	cp ./docker/.env.example ./docker/.env
	make build
	$(docker_compose) up -d
	make composer cmd="global require "fxp/composer-asset-plugin:^1.3.1""
	make composer cmd=install
	make php-init
	make dump-geo-load
	make php-yii cmd="migrate --interactive=0"
	make dump-import

start:
	$(docker_compose) up -d
	make composer cmd=install
	make php-yii cmd="migrate --interactive=0"

stop:
	$(docker_compose) down

restart:
	make stop
	make start

status:
	$(docker_compose) ps

build:
	$(docker_compose) build --build-arg LOCAL_ENV=dev --build-arg USER_ID=${USER_ID} --build-arg GROUP_ID=${GROUP_ID} --no-cache

composer:
    ifneq ($(cmd),)
		$(app_run) sh -c "composer $(cmd)"
    else
	    $(app_run) sh -c "composer update"
    endif

app_bash:
	$(app_run) sh

php-yii:
    ifneq ($(cmd),)
	    $(app_run) sh -c "php yii $(cmd)"
    else
	    $(app_run) sh -c "php yii"
    endif

dump-geo-load:
	$(db_run) sh -c "mysql -u cdaemru -p cdaemru --password='cdaemru' < /dumps/geo.sql"

dump-import:
	$(db_run) sh -c "mysql -u cdaemru -p cdaemru --password='cdaemru' < /dumps/articles.sql"
	$(db_run) sh -c "mysql -u cdaemru -p cdaemru --password='cdaemru' < /dumps/pages.sql"
	$(db_run) sh -c "mysql -u cdaemru -p cdaemru --password='cdaemru' < /dumps/seo_text.sql"

php-init:
	$(app_run) sh -c "php init"

pull:
	git status
	git pull
	make restart