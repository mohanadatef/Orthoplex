# Laravel Docker Starter (skeleton)

This package is a skeleton project prepared with:
- Docker / docker-compose
- Laravel skeleton files (models, controllers, jobs, commands, migrations)
- JWT (tymon/jwt-auth), Spatie permissions, Swagger (L5-Swagger)
- Queue worker and cron container
- Webhooks, API Keys, GDPR export/delete request flow
- Localization (en/ar)
- Rate limiting placeholders

## Quick start
1. Copy `.env.example` to `.env` and set values (JWT_SECRET, DB_*)
2. Build & start containers:
   ```bash
   docker-compose build --no-cache
   docker-compose up -d
   ```
3. Enter app container:
   ```bash
   docker exec -it laravel_app bash
   ```
4. Inside container:
   ```bash
   composer install
   php artisan key:generate
   php artisan jwt:secret
   php artisan migrate --force
   php artisan db:seed --class=PermissionSeeder
   php artisan l5-swagger:generate || true
   ```
5. Use Postman to test endpoints under `routes/api.php`.

This is a skeleton — extend services, repositories, interfaces, tests, and Swagger annotations as needed.



Added: Full DTOs/Repositories/Services, Swagger annotations, PHPUnit config and tests (basic suite).
