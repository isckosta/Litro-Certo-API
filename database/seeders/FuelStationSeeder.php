<?php

namespace Database\Seeders;

use App\Models\FuelStation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FuelStationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stations = [
            [
                'name' => 'Posto Shell Paulista',
                'brand' => 'Shell',
                'cnpj' => '12.345.678/0001-90',
                'address' => 'Av. Paulista, 1000',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip_code' => '01310-100',
                'phone' => '1133334444',
                'latitude' => -23.561684,
                'longitude' => -46.655981,
                'services' => ['wifi', 'convenience_store', 'car_wash', 'air_pump'],
                'payment_methods' => ['credit_card', 'debit_card', 'pix', 'cash'],
                'opening_hours' => [
                    'monday' => '00:00-23:59',
                    'tuesday' => '00:00-23:59',
                    'wednesday' => '00:00-23:59',
                    'thursday' => '00:00-23:59',
                    'friday' => '00:00-23:59',
                    'saturday' => '00:00-23:59',
                    'sunday' => '00:00-23:59',
                ],
                'is_active' => true,
                'is_verified' => true,
                'rating_avg' => 4.5,
                'rating_count' => 120,
            ],
            [
                'name' => 'Posto Ipiranga Centro',
                'brand' => 'Ipiranga',
                'cnpj' => '98.765.432/0001-10',
                'address' => 'Rua da Consolação, 500',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip_code' => '01302-000',
                'phone' => '1122223333',
                'latitude' => -23.547054,
                'longitude' => -46.652348,
                'services' => ['convenience_store', 'atm'],
                'payment_methods' => ['credit_card', 'debit_card', 'pix'],
                'opening_hours' => [
                    'monday' => '06:00-22:00',
                    'tuesday' => '06:00-22:00',
                    'wednesday' => '06:00-22:00',
                    'thursday' => '06:00-22:00',
                    'friday' => '06:00-22:00',
                    'saturday' => '07:00-20:00',
                    'sunday' => '08:00-18:00',
                ],
                'is_active' => true,
                'is_verified' => true,
                'rating_avg' => 4.2,
                'rating_count' => 85,
            ],
            [
                'name' => 'Posto Petrobras Vila Mariana',
                'brand' => 'Petrobras',
                'cnpj' => '11.222.333/0001-44',
                'address' => 'Av. Domingos de Morais, 2000',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip_code' => '04036-000',
                'phone' => '1144445555',
                'latitude' => -23.587416,
                'longitude' => -46.638195,
                'services' => ['car_wash', 'oil_change', 'convenience_store'],
                'payment_methods' => ['credit_card', 'debit_card', 'pix', 'cash', 'app'],
                'opening_hours' => [
                    'monday' => '00:00-23:59',
                    'tuesday' => '00:00-23:59',
                    'wednesday' => '00:00-23:59',
                    'thursday' => '00:00-23:59',
                    'friday' => '00:00-23:59',
                    'saturday' => '00:00-23:59',
                    'sunday' => '00:00-23:59',
                ],
                'is_active' => true,
                'is_verified' => true,
                'rating_avg' => 4.7,
                'rating_count' => 200,
            ],
        ];

        foreach ($stations as $stationData) {
            $latitude = $stationData['latitude'];
            $longitude = $stationData['longitude'];

            // Remove lat/lng from data as we'll set location separately
            unset($stationData['latitude'], $stationData['longitude']);

            $station = FuelStation::create($stationData);

            // Set PostGIS location using raw SQL
            DB::statement('UPDATE fuel_stations SET location = ST_SetSRID(ST_MakePoint(?, ?), 4326), latitude = ?, longitude = ? WHERE id = ?',
                [$longitude, $latitude, $latitude, $longitude, $station->id]);
        }
    }
}
