<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminTest extends TestCase
{
    public function test_health_check_returns_healthy_status(): void
    {
        $response = $this->getJson('/api/v1/admin/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'timestamp',
                'services' => [
                    'database' => ['healthy', 'message'],
                    'cache' => ['healthy', 'message'],
                ],
                'version',
            ])
            ->assertJson([
                'status' => 'healthy',
            ]);
    }

    public function test_health_check_includes_service_status(): void
    {
        $response = $this->getJson('/api/v1/admin/health');

        $services = $response->json('services');

        $this->assertTrue($services['database']['healthy']);
        $this->assertTrue($services['cache']['healthy']);
    }
}
