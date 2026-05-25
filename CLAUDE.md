# HOA Backend — Claude Code Guide

Springdale Village HOA Management System. **Backend API only** — the frontend is a separate Next.js project that consumes this service via bearer tokens.

---

## Tech Stack

- **Framework**: Laravel 13.8 / PHP 8.3+
- **Auth**: Laravel Sanctum (stateless personal access tokens)
- **Database**: MySQL in production; SQLite for local dev
- **Queue/Cache**: Database driver (upgrade to Redis before production)
- **Tests**: PHPUnit 12 via `php artisan test`
- **Code Style**: Laravel Pint (`./vendor/bin/pint`)

---

## Common Commands

```bash
# First-time setup
composer run setup

# Start all dev processes (server + queue + logs + vite)
composer run dev

# Run tests
composer run test
# or with filter
php artisan test --filter UserControllerTest

# Code style (auto-fix)
./vendor/bin/pint

# Fresh database with seed data
php artisan migrate:fresh --seed

# Seed only (no wipe)
php artisan db:seed

# Tail logs
php artisan pail
```

---

## Architecture

```
Request → FormRequest (validate + authorize) → Controller → Service → Repository → Eloquent Model
                                                         ↘ Policy (model-level auth)
```

**Three-layer separation is enforced:**

- **Controllers** (`app/Http/Controllers/Api/`) — thin; call one service method, return a Resource.
- **Services** (`app/Services/`) — all business logic; no Eloquent queries directly.
- **Repositories** (`app/Repositories/`) — all database access; return Eloquent models/collections.

Every interface→concrete binding lives in `app/Providers/RepositoryServiceProvider.php`. Adding a new entity means: Interface → Eloquent implementation → service interface → service class → bind both in the provider.

---

## Project Structure

```
app/
├── Enums/                  UserRole, InvoiceStatus, ViolationStatus, PaymentMethod
├── Events/                 ViolationStatusUpdated
├── Exceptions/             Handler.php + 4 domain exceptions
├── Http/
│   ├── Controllers/Api/    AuthController, UserController, PropertyController,
│   │                       InvoiceController, ViolationController
│   ├── Requests/Api/       13 FormRequest classes (validation + authorization)
│   └── Resources/          UserResource, PropertyResource, InvoiceResource,
│                           PaymentResource, ViolationResource
├── Listeners/              HandleViolationStatusUpdate
├── Models/                 User, Property, Invoice, Payment, Violation
├── Notifications/          ViolationIssuedNotification
├── Policies/               InvoicePolicy, ViolationPolicy, PropertyPolicy
├── Providers/              AppServiceProvider, RepositoryServiceProvider
├── Repositories/
│   ├── Contracts/          5 interfaces
│   └── Eloquent/           5 implementations
└── Services/
    ├── Contracts/          4 interfaces
    └── *.php               UserService, PropertyService, BillingService, ViolationService

database/
├── migrations/             11 migrations
└── seeders/                DatabaseSeeder + 6 entity seeders

routes/
└── api.php                 All routes under /api/v1
```

---

## API Overview

**Base URL**: `/api/v1`  
**Auth header**: `Authorization: Bearer <token>`

| Group | Prefix | Auth required | Role required |
|-------|--------|--------------|---------------|
| Auth | `/auth` | Login: no; me/logout: yes | any |
| Users | `/users` | yes | SuperAdmin |
| Properties | `/properties` | yes | SuperAdmin (write), any (read) |
| Financials | `/financials` | yes | Board+ (write), resident (own read) |
| Violations | `/violations` | yes | Board+ (write), any (read) |

**Uniform response envelope:**
```json
{ "success": true,  "data": { ... } }
{ "success": false, "message": "...", "errors": { ... } }
```

---

## Auth Flow

1. `POST /api/v1/auth/login` → returns `{ token, user }`
2. Include token in every subsequent request: `Authorization: Bearer <token>`
3. `POST /api/v1/auth/logout` → revokes the token

