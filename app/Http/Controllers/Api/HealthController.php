<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

final class HealthController extends Controller
{
    public function check(): JsonResponse
    {
        $dbStatus = 'ok';

        try {
            DB::connection()->getPdo();
        } catch (\Throwable) {
            $dbStatus = 'error';
        }

        $healthy    = $dbStatus === 'ok';
        $httpStatus = $healthy ? 200 : 503;

        return response()->json([
            'status'    => $healthy ? 'healthy' : 'degraded',
            'timestamp' => now()->toIso8601String(),
            'services'  => [
                'database' => $dbStatus,
            ],
        ], $httpStatus);
    }
}
