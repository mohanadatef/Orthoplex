
# Orthoplex Laravel 11 Challenge (Enterprise-Grade Scaffold)

> High-quality Laravel 11 skeleton built for the Orthoplex challenge. Production-style structure, SOLID, services, repositories-ready, DTOs-ready, policies, jobs, and analytics tables.

## Quick Start

```bash
cp .env.example .env
docker compose up -d --build
docker compose up
# open http://localhost:8080
docker compose exec app bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php

```

## Tech

- Laravel 11, PHP 8.3, MySQL 8, Redis 7, Mailhog.
- JWT auth (tymon/jwt-auth), 2FA (google2fa), RBAC (spatie/permission).
- Cursor pagination, optimistic locking, analytics rollups (`login_events`, `login_daily`).
- OpenAPI spec: `docs/openapi.yaml`.

## Structure (Key)

```
app/
  Http/Controllers/...    # thin controllers
  Http/Requests/...       # Form Request validation
  Models/                 # Eloquent models
  Policies/               # Policies & gates
  Services/               # Business logic (Auth, Users, Analytics, GDPR, Webhooks, Orgs)
domain/                   # place repositories, DTOs and aggregates here
database/migrations       # orgs, users, analytics tables
docs/openapi.yaml         # Swagger/OpenAPI
routes/api.php
```

## Notes vs Challenge

- Email verification, magic link, 2FA, idempotency, brute-force protection: **scaffolded**; fill in adapters and mailers.
- RBAC with roles/permissions (owner, admin, member, auditor) supported via Spatie package.
- GDPR export/delete: queued jobs to implement under `app/Jobs` and `GDPRService`.
- Webhooks: sign with HMAC (per-org secret), deliver via queue + retry (DLQ via Horizon).
- Inbound org provisioning: `POST /api/orgs/provision` guarded with API key + signature.
- Login analytics: write `login_events` on login and nightly rollup job to `login_daily`. Endpoints provided.
- RSQL-like filters: implement later via a dedicated `QueryFilter` and safe whitelist.

## Eventual Consistency

- `login_events` is the source of truth; `login_daily` is derived nightly.
- For windows intersecting "today", compute `SUM(rollup) + COUNT(events_today)` to be exact.
- Conflicts handled by UPSERT on `(user_id, org_id, date)` with atomic increments.

## Testing

- Pest configured (dev). Add feature tests for auth, pagination, filters, analytics.
- Use SQLite memory for fast tests; MySQL for integration.

## Next Steps

- Implement JWT issuing/revocation; passwordless magic-link flow.
- Add rate limiters & login throttling; idempotency store.
- Implement invitations, approvals, audit logs, inbound signature verification.
- Jobs: `ExportUserData`, `RollupLoginDaily`, `DeliverWebhook`.
- Policies mapping to permissions: `users.read/update/delete/invite`, `analytics.read`.
- Exception handler for uniform error envelope.
- Sparse fieldsets & includes via request params.
- Add full OpenAPI docs and Swagger UI integration.
- Optional search via Scout/Meilisearch.


## Added in this revision

- **JWT issue/revoke (scaffold)** with config; plug into tymon/jwt-auth.
- **Idempotency** middleware + table; use `Idempotency-Key` header.
- **Login throttling / rate limits**: `auth` & `sensitive` buckets via RouteServiceProvider.
- **Invitations** (email-based tokens) + endpoints.
- **GDPR** export job + deletion approval table.
- **Outbound Webhooks** with HMAC signature, retries, and backoff.
- **RSQL filters** (safe subset) + **sparse fieldsets/includes** for `/api/users`.
- **Swagger UI (l5-swagger)** config + expanded OpenAPI.
- **Audit Log** table + middleware to log admin actions.
- **DTO + Repository** layering (Domain + App Repos).
- **Pest tests** (basic Feature tests scaffolding).


## Horizon & RBAC

- Horizon dashboard at `/horizon` (local only). Service `horizon` موجودة في docker-compose.
- Seeder للأدوار/الصلاحيات: 
  ```bash
  php artisan migrate --force
  php artisan db:seed --class=Database\Seeders\RolesSeeder
  ```
- ربط الصلاحيات بالـroutes عبر `can:*` وبالسياسات في `AuthServiceProvider`.

## Swagger Security

- مفعّل مخطط `bearerAuth` (JWT). أضف `Authorization: Bearer <token>` في الطلبات المحمية.


## Completed for v5

- Email verification flow (tokens table + mail + endpoint).
- Magic link login (token table + mail + consume endpoint).
- GDPR export now zipped with one-time download link (valid 3 days).
- Nightly analytics rollup job `RollupLoginDaily`.
- Inbound org provisioning: validates `X-Api-Key` and `X-Signature` (HMAC SHA-256).

### Cron / Scheduler
Add to `app/Console/Kernel.php` a schedule for `RollupLoginDaily`:
```php
$schedule->job(new \App\Jobs\RollupLoginDaily())->dailyAt('01:30');
```
Or dispatch via your orchestrator.


## Bonus Features (v6)
- **Search**: Laravel Scout + Meilisearch (`/api/search/users?q=`) — مقيّد على org.
- **Org API Keys**: إنشاء/إلغاء مفاتيح منظّمة مع scopes + Middleware `orgapikey` (مرن لإضافة مسارات partner).
- **Saga/Compensation**: `ProvisioningSaga` تستخدم في Inbound provisioning للتراجع الآمن عن الخطوات.
- **Rate-limit analytics**: `rate_counters` + Middleware `ratemetrics` + Endpoint `/api/analytics/rate`.
- **i18n**: لغتان en/ar + Middleware `setlocale` يعتمد على `Accept-Language`.
- **Load testing**: سكربت k6 بسيط في `tools/k6/login-and-list-users.js`.

### Meilisearch
شغّل Meilisearch (لوكال أو Docker) وعيّن:
```
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://meilisearch:7700
MEILISEARCH_KEY=null
```
ثم:
```
php artisan scout:import "App\Models\User"
```