Tokens are created with `$user->createToken($deviceName)->plainTextToken`. No token expiry is set by default — configure `SANCTUM_TOKEN_EXPIRATION` in `.env` for production.

---

## Role System

Roles are defined in `App\Enums\UserRole`. Helper methods live on the `User` model:

```php
$user->isSuperAdmin()        // role === super_admin
$user->isBoardMember()       // role === board_member
$user->isResident()          // role === resident
$user->canManageFinancials()  // super_admin || board_member
$user->canManageViolations()  // super_admin || board_member
```

Authorization is enforced at two levels:
1. **FormRequest `authorize()`** — role gate (fails fast, returns 403)
2. **Laravel Policies** — model-level (e.g., a resident can only view their own invoices)

---

## Domain Exceptions

All domain exceptions extend `\RuntimeException` and are mapped to HTTP 409 in `Handler.php`:

| Exception | When thrown |
|-----------|-------------|
| `DuplicateInvoicePeriodException` | Invoice already exists for property+period_month |
| `InvalidViolationTransitionException` | Violation status FSM rejects the transition |
| `InvoiceNotSettleableException` | Payment attempted on a paid or cancelled invoice |
| `InvoiceNotCancellableException` | Cancel attempted on an already-paid invoice |

When adding new domain exceptions, add them to `Handler.php` so they render correctly.

---

## Violation State Machine

Valid transitions defined in `Violation::canTransitionTo()`:

```
draft → issued → appealed → paid → resolved
              ↘ resolved
```

`ViolationStatusUpdated` event fires on every status change. `HandleViolationStatusUpdate` listener sends the `ViolationIssuedNotification` email when status becomes `issued`.

---

## Key Conventions

- `declare(strict_types=1)` at the top of every PHP file — no exceptions.
- Public identifiers are **UUIDs**; internal foreign keys are integer IDs. Never expose integer IDs in API responses.
- Use **Enums** for every fixed-value column. Never use raw strings.
- Soft deletes on: `User`, `Property`, `Invoice`, `Violation`. `Payment` is hard-deleted only (financial immutability).
- Pagination: every index endpoint is paginated. Default `per_page` is 20.
- No direct Eloquent calls in controllers or form requests. Route through Service → Repository.

---

## Adding a New Entity

1. Create migration + model (with `HasUuids`, `SoftDeletes` if applicable).
2. Create `RepositoryInterface` in `Repositories/Contracts/`.
3. Create `EloquentRepository` in `Repositories/Eloquent/`.
4. Create `ServiceInterface` in `Services/Contracts/`.
5. Create `Service` class in `Services/`.
6. Bind both in `RepositoryServiceProvider::$bindings`.
7. Create FormRequest(s) in `Http/Requests/Api/`.
8. Create API Resource in `Http/Resources/`.
9. Create Policy in `Policies/` and register in `AppServiceProvider`.
10. Add routes to `routes/api.php`.
11. Write feature tests in `tests/Feature/`.

---

## Known Production Gaps

These are not implemented yet and must be addressed before a production launch:

- **Rate limiting** — no throttle middleware on any route.
- **CORS** — `config/cors.php` is unconfigured for the Next.js frontend origin.
- **Payment idempotency** — double-submit on `POST /financials/invoices/{uuid}/payments` can create duplicate payments.
- **No API tests** — `tests/Feature/` and `tests/Unit/` contain only placeholders.
- **Pagination max** — no enforced upper bound on `per_page`; large values can dump entire tables.
- **Password reset flow** — `password_reset_tokens` table exists but no controller/route.
- **Email verification** — `email_verified_at` column exists but no verification flow.
- **Audit trail** — no log of who changed what on invoices or violations.
- **Health check endpoint** — no `/health` route for load balancer probes.

---

## Environment Notes

For production, change these `.env` defaults:

```dotenv
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql       # not sqlite
QUEUE_CONNECTION=redis    # not database
CACHE_STORE=redis         # not database
MAIL_MAILER=smtp          # not log
SANCTUM_TOKEN_EXPIRATION=1440   # 24h, in minutes
```
