<?php

namespace Database\Seeders;

use App\Models\FuelPrice;
use App\Models\FuelStation;
use Illuminate\Database\Seeder;

class FuelPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stations = FuelStation::all();
        $fuelTypes = ['gasoline', 'ethanol', 'diesel', 'diesel_s10'];

        foreach ($stations as $station) {
            foreach ($fuelTypes as $fuelType) {
                $basePrice = match ($fuelType) {
                    'gasoline' => 5.89,
                    'ethanol' => 3.99,
                    'diesel' => 5.49,
                    'diesel_s10' => 5.79,
                    default => 5.00,
                };

                // Add some variation
                $price = $basePrice + (rand(-20, 20) / 100);

                FuelPrice::create([
                    'fuel_station_id' => $station->id,
                    'fuel_type' => $fuelType,
                    'price' => round($price, 3),
                    'valid_from' => now(),
                    'valid_until' => null,
                    'is_current' => true,
                    'reported_by' => 1, // Admin user
                ]);
            }
        }
    }
}
