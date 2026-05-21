<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ViolationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| HOA Management System – API Routes  (v1)
|--------------------------------------------------------------------------
| All routes are versioned under /api/v1.  Auth-protected routes require a
| valid Sanctum token.  Authorization granularity is enforced by FormRequest
| ::authorize() (role gates) and Laravel Policies (model-level).
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function (): void {

    // ── Authentication ────────────────────────────────────────────────────────
    Route::prefix('auth')->group(function (): void {
        Route::post('login', [AuthController::class, 'login'])->name('auth.login');

        Route::middleware('auth:sanctum')->group(function (): void {
            Route::get('me',      [AuthController::class, 'me'])->name('auth.me');
            Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
        });
    });

    Route::middleware('auth:sanctum')->group(function (): void {

        // ── User Management (SuperAdmin only) ─────────────────────────────────
        Route::prefix('users')->group(function (): void {
            Route::get('/',         [UserController::class, 'index'])->name('users.index');
            Route::post('/',        [UserController::class, 'store'])->name('users.store');
            Route::get('{user}',    [UserController::class, 'show'])->name('users.show');
            Route::patch('{user}',  [UserController::class, 'update'])->name('users.update');
            Route::delete('{user}', [UserController::class, 'destroy'])->name('users.destroy');
        });

        // ── Property Management ───────────────────────────────────────────────
        Route::prefix('properties')->group(function (): void {
            Route::get('/',            [PropertyController::class, 'index'])->name('properties.index');
            Route::post('/',           [PropertyController::class, 'store'])->name('properties.store');
            Route::get('{property}',   [PropertyController::class, 'show'])->name('properties.show');
            Route::patch('{property}', [PropertyController::class, 'update'])->name('properties.update');

            // Resident assignment (pivot)
            Route::post('{property}/residents',             [PropertyController::class, 'assignResident'])
                 ->name('properties.residents.assign');
            Route::delete('{property}/residents/{user}',   [PropertyController::class, 'unassignResident'])
                 ->name('properties.residents.unassign');
        });

        // ── Financials ────────────────────────────────────────────────────────
        Route::prefix('financials')->group(function (): void {

            // Bulk invoice generation (must be before {uuid} catch-all)
            Route::post('invoices/bulk',            [InvoiceController::class, 'storeBulk'])
                 ->name('invoices.bulk');
            Route::post('invoices/apply-late-fees', [InvoiceController::class, 'applyLateFees'])
                 ->name('invoices.apply-late-fees');

            // Per-property invoice list
            Route::get('properties/{propertyId}/invoices', [InvoiceController::class, 'index'])
                 ->name('invoices.index');

            // Single-invoice operations
            Route::get('invoices/{uuid}',          [InvoiceController::class, 'show'])->name('invoices.show');
            Route::post('invoices',                [InvoiceController::class, 'store'])->name('invoices.store');
            Route::post('invoices/{uuid}/payments',[InvoiceController::class, 'processPayment'])
                 ->name('invoices.payments.store');
            Route::delete('invoices/{uuid}',       [InvoiceController::class, 'cancel'])->name('invoices.cancel');
        });

        // ── Violations ────────────────────────────────────────────────────────
        Route::prefix('violations')->group(function (): void {
            Route::get('/',                       [ViolationController::class, 'index'])->name('violations.index');
            Route::post('/',                      [ViolationController::class, 'store'])->name('violations.store');
            Route::get('{uuid}',                  [ViolationController::class, 'show'])->name('violations.show');
            Route::patch('{uuid}/status',         [ViolationController::class, 'updateStatus'])
                 ->name('violations.status.update');
            Route::post('{uuid}/evidence',        [ViolationController::class, 'appendEvidence'])
                 ->name('violations.evidence.append');
        });
    });
});
