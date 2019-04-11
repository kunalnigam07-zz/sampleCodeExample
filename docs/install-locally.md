branched used
fitswarm: dev-liveswitch
fitswarm-live: master
fitswarm-liveswitch: simulcast-reconnect

cd fitswarm-liveswitch
npm install
npm run build
npm run copy

cd fitswarm-live
npm install
npm run build
npm run copy

stop sync
cd fitswarm
docker-compose -f docker-dev.yml build

comment #command: php artisan serve --host=0.0.0.0 --port=8000
docker-compose -f docker-dev.yml up

docker exec -it fitswarm-dev /bin/bash

mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
composer install
php artisan key:generate

will be save generated key in .env

Before start, DROP DATABASE IF EXISTS fitswarm; see MySql below
php artisan migrate -vvv
php artisan db:seed
exit

# Enable mysql driver
php -i | grep PDO
php ./tests/TestMySqlConnection.php


# MySql
. .env
mysql --host=$DB_HOST --port=$DB_PORT --user=$DB_USERNAME --password=$DB_PASSWORD $DB_DATABASE
DROP DATABASE IF EXISTS fitswarm;
SHOW DATABASES;
show tables;

# Used generator from https://phpdocker.io/generator


# Run locally,
fitswarm/public/static/web/js/system-check.js L:14
let SomDebugValues = [10000,10000];
