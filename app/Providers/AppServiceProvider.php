<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\ViolationStatusUpdated;
use App\Listeners\HandleViolationStatusUpdate;
use App\Models\Amenity;
use App\Models\AmenityBooking;
use App\Models\Announcement;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\MaintenanceRequest;
use App\Models\Meeting;
use App\Models\Property;
use App\Models\Violation;
use App\Policies\AmenityBookingPolicy;
use App\Policies\AmenityPolicy;
use App\Policies\AnnouncementPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\MaintenancePolicy;
use App\Policies\MeetingPolicy;
use App\Policies\PropertyPolicy;
use App\Policies\ViolationPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // ── Event → Listener wiring ───────────────────────────────────────────
        // Laravel 11 removed the default EventServiceProvider; events are
        // registered here in AppServiceProvider::boot().
        Event::listen(ViolationStatusUpdated::class, HandleViolationStatusUpdate::class);

        // ── Model Policies ────────────────────────────────────────────────────
        Gate::policy(Invoice::class,           InvoicePolicy::class);
        Gate::policy(Property::class,          PropertyPolicy::class);
        Gate::policy(Violation::class,         ViolationPolicy::class);
        Gate::policy(Announcement::class,      AnnouncementPolicy::class);
        Gate::policy(MaintenanceRequest::class, MaintenancePolicy::class);
        Gate::policy(Amenity::class,           AmenityPolicy::class);
        Gate::policy(AmenityBooking::class,    AmenityBookingPolicy::class);
        Gate::policy(Document::class,          DocumentPolicy::class);
        Gate::policy(Meeting::class,           MeetingPolicy::class);
    }
}
