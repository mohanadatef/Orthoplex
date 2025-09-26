# Run once: set up environment on Windows (assuming WSL2 + Docker Desktop)
Write-Host "Starting Windows setup..."

if (-not (Test-Path .env)) { Copy-Item .env.example .env }

docker-compose up -d --build
Start-Sleep -s 8

docker exec laravel_app php artisan key:generate || true
docker exec laravel_app php artisan jwt:secret --force || true
docker exec laravel_app composer install || true
docker exec laravel_app php artisan migrate --force || true
docker exec laravel_app php artisan db:seed --class=PermissionSeeder || true
Write-Host "Setup complete. Containers up and migrations seeded."
