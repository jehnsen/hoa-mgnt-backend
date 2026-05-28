<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\AuditLogRepositoryInterface;
use App\Repositories\Contracts\AmenityBookingRepositoryInterface;
use App\Repositories\Contracts\AmenityRepositoryInterface;
use App\Repositories\Contracts\AnnouncementRepositoryInterface;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Repositories\Contracts\MaintenanceRequestRepositoryInterface;
use App\Repositories\Contracts\BoardPositionRepositoryInterface;
use App\Repositories\Contracts\CommitteeRepositoryInterface;
use App\Repositories\Contracts\MeetingProxyRepositoryInterface;
use App\Repositories\Contracts\MeetingRepositoryInterface;
use App\Repositories\Contracts\MeetingVoteRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\AmenityBlackoutRepositoryInterface;
use App\Repositories\Contracts\BudgetRepositoryInterface;
use App\Repositories\Contracts\ClearanceRepositoryInterface;
use App\Repositories\Contracts\ElectionNominationRepositoryInterface;
use App\Repositories\Contracts\ElectionRepositoryInterface;
use App\Repositories\Contracts\ElectionVoteRepositoryInterface;
use App\Repositories\Contracts\UtilityMeterReadingRepositoryInterface;
use App\Repositories\Contracts\VisitorPassRepositoryInterface;
use App\Repositories\Contracts\EmergencyContactRepositoryInterface;
use App\Repositories\Contracts\PetRepositoryInterface;
use App\Repositories\Contracts\RecurringMaintenanceRepositoryInterface;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use App\Repositories\Contracts\VendorProfileRepositoryInterface;
use App\Repositories\Contracts\ViolationAppealRepositoryInterface;
use App\Repositories\Contracts\ViolationRepositoryInterface;
use App\Repositories\Eloquent\AuditLogRepository;
use App\Repositories\Eloquent\AmenityBlackoutRepository;
use App\Repositories\Eloquent\AmenityBookingRepository;
use App\Repositories\Eloquent\BudgetRepository;
use App\Repositories\Eloquent\ElectionNominationRepository;
use App\Repositories\Eloquent\ElectionRepository;
use App\Repositories\Eloquent\ElectionVoteRepository;
use App\Repositories\Eloquent\UtilityMeterReadingRepository;
use App\Repositories\Eloquent\VisitorPassRepository;
use App\Repositories\Eloquent\AmenityRepository;
use App\Repositories\Eloquent\ClearanceRepository;
use App\Repositories\Eloquent\EmergencyContactRepository;
use App\Repositories\Eloquent\PetRepository;
use App\Repositories\Eloquent\RecurringMaintenanceRepository;
use App\Repositories\Eloquent\VendorProfileRepository;
use App\Repositories\Eloquent\AnnouncementRepository;
use App\Repositories\Eloquent\DocumentRepository;
use App\Repositories\Eloquent\InvoiceRepository;
use App\Repositories\Eloquent\MaintenanceRequestRepository;
use App\Repositories\Eloquent\BoardPositionRepository;
use App\Repositories\Eloquent\CommitteeRepository;
use App\Repositories\Eloquent\MeetingProxyRepository;
use App\Repositories\Eloquent\MeetingRepository;
use App\Repositories\Eloquent\MeetingVoteRepository;
use App\Repositories\Eloquent\PaymentRepository;
use App\Repositories\Eloquent\PropertyRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\VehicleRepository;
use App\Repositories\Eloquent\ViolationAppealRepository;
use App\Repositories\Eloquent\ViolationRepository;
use App\Services\AmenityBlackoutService;
use App\Services\AmenityBookingService;
use App\Services\AnnouncementService;
use App\Services\BudgetService;
use App\Services\DelinquencyService;
use App\Services\ElectionService;
use App\Services\RecurringMaintenanceService;
use App\Services\UtilityBillingService;
use App\Services\VisitorPassService;
use App\Services\BillingService;
use App\Services\ClearanceService;
use App\Services\Contracts\AmenityBlackoutServiceInterface;
use App\Services\Contracts\AmenityBookingServiceInterface;
use App\Services\Contracts\AnnouncementServiceInterface;
use App\Services\Contracts\BudgetServiceInterface;
use App\Services\Contracts\DelinquencyServiceInterface;
use App\Services\Contracts\ElectionServiceInterface;
use App\Services\Contracts\RecurringMaintenanceServiceInterface;
use App\Services\Contracts\UtilityBillingServiceInterface;
use App\Services\Contracts\VisitorPassServiceInterface;
use App\Services\Contracts\BillingServiceInterface;
use App\Services\Contracts\ClearanceServiceInterface;
use App\Services\Contracts\DocumentServiceInterface;
use App\Services\Contracts\EmergencyContactServiceInterface;
use App\Services\Contracts\MaintenanceServiceInterface;
use App\Services\Contracts\MeetingServiceInterface;
use App\Services\Contracts\PetServiceInterface;
use App\Services\Contracts\PropertyServiceInterface;
use App\Services\Contracts\ReportingServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Contracts\VehicleServiceInterface;
use App\Services\Contracts\VendorServiceInterface;
use App\Services\BoardService;
use App\Services\CommitteeService;
use App\Services\Contracts\BoardServiceInterface;
use App\Services\Contracts\CommitteeServiceInterface;
use App\Services\Contracts\MeetingProxyServiceInterface;
use App\Services\Contracts\ViolationAppealServiceInterface;
use App\Services\Contracts\ViolationServiceInterface;
use App\Services\MeetingProxyService;
use App\Services\DocumentService;
use App\Services\EmergencyContactService;
use App\Services\MaintenanceService;
use App\Services\MeetingService;
use App\Services\PetService;
use App\Services\PropertyService;
use App\Services\ReportingService;
use App\Services\UserService;
use App\Services\VehicleService;
use App\Services\VendorService;
use App\Services\ViolationAppealService;
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
        AuditLogRepositoryInterface::class               => AuditLogRepository::class,
        UserRepositoryInterface::class                  => UserRepository::class,
        PropertyRepositoryInterface::class              => PropertyRepository::class,
        InvoiceRepositoryInterface::class               => InvoiceRepository::class,
        PaymentRepositoryInterface::class               => PaymentRepository::class,
        ViolationRepositoryInterface::class             => ViolationRepository::class,
        ViolationAppealRepositoryInterface::class       => ViolationAppealRepository::class,
        AnnouncementRepositoryInterface::class          => AnnouncementRepository::class,
        MaintenanceRequestRepositoryInterface::class    => MaintenanceRequestRepository::class,
        AmenityRepositoryInterface::class               => AmenityRepository::class,
        AmenityBookingRepositoryInterface::class        => AmenityBookingRepository::class,
        DocumentRepositoryInterface::class              => DocumentRepository::class,
        MeetingRepositoryInterface::class               => MeetingRepository::class,
        MeetingVoteRepositoryInterface::class           => MeetingVoteRepository::class,
        MeetingProxyRepositoryInterface::class          => MeetingProxyRepository::class,
        BoardPositionRepositoryInterface::class         => BoardPositionRepository::class,
        CommitteeRepositoryInterface::class             => CommitteeRepository::class,
        VendorProfileRepositoryInterface::class         => VendorProfileRepository::class,
        VehicleRepositoryInterface::class               => VehicleRepository::class,
        PetRepositoryInterface::class                   => PetRepository::class,
        EmergencyContactRepositoryInterface::class      => EmergencyContactRepository::class,
        ClearanceRepositoryInterface::class              => ClearanceRepository::class,
        RecurringMaintenanceRepositoryInterface::class   => RecurringMaintenanceRepository::class,
        AmenityBlackoutRepositoryInterface::class        => AmenityBlackoutRepository::class,
        BudgetRepositoryInterface::class                 => BudgetRepository::class,
        UtilityMeterReadingRepositoryInterface::class    => UtilityMeterReadingRepository::class,
        VisitorPassRepositoryInterface::class            => VisitorPassRepository::class,
        ElectionRepositoryInterface::class               => ElectionRepository::class,
        ElectionNominationRepositoryInterface::class     => ElectionNominationRepository::class,
        ElectionVoteRepositoryInterface::class           => ElectionVoteRepository::class,

        // ── Services ─────────────────────────────────────────────────────────
        UserServiceInterface::class             => UserService::class,
        PropertyServiceInterface::class         => PropertyService::class,
        BillingServiceInterface::class          => BillingService::class,
        ViolationServiceInterface::class        => ViolationService::class,
        ViolationAppealServiceInterface::class  => ViolationAppealService::class,
        AnnouncementServiceInterface::class     => AnnouncementService::class,
        MaintenanceServiceInterface::class      => MaintenanceService::class,
        AmenityBookingServiceInterface::class   => AmenityBookingService::class,
        DocumentServiceInterface::class         => DocumentService::class,
        MeetingServiceInterface::class          => MeetingService::class,
        ReportingServiceInterface::class        => ReportingService::class,
        VendorServiceInterface::class           => VendorService::class,
        VehicleServiceInterface::class          => VehicleService::class,
        PetServiceInterface::class              => PetService::class,
        EmergencyContactServiceInterface::class => EmergencyContactService::class,
        ClearanceServiceInterface::class        => ClearanceService::class,
        BoardServiceInterface::class            => BoardService::class,
        CommitteeServiceInterface::class        => CommitteeService::class,
        MeetingProxyServiceInterface::class         => MeetingProxyService::class,
        RecurringMaintenanceServiceInterface::class => RecurringMaintenanceService::class,
        AmenityBlackoutServiceInterface::class      => AmenityBlackoutService::class,
        BudgetServiceInterface::class               => BudgetService::class,
        UtilityBillingServiceInterface::class       => UtilityBillingService::class,
        VisitorPassServiceInterface::class          => VisitorPassService::class,
        DelinquencyServiceInterface::class          => DelinquencyService::class,
        ElectionServiceInterface::class             => ElectionService::class,
    ];

    public function register(): void
    {
        // All bindings are declared above and auto-registered by Laravel via
        // the $bindings property — no manual bind() calls needed.
    }
}
