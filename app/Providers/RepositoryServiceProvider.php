<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\ViolationRepositoryInterface;
use App\Repositories\Eloquent\InvoiceRepository;
use App\Repositories\Eloquent\PaymentRepository;
use App\Repositories\Eloquent\PropertyRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\ViolationRepository;
use App\Services\BillingService;
use App\Services\Contracts\BillingServiceInterface;
use App\Services\Contracts\PropertyServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Contracts\ViolationServiceInterface;
use App\Services\PropertyService;
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
        UserRepositoryInterface::class      => UserRepository::class,
        PropertyRepositoryInterface::class  => PropertyRepository::class,
        InvoiceRepositoryInterface::class   => InvoiceRepository::class,
        PaymentRepositoryInterface::class   => PaymentRepository::class,
        ViolationRepositoryInterface::class => ViolationRepository::class,

        // ── Services ─────────────────────────────────────────────────────────
        UserServiceInterface::class      => UserService::class,
        PropertyServiceInterface::class  => PropertyService::class,
        BillingServiceInterface::class   => BillingService::class,
        ViolationServiceInterface::class => ViolationService::class,
    ];

    public function register(): void
    {
        // All bindings are declared above and auto-registered by Laravel via
        // the $bindings property — no manual bind() calls needed.
    }
}
