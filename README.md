# Springdale Village HOA Management System — Backend API

Production-grade RESTful API for the **Springdale Village Homeowners Association** (Biñan City, Laguna, Philippines), built with Laravel 11, MySQL 8, and Laravel Sanctum.

---

## Stack

| Layer       | Technology                          |
|-------------|-------------------------------------|
| Framework   | Laravel 11 (PHP 8.2+)               |
| Database    | MySQL 8.0+ with generated columns   |
| Auth        | Laravel Sanctum (token-based)       |
| Pattern     | Service → Repository (strict DI)    |
| Queue       | Database driver (notifications)     |

---

## Architecture

Controllers inject **Service Interfaces** only. Services inject **Repository Interfaces** only. Eloquent is never touched outside repository classes.

```
Request → FormRequest (auth + validation)
        → Controller (thin, no business logic)
        → ServiceInterface → concrete Service (domain rules, DB::transaction)
        → RepositoryInterface → Eloquent Repository
        → MySQL 8
```

All public models use **UUID** (`HasUuids`). Soft deletes on User, Property, Invoice, Violation. `total_amount` on invoices is a MySQL stored generated column (`base_amount + late_fee_amount`).

---

## Getting Started

### Prerequisites

- PHP 8.2+, Composer
- MySQL 8.0+
- XAMPP (or any local server) with the dev server on port 8000

### Installation

```bash
# 1. Install PHP dependencies
composer install

# 2. Copy environment file and configure
cp .env.example .env
# Edit DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 3. Generate application key
php artisan key:generate

# 4. Run migrations and seed sample data
php artisan migrate:fresh --seed

# 5. Start the development server
php artisan serve
```

The API is now available at `http://localhost:8000/api/v1`.

---

## Seeded Credentials

All seeded accounts use the password **`Springdale@2026`**.

| Role          | Email                               | Access                              |
|---------------|-------------------------------------|-------------------------------------|
| SuperAdmin    | admin@springdale-hoa.ph             | Full access                         |
| Board President | rodrigo.aquino@springdale-hoa.ph  | Financials + Violations             |
| Treasurer     | lourdes.santos@springdale-hoa.ph    | Financials + Violations             |
| Secretary     | jose.bautista@springdale-hoa.ph     | Violations                          |
| Resident      | carlos.reyes@gmail.com              | Own property invoices (read-only)   |

**20 properties** (Blocks 1–5, Lots 1–4 each), **10 violations**, and **6 months of invoice history** (Dec 2025 – May 2026) are seeded.

---

## API Reference

All endpoints are under `/api/v1` and require an `Authorization: Bearer {token}` header (except Login).

### Authentication

| Method | Endpoint              | Description                       |
|--------|-----------------------|-----------------------------------|
| POST   | `/auth/login`         | Get Sanctum token                 |
| GET    | `/auth/me`            | Authenticated user profile        |
| POST   | `/auth/logout`        | Revoke current token              |

### User Management *(SuperAdmin only for writes)*

| Method | Endpoint           | Description          |
|--------|--------------------|----------------------|
| GET    | `/users`           | List all users       |
| POST   | `/users`           | Create user          |
| GET    | `/users/{id}`      | Show user            |
| PATCH  | `/users/{id}`      | Update user          |
| DELETE | `/users/{id}`      | Soft-delete user     |

### Property Management *(SuperAdmin only for writes)*

| Method | Endpoint                                   | Description              |
|--------|--------------------------------------------|--------------------------|
| GET    | `/properties`                              | List all properties      |
| POST   | `/properties`                              | Create property          |
| GET    | `/properties/{id}`                         | Show property            |
| PATCH  | `/properties/{id}`                         | Update property          |
| POST   | `/properties/{id}/residents`               | Assign resident          |
| DELETE | `/properties/{id}/residents/{userId}`      | Unassign resident        |

### Financials *(board_member / super_admin for writes)*

| Method | Endpoint                                        | Description                              |
|--------|-------------------------------------------------|------------------------------------------|
| GET    | `/financials/properties/{id}/invoices`          | List invoices for a property             |
| GET    | `/financials/invoices/{uuid}`                   | Show single invoice                      |
| POST   | `/financials/invoices`                          | Generate invoice (single property)       |
| POST   | `/financials/invoices/bulk`                     | Generate invoices (all active properties)|
| DELETE | `/financials/invoices/{uuid}`                   | Cancel invoice                           |
| POST   | `/financials/invoices/{uuid}/payments`          | Record a payment                         |
| POST   | `/financials/invoices/apply-late-fees`          | Batch: mark overdue + apply late fees    |

### Violations *(board_member / super_admin for writes)*

| Method | Endpoint                              | Description                         |
|--------|---------------------------------------|-------------------------------------|
| GET    | `/violations`                         | List violations (filterable)        |
| POST   | `/violations`                         | Log new violation (Draft)           |
| GET    | `/violations/{uuid}`                  | Show violation                      |
| PATCH  | `/violations/{uuid}/status`           | Transition status (state machine)   |
| POST   | `/violations/{uuid}/evidence`         | Append geotagged evidence images    |

**Violation list filters:** `?status=issued`, `?property_id=3`, or combine both.

---

## Violation State Machine

```
Draft ──► Issued ──► Appealed ──► Resolved
                 └──► Paid ──────► Resolved
                 └──► Resolved (direct)
Appealed ────────────► Issued (re-open)
Resolved — terminal, no further transitions
```

On transition to **Issued**, a queued `ViolationIssuedNotification` (mail) is dispatched to all residents of the property.

---

## Artisan Commands

```bash
# Apply late-fee surcharge to all past-due pending invoices
php artisan hoa:apply-late-fees

# Generate monthly dues for all active properties
php artisan hoa:generate-monthly-dues 2026-08
```

Schedule both in `routes/console.php`:

```php
Schedule::command('hoa:apply-late-fees')->dailyAt('01:00');
Schedule::command('hoa:generate-monthly-dues', [now()->format('Y-m')])->monthlyOn(1, '06:00');
```

---

## Postman Collection

Import both files from the `docs/` folder:

```
docs/HOA-Management-API.postman_collection.json   ← 26 requests, 5 folders
docs/HOA-Management-Local.postman_environment.json ← pre-filled UUIDs + base URL
```

**Quick start:** Run **Login** first — the post-response script auto-saves the token to `{{token}}` so all other requests are immediately authorized.

---

## Key Design Decisions

| Decision | Rationale |
|---|---|
| `total_amount` as MySQL stored column | Guarantees arithmetic consistency at the DB layer; avoids PHP float drift |
| Evidence images as JSON on the violation row | Avoids joins for the common read path; GPS metadata travels with the record |
| Event fired **outside** `DB::transaction` | Ensures listeners only run after the commit; no ghost notifications on rollback |
| `applyLateFees` chunked per-invoice transactions | A failure on one invoice does not roll back the rest of the batch |
| `generateBulkMonthlyDues` catches `DuplicateInvoicePeriodException` | Makes the endpoint safe to call multiple times (idempotent) |
| Policies registered in `AppServiceProvider` | Laravel 11 removed the default `EventServiceProvider`; manual registration is explicit and auditable |

---

## License

MIT
