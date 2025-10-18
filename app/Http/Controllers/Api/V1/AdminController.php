<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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
     *     @OA\Response(
     *         response=200,
     *         description="System health status",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="healthy"),
     *             @OA\Property(property="timestamp", type="string", format="date-time"),
     *             @OA\Property(property="services", type="object")
     *         )
     *     )
     * )
     */
    public function health(): JsonResponse
    {
        $services = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
        ];

        $allHealthy = !in_array(false, array_column($services, 'healthy'));

        return response()->json([
            'status' => $allHealthy ? 'healthy' : 'unhealthy',
            'timestamp' => now()->toIso8601String(),
            'services' => $services,
            'version' => config('app.version', '1.0.0'),
        ], $allHealthy ? 200 : 503);
    }

    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            return ['healthy' => true, 'message' => 'Database connection successful'];
        } catch (\Exception $e) {
            return ['healthy' => false, 'message' => 'Database connection failed'];
        }
    }

    private function checkCache(): array
    {
        try {
            cache()->put('health_check', true, 10);
            $result = cache()->get('health_check');
            return ['healthy' => $result === true, 'message' => 'Cache working'];
        } catch (\Exception $e) {
            return ['healthy' => false, 'message' => 'Cache failed'];
        }
    }
}
