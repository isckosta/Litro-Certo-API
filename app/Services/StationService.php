<?php

namespace App\Services;

use App\Models\Station;
use Illuminate\Support\Facades\DB;

class StationService
{
    /**
     * Get nearby stations.
     */
    public static function getNearbyStations(float $lat, float $lng, float $radius = 10): array
    {
        try {
            $stations = Station::select([
                'id',
                'name',
                'address',
                'phone',
                'is_24h',
                'has_convenience_store',
                DB::raw("ST_Distance(location, ST_GeomFromText('POINT($lng $lat)', 4326)) / 1000 as distance")
            ])
            ->whereRaw("ST_DWithin(location, ST_GeomFromText('POINT($lng $lat)', 4326), ?)", [$radius * 1000])
            ->orderBy('distance')
            ->get();

            return [
                'success' => true,
                'data' => $stations->toArray(),
                'count' => $stations->count(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to get nearby stations',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get station details by ID.
     */
    public static function getStationById(int $id): array
    {
        try {
            $station = Station::with(['prices' => function ($query) {
                $query->latest('updated_at');
            }])->find($id);

            if (!$station) {
                return [
                    'success' => false,
                    'message' => 'Station not found',
                ];
            }

            return [
                'success' => true,
                'data' => $station->toArray(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to get station details',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get station prices by ID.
     */
    public static function getStationPrices(int $id): array
    {
        try {
            $station = Station::with(['prices' => function ($query) {
                $query->latest('updated_at');
            }])->find($id);

            if (!$station) {
                return [
                    'success' => false,
                    'message' => 'Station not found',
                ];
            }

            return [
                'success' => true,
                'data' => [
                    'station_id' => $station->id,
                    'station_name' => $station->name,
                    'prices' => $station->prices->toArray(),
                ],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to get station prices',
                'error' => $e->getMessage(),
            ];
        }
    }
}
