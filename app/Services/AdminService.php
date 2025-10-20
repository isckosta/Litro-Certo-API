<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AdminService
{
    /**
     * Get system health status.
     */
    public function getHealthStatus(): array
    {
        try {
            $services = [
                'database' => $this->checkDatabase(),
                'cache' => $this->checkCache(),
            ];

            $allHealthy = !in_array(false, array_column($services, 'healthy'));

            return [
                'success' => true,
                'status' => $allHealthy ? 'healthy' : 'unhealthy',
                'timestamp' => now()->toISOString(),
                'services' => $services,
                'version' => config('app.version', '1.0.0'),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'status' => 'error',
                'timestamp' => now()->toISOString(),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check database connection.
     */
    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();

            return ['healthy' => true, 'message' => 'Database connection successful'];
        } catch (\Exception $e) {
            return ['healthy' => false, 'message' => 'Database connection failed'];
        }
    }

    /**
     * Check cache connection.
     */
    private function checkCache(): array
    {
        try {
            Cache::put('health_check', true, 10);
            $result = Cache::get('health_check');

            return ['healthy' => $result === true, 'message' => 'Cache working'];
        } catch (\Exception $e) {
            return ['healthy' => false, 'message' => 'Cache failed'];
        }
    }
}
