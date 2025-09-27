up:
	docker-compose up -d --build

down:
	docker-compose down

rebuild:
	docker-compose down && docker-compose up -d --build

install:
	docker-compose exec app composer install

key:
	docker-compose exec app php artisan key:generate

migrate:
	docker-compose exec app php artisan migrate --force

seed:
	docker-compose exec app php artisan db:seed --force

cache-clear:
	docker-compose exec app php artisan optimize:clear

test:
	docker-compose exec app php artisan test

bash:
	docker-compose exec app bash

bootstrap:
	docker-compose build --no-cache
	docker-compose up -d app db redis mailhog queue cron
	docker-compose exec app composer install
	docker-compose exec app php artisan key:generate
	docker-compose exec app php artisan migrate --seed
	docker-compose exec app php artisan l5-swagger:generate
	docker-compose exec app php artisan storage:link
