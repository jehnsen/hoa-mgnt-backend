<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Exceptions\DuplicateInvoicePeriodException;
use App\Exceptions\InvalidViolationTransitionException;
use App\Exceptions\InvoiceNotSettleableException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

/**
 * Centralised API exception formatter.
 *
 * All exceptions are mapped to a uniform JSON envelope so API consumers
 * receive consistent error shapes regardless of where the exception originates.
 *
 * Envelope schema:
 * {
 *   "success": false,
 *   "message": "Human-readable summary",
 *   "errors":  { ... }  // present only for validation failures
 * }
 */
final class Handler
{
    /**
     * Render any exception into a structured JSON response.
     * Registered in bootstrap/app.php via ->withExceptions().
     */
    public static function render(Throwable $e, Request $request): ?JsonResponse
    {
        // Validation errors – expose field-level messages
        if ($e instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'message' => 'The given data was invalid.',
                'errors'  => $e->errors(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Unauthenticated
        if ($e instanceof AuthenticationException) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Domain exceptions: map to 409 Conflict
        if ($e instanceof DuplicateInvoicePeriodException
            || $e instanceof InvalidViolationTransitionException
            || $e instanceof InvoiceNotSettleableException
        ) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_CONFLICT);
        }

        // Standard HTTP exceptions (404 NotFound, 403 Forbidden, etc.)
        if ($e instanceof HttpException) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'HTTP error.',
            ], $e->getStatusCode());
        }

        // Unhandled / server-side exception
        $message = app()->hasDebugModeEnabled()
            ? $e->getMessage()
            : 'An unexpected error occurred. Please try again later.';

        return response()->json([
            'success' => false,
            'message' => $message,
        ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}
