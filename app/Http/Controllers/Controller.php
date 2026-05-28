<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

abstract class Controller
{
    use AuthorizesRequests;

    protected function successResponse(
        mixed  $data,
        string $message = 'OK',
        int    $status  = Response::HTTP_OK,
    ): JsonResponse {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $status);
    }

    protected function perPage(int $default = 20, int $max = 100): int
    {
        return min(max(1, request()->integer('per_page', $default)), $max);
    }
}
