<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\InvoiceType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApplyLateFeesRequest;
use App\Http\Requests\Api\CancelInvoiceRequest;
use App\Http\Requests\Api\CreateInvoiceRequest;
use App\Http\Requests\Api\GenerateBulkDuesRequest;
use App\Http\Requests\Api\ProcessPaymentRequest;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\PaymentResource;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Services\Contracts\BillingServiceInterface;
use App\Services\Contracts\PropertyServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Slim controller: no business logic, no direct Eloquent access.
 * All domain work is delegated to BillingServiceInterface / PropertyServiceInterface.
 */
final class InvoiceController extends Controller
{
    public function __construct(
        private readonly BillingServiceInterface    $billingService,
        private readonly PropertyServiceInterface   $propertyService,
        private readonly InvoiceRepositoryInterface $invoiceRepository,
    ) {}

    public function index(string $propertyId): AnonymousResourceCollection
    {
        $property = $this->propertyService->findOrFail($propertyId);
        $this->authorize('viewAny', [\App\Models\Invoice::class, $property]);

        return InvoiceResource::collection(
            $this->invoiceRepository->paginateForProperty($property, $this->perPage())
        );
    }

    public function show(string $uuid): JsonResponse
    {
        $invoice = $this->invoiceRepository->findByUuid($uuid)
            ?? throw new NotFoundHttpException("Invoice [{$uuid}] not found.");

        $this->authorize('view', $invoice);

        return $this->successResponse(new InvoiceResource($invoice));
    }

    public function store(CreateInvoiceRequest $request): JsonResponse
    {
        $property = $this->propertyService->findOrFail($request->string('property_id')->toString());
        $type     = InvoiceType::from($request->input('type', InvoiceType::MonthlyDues->value));

        if ($type === InvoiceType::MonthlyDues) {
            $invoice = $this->billingService->generateMonthlyDues(
                $property,
                $request->string('period_month')->toString()
            );
        } else {
            $invoice = $this->billingService->generateCustomInvoice($property, $type, [
                'base_amount'  => $request->input('base_amount'),
                'description'  => $request->input('description'),
                'due_at'       => $request->input('due_at'),
                'period_month' => $request->input('period_month'),
            ]);
        }

        return $this->successResponse(
            new InvoiceResource($invoice->load('property')),
            'Invoice generated successfully.',
            Response::HTTP_CREATED
        );
    }

    public function storeBulk(GenerateBulkDuesRequest $request): JsonResponse
    {
        $result = $this->billingService->generateBulkMonthlyDues(
            $request->string('period_month')->toString()
        );

        return $this->successResponse(
            $result,
            "Generated {$result['created']} invoice(s) for period {$request->string('period_month')}."
        );
    }

    public function processPayment(ProcessPaymentRequest $request, string $uuid): JsonResponse
    {
        $invoice = $this->invoiceRepository->findByUuid($uuid)
            ?? throw new NotFoundHttpException("Invoice [{$uuid}] not found.");

        $this->authorize('processPayment', $invoice);

        $data = array_merge($request->validated(), [
            'received_by' => $request->input('received_by', $request->user()->id),
        ]);

        $payment = $this->billingService->processPayment($invoice, $data);

        return $this->successResponse(
            new PaymentResource($payment->load(['invoice', 'receiver'])),
            'Payment recorded successfully.',
            Response::HTTP_CREATED
        );
    }

    public function cancel(CancelInvoiceRequest $request, string $uuid): JsonResponse
    {
        $invoice = $this->invoiceRepository->findByUuid($uuid)
            ?? throw new NotFoundHttpException("Invoice [{$uuid}] not found.");

        $this->authorize('cancel', $invoice);

        $cancelled = $this->billingService->cancelInvoice($invoice);

        return $this->successResponse(
            new InvoiceResource($cancelled),
            'Invoice cancelled successfully.'
        );
    }

    public function applyLateFees(ApplyLateFeesRequest $request): JsonResponse
    {
        $count = $this->billingService->applyLateFees();

        return $this->successResponse(
            ['updated_invoices' => $count],
            "{$count} invoice(s) marked as overdue with late fees applied."
        );
    }

}
