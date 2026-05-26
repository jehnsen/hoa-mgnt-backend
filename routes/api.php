<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AmenityBookingController;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\BoardController;
use App\Http\Controllers\Api\ClearanceController;
use App\Http\Controllers\Api\CommitteeController;
use App\Http\Controllers\Api\EmergencyContactController;
use App\Http\Controllers\Api\MeetingProxyController;
use App\Http\Controllers\Api\PetController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\ViolationAppealController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\AmenityController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\MaintenanceRequestController;
use App\Http\Controllers\Api\MeetingController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ViolationController;
use App\Http\Controllers\Api\VoteController;
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
            // static segments before {uuid} wildcard
            Route::get('repeat-offenders',        [ViolationController::class, 'repeatOffenders'])
                 ->name('violations.repeat-offenders');
            Route::get('{uuid}',                  [ViolationController::class, 'show'])->name('violations.show');
            Route::patch('{uuid}/status',         [ViolationController::class, 'updateStatus'])
                 ->name('violations.status.update');
            Route::post('{uuid}/evidence',        [ViolationController::class, 'appendEvidence'])
                 ->name('violations.evidence.append');
            Route::get('{uuid}/appeals',          [ViolationAppealController::class, 'index'])
                 ->name('violations.appeals.index');
            Route::post('{uuid}/appeal',          [ViolationAppealController::class, 'store'])
                 ->name('violations.appeals.store');
        });

        Route::get('appeals/{uuid}', [ViolationAppealController::class, 'show'])->name('appeals.show');

        // ── Announcements ─────────────────────────────────────────────────────
        Route::prefix('announcements')->group(function (): void {
            Route::get('/',            [AnnouncementController::class, 'index'])->name('announcements.index');
            Route::post('/',           [AnnouncementController::class, 'store'])->name('announcements.store');
            Route::get('{uuid}',       [AnnouncementController::class, 'show'])->name('announcements.show');
            Route::patch('{uuid}',     [AnnouncementController::class, 'update'])->name('announcements.update');
            Route::delete('{uuid}',    [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
        });

        // ── Maintenance Requests ──────────────────────────────────────────────
        Route::prefix('maintenance')->group(function (): void {
            Route::get('/',                    [MaintenanceRequestController::class, 'index'])->name('maintenance.index');
            Route::post('/',                   [MaintenanceRequestController::class, 'store'])->name('maintenance.store');
            Route::get('{uuid}',               [MaintenanceRequestController::class, 'show'])->name('maintenance.show');
            Route::patch('{uuid}/status',      [MaintenanceRequestController::class, 'updateStatus'])
                 ->name('maintenance.status.update');
        });

        // ── Amenities & Bookings ──────────────────────────────────────────────
        Route::prefix('amenities')->group(function (): void {
            Route::get('/',            [AmenityController::class, 'index'])->name('amenities.index');
            Route::post('/',           [AmenityController::class, 'store'])->name('amenities.store');
            Route::get('{uuid}',       [AmenityController::class, 'show'])->name('amenities.show');
            Route::patch('{uuid}',     [AmenityController::class, 'update'])->name('amenities.update');
        });

        Route::prefix('bookings')->group(function (): void {
            Route::get('/',                    [AmenityBookingController::class, 'index'])->name('bookings.index');
            Route::post('/',                   [AmenityBookingController::class, 'store'])->name('bookings.store');
            Route::get('{uuid}',               [AmenityBookingController::class, 'show'])->name('bookings.show');
            Route::patch('{uuid}/status',      [AmenityBookingController::class, 'updateStatus'])
                 ->name('bookings.status.update');
        });

        // ── Documents ─────────────────────────────────────────────────────────
        Route::prefix('documents')->group(function (): void {
            Route::get('/',                    [DocumentController::class, 'index'])->name('documents.index');
            Route::post('/',                   [DocumentController::class, 'store'])->name('documents.store');
            Route::get('{uuid}',               [DocumentController::class, 'show'])->name('documents.show');
            Route::get('{uuid}/download',      [DocumentController::class, 'download'])->name('documents.download');
            Route::patch('{uuid}',             [DocumentController::class, 'update'])->name('documents.update');
            Route::delete('{uuid}',            [DocumentController::class, 'destroy'])->name('documents.destroy');
        });

        // ── Meetings & Voting ─────────────────────────────────────────────────
        Route::prefix('meetings')->group(function (): void {
            Route::get('/',                        [MeetingController::class, 'index'])->name('meetings.index');
            Route::post('/',                       [MeetingController::class, 'store'])->name('meetings.store');
            Route::get('{uuid}',                   [MeetingController::class, 'show'])->name('meetings.show');
            Route::patch('{uuid}',                 [MeetingController::class, 'update'])->name('meetings.update');
            Route::post('{uuid}/votes',            [MeetingController::class, 'storeVote'])->name('meetings.votes.store');
        });

        Route::prefix('votes')->group(function (): void {
            Route::post('{uuid}/cast',   [VoteController::class, 'cast'])->name('votes.cast');
            Route::post('{uuid}/close',  [VoteController::class, 'close'])->name('votes.close');
            Route::get('{uuid}/tally',   [VoteController::class, 'tally'])->name('votes.tally');
        });

        // ── Reports & Summaries ───────────────────────────────────────────────
        Route::prefix('reports')->group(function (): void {
            Route::get('dashboard',    [ReportController::class, 'dashboard'])->name('reports.dashboard');
            Route::get('financial',    [ReportController::class, 'financial'])->name('reports.financial');
            Route::get('violations',   [ReportController::class, 'violations'])->name('reports.violations');
            Route::get('occupancy',    [ReportController::class, 'occupancy'])->name('reports.occupancy');
            Route::get('maintenance',  [ReportController::class, 'maintenance'])->name('reports.maintenance');
        });

        // ── Vendors ───────────────────────────────────────────────────────────
        Route::prefix('vendors')->group(function (): void {
            Route::get('/',                                          [VendorController::class, 'index'])->name('vendors.index');
            Route::post('/',                                         [VendorController::class, 'store'])->name('vendors.store');
            Route::get('{uuid}',                                     [VendorController::class, 'show'])->name('vendors.show');
            Route::patch('{uuid}',                                   [VendorController::class, 'update'])->name('vendors.update');
            Route::delete('{uuid}',                                  [VendorController::class, 'destroy'])->name('vendors.destroy');
            Route::post('{uuid}/maintenance/{maintenanceUuid}/assign', [VendorController::class, 'assignToMaintenance'])
                 ->name('vendors.maintenance.assign');
        });

        // ── Audit Logs (SuperAdmin only) ──────────────────────────────────────
        Route::prefix('audit-logs')->group(function (): void {
            Route::get('/', [AuditLogController::class, 'index'])->name('audit-logs.index');
            Route::get('{type}/{uuid}', [AuditLogController::class, 'forEntity'])->name('audit-logs.entity');
        });

        // ── Vehicles ──────────────────────────────────────────────────────────
        Route::prefix('vehicles')->group(function (): void {
            Route::get('/',            [VehicleController::class, 'index'])->name('vehicles.index');
            Route::post('/',           [VehicleController::class, 'store'])->name('vehicles.store');
            Route::get('{uuid}',       [VehicleController::class, 'show'])->name('vehicles.show');
            Route::patch('{uuid}',     [VehicleController::class, 'update'])->name('vehicles.update');
            Route::delete('{uuid}',    [VehicleController::class, 'destroy'])->name('vehicles.destroy');
        });

        Route::get('properties/{propertyUuid}/vehicles', [VehicleController::class, 'indexForProperty'])
             ->name('properties.vehicles.index');

        // ── Pets ──────────────────────────────────────────────────────────────
        Route::prefix('pets')->group(function (): void {
            Route::get('/',            [PetController::class, 'index'])->name('pets.index');
            Route::post('/',           [PetController::class, 'store'])->name('pets.store');
            Route::get('{uuid}',       [PetController::class, 'show'])->name('pets.show');
            Route::patch('{uuid}',     [PetController::class, 'update'])->name('pets.update');
            Route::delete('{uuid}',    [PetController::class, 'destroy'])->name('pets.destroy');
        });

        Route::get('properties/{propertyUuid}/pets', [PetController::class, 'indexForProperty'])
             ->name('properties.pets.index');

        // ── Emergency Contacts ────────────────────────────────────────────────
        Route::prefix('users/{userUuid}/emergency-contacts')->group(function (): void {
            Route::get('/',            [EmergencyContactController::class, 'indexForUser'])->name('emergency-contacts.index');
            Route::post('/',           [EmergencyContactController::class, 'store'])->name('emergency-contacts.store');
        });

        Route::prefix('emergency-contacts')->group(function (): void {
            Route::get('{uuid}',       [EmergencyContactController::class, 'show'])->name('emergency-contacts.show');
            Route::patch('{uuid}',     [EmergencyContactController::class, 'update'])->name('emergency-contacts.update');
            Route::delete('{uuid}',    [EmergencyContactController::class, 'destroy'])->name('emergency-contacts.destroy');
        });

        // ── HOA Clearances ────────────────────────────────────────────────────
        Route::prefix('clearances')->group(function (): void {
            Route::get('/',                    [ClearanceController::class, 'index'])->name('clearances.index');
            Route::post('/',                   [ClearanceController::class, 'store'])->name('clearances.store');
            Route::get('{uuid}',               [ClearanceController::class, 'show'])->name('clearances.show');
            Route::patch('{uuid}/status',      [ClearanceController::class, 'updateStatus'])->name('clearances.status.update');
        });

        // ── Board of Directors ────────────────────────────────────────────────
        Route::prefix('board-positions')->group(function (): void {
            Route::get('/',            [BoardController::class, 'index'])->name('board.index');
            Route::post('/',           [BoardController::class, 'store'])->name('board.store');
            Route::get('{uuid}',       [BoardController::class, 'show'])->name('board.show');
            Route::patch('{uuid}',     [BoardController::class, 'update'])->name('board.update');
            Route::patch('{uuid}/vacate', [BoardController::class, 'vacate'])->name('board.vacate');
            Route::delete('{uuid}',    [BoardController::class, 'destroy'])->name('board.destroy');
        });

        // ── Committees ────────────────────────────────────────────────────────
        Route::prefix('committees')->group(function (): void {
            Route::get('/',            [CommitteeController::class, 'index'])->name('committees.index');
            Route::post('/',           [CommitteeController::class, 'store'])->name('committees.store');
            Route::get('{uuid}',       [CommitteeController::class, 'show'])->name('committees.show');
            Route::patch('{uuid}',     [CommitteeController::class, 'update'])->name('committees.update');
            Route::delete('{uuid}',    [CommitteeController::class, 'destroy'])->name('committees.destroy');
            Route::post('{uuid}/members',              [CommitteeController::class, 'addMember'])
                 ->name('committees.members.add');
            Route::delete('{uuid}/members/{userUuid}', [CommitteeController::class, 'removeMember'])
                 ->name('committees.members.remove');
        });

        // ── Meeting Proxies ───────────────────────────────────────────────────
        Route::prefix('meetings/{meetingUuid}/proxies')->group(function (): void {
            Route::get('/',           [MeetingProxyController::class, 'index'])->name('meetings.proxies.index');
            Route::post('/',          [MeetingProxyController::class, 'store'])->name('meetings.proxies.store');
            Route::delete('{uuid}',   [MeetingProxyController::class, 'revoke'])->name('meetings.proxies.revoke');
        });

        // ── Resident Self-Service (Profile) ───────────────────────────────────
        Route::prefix('profile')->group(function (): void {
            Route::get('/',    [ProfileController::class, 'show'])->name('profile.show');
            Route::patch('/',  [ProfileController::class, 'update'])->name('profile.update');
        });
    });
});
