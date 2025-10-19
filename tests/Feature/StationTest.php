<?php

namespace Tests\Feature;

use App\Models\FuelStation;
use App\Models\FuelPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestStations();
    }

    private function createTestStations(): void
    {
        // Create test station near Paulista Avenue
        $station = FuelStation::create([
            'name' => 'Posto Teste Paulista',
            'brand' => 'Test',
            'cnpj' => '12.345.678/0001-90',
            'address' => 'Av. Paulista, 1000',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '01310-100',
            'is_active' => true,
            'is_verified' => true,
        ]);
        
        // Set location using PostGIS
        DB::statement("UPDATE fuel_stations SET location = ST_SetSRID(ST_MakePoint(?, ?), 4326), latitude = ?, longitude = ? WHERE id = ?", 
            [-46.655981, -23.561684, -23.561684, -46.655981, $station->id]);

        // Add prices
        FuelPrice::create([
            'fuel_station_id' => $station->id,
            'fuel_type' => 'gasoline',
            'price' => 5.89,
            'valid_from' => now(),
            'is_current' => true,
        ]);

        FuelPrice::create([
            'fuel_station_id' => $station->id,
            'fuel_type' => 'ethanol',
            'price' => 3.99,
            'valid_from' => now(),
            'is_current' => true,
        ]);
    }

    public function test_can_get_nearby_stations(): void
    {
        $response = $this->getJson('/api/v1/stations/nearby?latitude=-23.561684&longitude=-46.655981&radius=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'brand',
                        'address',
                        'city',
                        'state',
                        'latitude',
                        'longitude',
                        'distance_km',
                    ],
                ],
                'meta' => [
                    'total',
                    'radius_km',
                    'center',
                ],
            ]);

        $this->assertGreaterThan(0, count($response->json('data')));
    }

    public function test_nearby_stations_are_ordered_by_distance(): void
    {
        // Create another station farther away
        $station2 = FuelStation::create([
            'name' => 'Posto Teste Distante',
            'brand' => 'Test',
            'cnpj' => '98.765.432/0001-10',
            'address' => 'Rua Distante, 100',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '01000-000',
            'is_active' => true,
            'is_verified' => true,
        ]);
        
        DB::statement("UPDATE fuel_stations SET location = ST_SetSRID(ST_MakePoint(?, ?), 4326), latitude = ?, longitude = ? WHERE id = ?", 
            [-46.650, -23.550, -23.550, -46.650, $station2->id]);

        $response = $this->getJson('/api/v1/stations/nearby?latitude=-23.561684&longitude=-46.655981&radius=50');

        $response->assertStatus(200);

        $stations = $response->json('data');
        $this->assertGreaterThanOrEqual(2, count($stations));

        // Check that distances are in ascending order
        for ($i = 0; $i < count($stations) - 1; $i++) {
            $this->assertLessThanOrEqual(
                $stations[$i + 1]['distance_km'],
                $stations[$i]['distance_km']
            );
        }
    }

    public function test_can_get_station_details(): void
    {
        $station = FuelStation::first();

        $response = $this->getJson("/api/v1/stations/{$station->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'brand',
                    'address',
                    'current_prices',
                    'reviews',
                    'promotions',
                ],
            ]);
    }

    public function test_can_get_station_prices(): void
    {
        $station = FuelStation::first();

        $response = $this->getJson("/api/v1/stations/{$station->id}/prices");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'fuel_type',
                        'price',
                        'valid_from',
                        'is_current',
                    ],
                ],
                'station' => [
                    'id',
                    'name',
                    'brand',
                ],
            ]);

        $this->assertGreaterThan(0, count($response->json('data')));
    }

    public function test_nearby_stations_validation_fails_with_invalid_coordinates(): void
    {
        $response = $this->getJson('/api/v1/stations/nearby?latitude=invalid&longitude=-46.655981');

        $response->assertStatus(422);
    }

    public function test_returns_404_for_nonexistent_station(): void
    {
        $response = $this->getJson('/api/v1/stations/99999');

        $response->assertStatus(404);
    }
}
