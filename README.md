# Laravel Docker Starter – Full Setup

Skeleton project with modern Laravel 11 architecture:

- **Docker / docker-compose** (App, MySQL, Redis, Queue, Cron, Mailhog).
- **Laravel 11** (OOP / SOLID structure with Controllers, DTOs, Services, Repositories, Interfaces).
- **Auth**: JWT (tymon/jwt-auth), Email verification, 2FA (TOTP), Magic link.
- **RBAC**: Spatie permissions (roles: owner, admin, member, auditor).
- **API**: Swagger (L5-Swagger), API keys with HMAC & expiry/grace period.
- **Background jobs**: Queue workers, cron container.
- **Webhooks**: Sending with retries, DLQ, per-org HMAC signing.
- **GDPR**: Export (ZIP + one-time link), Delete request approval flow.
- **Analytics**: Top logins, inactive users, daily login aggregation.
- **Localization**: en/ar messages.
- **Rate limiting**: Login & delete-requests.
- **Tests**: PHPUnit feature tests for Auth, GDPR, 2FA, Webhooks, Analytics.

---

## 🚀 Quick start

1. Copy `.env.example` → `.env`  
   Update variables:  
   ```
   DB_HOST=mysql
   DB_DATABASE=orthoplex
   DB_USERNAME=root
   DB_PASSWORD=root
   JWT_SECRET=base64:...
   MAIL_HOST=mailhog
   ```

2. Build & start containers:
   ```bash
   docker-compose build --no-cache
   docker-compose up -d
   ```

3. Enter app container:
   ```bash
   docker-compose exec app bash
   ```

4. Inside container:
   ```bash
   composer install
   php artisan key:generate
   php artisan jwt:secret
   php artisan migrate --force
   php artisan db:seed --class=RolesAndPermissionsSeeder
   php artisan l5-swagger:generate
   ```

5. Verify services:
   - App → http://localhost  
   - Swagger docs → http://localhost/api/documentation  
   - Mailhog → http://localhost:8025  
   - MySQL → port 3306  
   - Redis → port 6379  

---

## 📂 Structure

```
app/
 ├── DTOs/
 ├── Http/
 │   ├── Controllers/Api/
 │   ├── Middleware/
 │   └── Requests/
 ├── Jobs/
 ├── Models/
 ├── Notifications/
 ├── Policies/
 ├── Providers/
 ├── Repositories/
 │   └── Contracts/
 └── Services/
routes/
 └── api.php
database/
 ├── migrations/
 └── seeders/
tests/
 ├── Feature/
 └── Unit/
```

---

## 🛠 Features & Commands

- **Queue worker**:  
  ```bash
  docker-compose exec queue php artisan queue:work
  ```

- **Cron** (scheduled tasks): configured in `app/Console/Kernel.php`.

- **Jobs**:  
  - `AggregateLoginDaily` (login_daily aggregation).  
  - `ExportUserDataJob` (GDPR export).  
  - `RetryFailedWebhooksJob` (with DLQ).  

- **Makefile** (shortcuts):  
  ```bash
  make up      # start containers
  make down    # stop containers
  make test    # run tests
  ```

---

## ✅ Checklist

- [x] Docker (app, mysql, redis, mailhog, queue, cron).  
- [x] Auth (JWT, email verification, 2FA, magic link).  
- [x] RBAC (roles/permissions).  
- [x] GDPR (export/delete with approval & notifications).  
- [x] API keys (create, rotate, HMAC, expiry).  
- [x] Webhooks (signing, retries, DLQ).  
- [x] Analytics (top logins, inactive users, login_daily job).  
- [x] Rate limiting (login, delete-requests).  
- [x] Tests (PHPUnit coverage).  
- [x] Swagger docs generated.  
