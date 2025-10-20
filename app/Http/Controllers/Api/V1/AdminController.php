<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Admin",
 *     description="API Endpoints for administration"
 * )
 */
class AdminController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/admin/health",
     *     summary="Health check endpoint",
     *     tags={"Admin"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="System health status",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="healthy"),
     *             @OA\Property(property="timestamp", type="string", format="date-time"),
     *             @OA\Property(property="services", type="object")
     *         )
     *     )
     * )
     */
    public function health(): JsonResponse
    {
        $result = AdminService::getHealthStatus();

        if (!$result['success']) {
            return response()->json([
                'status' => $result['status'],
                'timestamp' => $result['timestamp'],
                'error' => $result['error'],
            ], 500);
        }

        return response()->json([
            'status' => $result['status'],
            'timestamp' => $result['timestamp'],
            'services' => $result['services'],
            'version' => $result['version'],
        ]);
    }
}
