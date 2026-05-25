<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\AmenityBookingRepositoryInterface;
use App\Repositories\Contracts\AmenityRepositoryInterface;
use App\Repositories\Contracts\AnnouncementRepositoryInterface;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Repositories\Contracts\MaintenanceRequestRepositoryInterface;
use App\Repositories\Contracts\MeetingRepositoryInterface;
use App\Repositories\Contracts\MeetingVoteRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\ViolationRepositoryInterface;
use App\Repositories\Eloquent\AmenityBookingRepository;
use App\Repositories\Eloquent\AmenityRepository;
use App\Repositories\Eloquent\AnnouncementRepository;
use App\Repositories\Eloquent\DocumentRepository;
use App\Repositories\Eloquent\InvoiceRepository;
use App\Repositories\Eloquent\MaintenanceRequestRepository;
use App\Repositories\Eloquent\MeetingRepository;
use App\Repositories\Eloquent\MeetingVoteRepository;
use App\Repositories\Eloquent\PaymentRepository;
use App\Repositories\Eloquent\PropertyRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\ViolationRepository;
use App\Services\AmenityBookingService;
use App\Services\AnnouncementService;
use App\Services\BillingService;
use App\Services\Contracts\AmenityBookingServiceInterface;
use App\Services\Contracts\AnnouncementServiceInterface;
use App\Services\Contracts\BillingServiceInterface;
use App\Services\Contracts\DocumentServiceInterface;
use App\Services\Contracts\MaintenanceServiceInterface;
use App\Services\Contracts\MeetingServiceInterface;
use App\Services\Contracts\PropertyServiceInterface;
use App\Services\Contracts\ReportingServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Contracts\ViolationServiceInterface;
use App\Services\DocumentService;
use App\Services\MaintenanceService;
use App\Services\MeetingService;
use App\Services\PropertyService;
use App\Services\ReportingService;
use App\Services\UserService;
use App\Services\ViolationService;
use Illuminate\Support\ServiceProvider;

/**
 * RepositoryServiceProvider
 *
 * Binds every Repository Interface to its Eloquent implementation and every
 * Service Interface to its concrete Service class inside Laravel's IoC container.
 *
 * Architecture decision: keeping bindings in a dedicated provider (rather than
 * AppServiceProvider) preserves separation of concerns and makes the binding
 * list easy to audit at a glance.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All repository interface → concrete class pairs.
     * Laravel resolves constructor dependencies automatically via reflection.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        // ── Repositories ──────────────────────────────────────────────────────
        UserRepositoryInterface::class                  => UserRepository::class,
        PropertyRepositoryInterface::class              => PropertyRepository::class,
        InvoiceRepositoryInterface::class               => InvoiceRepository::class,
        PaymentRepositoryInterface::class               => PaymentRepository::class,
        ViolationRepositoryInterface::class             => ViolationRepository::class,
        AnnouncementRepositoryInterface::class          => AnnouncementRepository::class,
        MaintenanceRequestRepositoryInterface::class    => MaintenanceRequestRepository::class,
        AmenityRepositoryInterface::class               => AmenityRepository::class,
        AmenityBookingRepositoryInterface::class        => AmenityBookingRepository::class,
        DocumentRepositoryInterface::class              => DocumentRepository::class,
        MeetingRepositoryInterface::class               => MeetingRepository::class,
        MeetingVoteRepositoryInterface::class           => MeetingVoteRepository::class,

        // ── Services ─────────────────────────────────────────────────────────
        UserServiceInterface::class             => UserService::class,
        PropertyServiceInterface::class         => PropertyService::class,
        BillingServiceInterface::class          => BillingService::class,
        ViolationServiceInterface::class        => ViolationService::class,
        AnnouncementServiceInterface::class     => AnnouncementService::class,
        MaintenanceServiceInterface::class      => MaintenanceService::class,
        AmenityBookingServiceInterface::class   => AmenityBookingService::class,
        DocumentServiceInterface::class         => DocumentService::class,
        MeetingServiceInterface::class          => MeetingService::class,
        ReportingServiceInterface::class        => ReportingService::class,
    ];

    public function register(): void
    {
        // All bindings are declared above and auto-registered by Laravel via
        // the $bindings property — no manual bind() calls needed.
    }
}
