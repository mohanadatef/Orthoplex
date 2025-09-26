Write-Host "🚀 Setting up Laravel (Docker) ..."

docker-compose build --no-cache
docker-compose up -d

Write-Host "📦 Composer install..."
docker-compose exec app composer install

Write-Host "🔑 App key..."
docker-compose exec app php artisan key:generate

Write-Host "🗄️ Migrate & seed..."
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed --force

Write-Host "🧹 Optimize clear..."
docker-compose exec app php artisan optimize:clear

Write-Host "✅ Done. App: http://localhost | Mailhog: http://localhost:8025"
