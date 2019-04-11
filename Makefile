MAKEFLAGS += --silent

build:
	docker-compose build

install:
	docker-compose up -d
	docker-compose exec app /bin/bash /var/www/install.sh
	docker-compose stop

start:
	docker-compose up

stop:
	docker-compose down

.PHONY: build install start stop