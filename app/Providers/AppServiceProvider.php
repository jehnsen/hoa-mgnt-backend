<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\ViolationStatusUpdated;
use App\Listeners\HandleViolationStatusUpdate;
use App\Models\Amenity;
use App\Models\AmenityBlackout;
use App\Models\AmenityBooking;
use App\Models\Announcement;
use App\Models\BoardElection;
use App\Models\BoardPosition;
use App\Models\Clearance;
use App\Models\Committee;
use App\Models\Document;
use App\Models\EmergencyContact;
use App\Models\HoaBudget;
use App\Models\Invoice;
use App\Models\MaintenanceRequest;
use App\Models\Meeting;
use App\Models\Pet;
use App\Models\Property;
use App\Models\RecurringMaintenanceSchedule;
use App\Models\UtilityMeterReading;
use App\Models\Vehicle;
use App\Models\VendorProfile;
use App\Models\ViolationAppeal;
use App\Models\VisitorPass;
use App\Models\Violation;
use App\Policies\AmenityBlackoutPolicy;
use App\Policies\AmenityBookingPolicy;
use App\Policies\AmenityPolicy;
use App\Policies\AnnouncementPolicy;
use App\Policies\BoardElectionPolicy;
use App\Policies\BoardPositionPolicy;
use App\Policies\ClearancePolicy;
use App\Policies\CommitteePolicy;
use App\Policies\DocumentPolicy;
use App\Policies\EmergencyContactPolicy;
use App\Policies\HoaBudgetPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\MaintenancePolicy;
use App\Policies\MeetingPolicy;
use App\Policies\PetPolicy;
use App\Policies\PropertyPolicy;
use App\Policies\RecurringMaintenanceSchedulePolicy;
use App\Policies\UtilityMeterReadingPolicy;
use App\Policies\VehiclePolicy;
use App\Policies\VendorProfilePolicy;
use App\Policies\ViolationAppealPolicy;
use App\Policies\ViolationPolicy;
use App\Policies\VisitorPassPolicy;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // ── Email verification URL – use UUID, not integer primary key ───────
        // The default Laravel VerifyEmail notification embeds $user->getKey()
        // (integer ID) in the signed URL. We replace it with the UUID so no
        // internal IDs are ever exposed in emails or API responses.
        VerifyEmail::createUrlUsing(function (object $notifiable): string {
            return URL::temporarySignedRoute(
                'auth.email.verify',
                now()->addMinutes(60),
                [
                    'id'   => $notifiable->uuid,
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ],
            );
        });

        // ── Rate Limiters ─────────────────────────────────────────────────────
        // "api"   — authenticated routes: 60 req/min keyed by user ID (or IP for guests)
        // "login" — brute-force protection: 5 attempts/min per IP
        RateLimiter::for('api', function (Request $request): Limit {
            return Limit::perMinute(60)
                        ->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('login', function (Request $request): Limit {
            return Limit::perMinute(5)->by($request->ip());
        });

        // ── Event → Listener wiring ───────────────────────────────────────────
        // Laravel 11 removed the default EventServiceProvider; events are
        // registered here in AppServiceProvider::boot().
        Event::listen(ViolationStatusUpdated::class, HandleViolationStatusUpdate::class);

        // ── Model Policies ────────────────────────────────────────────────────
        // ── Pre-existing policies ─────────────────────────────────────────────
        Gate::policy(Invoice::class,            InvoicePolicy::class);
        Gate::policy(Property::class,           PropertyPolicy::class);
        Gate::policy(Violation::class,          ViolationPolicy::class);
        Gate::policy(Announcement::class,       AnnouncementPolicy::class);
        Gate::policy(MaintenanceRequest::class, MaintenancePolicy::class);
        Gate::policy(Amenity::class,            AmenityPolicy::class);
        Gate::policy(AmenityBooking::class,     AmenityBookingPolicy::class);
        Gate::policy(Document::class,           DocumentPolicy::class);
        Gate::policy(Meeting::class,            MeetingPolicy::class);

        // ── New module policies ───────────────────────────────────────────────
        Gate::policy(Vehicle::class,                    VehiclePolicy::class);
        Gate::policy(Pet::class,                        PetPolicy::class);
        Gate::policy(EmergencyContact::class,           EmergencyContactPolicy::class);
        Gate::policy(VendorProfile::class,              VendorProfilePolicy::class);
        Gate::policy(BoardPosition::class,              BoardPositionPolicy::class);
        Gate::policy(Committee::class,                  CommitteePolicy::class);
        Gate::policy(Clearance::class,                  ClearancePolicy::class);
        Gate::policy(ViolationAppeal::class,            ViolationAppealPolicy::class);
        Gate::policy(HoaBudget::class,                  HoaBudgetPolicy::class);
        Gate::policy(UtilityMeterReading::class,        UtilityMeterReadingPolicy::class);
        Gate::policy(VisitorPass::class,                VisitorPassPolicy::class);
        Gate::policy(BoardElection::class,              BoardElectionPolicy::class);
        Gate::policy(RecurringMaintenanceSchedule::class, RecurringMaintenanceSchedulePolicy::class);
        Gate::policy(AmenityBlackout::class,            AmenityBlackoutPolicy::class);
    }
}
