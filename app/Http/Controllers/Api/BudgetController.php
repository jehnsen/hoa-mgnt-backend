<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBudgetRequest;
use App\Http\Resources\BudgetResource;
use App\Services\Contracts\BudgetServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class BudgetController extends Controller
{
    public function __construct(
        private readonly BudgetServiceInterface $budgetService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $year = (int) request()->integer('year', now()->year);

        return BudgetResource::collection($this->budgetService->forYear($year));
    }

    public function summary(): JsonResponse
    {
        $year = (int) request()->integer('year', now()->year);

        return $this->successResponse($this->budgetService->summary($year), 'Budget summary');
    }

    public function store(StoreBudgetRequest $request): JsonResponse
    {
        return $this->successResponse(
            new BudgetResource($this->budgetService->save($request->safe()->all())),
            'Budget entry saved.',
            Response::HTTP_CREATED
        );
    }

    public function destroy(string $uuid): JsonResponse
    {
        $this->budgetService->delete($this->budgetService->findOrFail($uuid));

        return $this->successResponse(null, 'Budget entry deleted.');
    }

}
