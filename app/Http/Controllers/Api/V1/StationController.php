<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\FuelStation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Stations",
 *     description="API Endpoints for fuel stations"
 * )
 */
class StationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/stations/nearby",
     *     summary="Get nearby fuel stations",
     *     tags={"Stations"},
     *
     *     @OA\Parameter(
     *         name="latitude",
     *         in="query",
     *         required=true,
     *
     *         @OA\Schema(type="number", format="float", example=-23.561684)
     *     ),
     *
     *     @OA\Parameter(
     *         name="longitude",
     *         in="query",
     *         required=true,
     *
     *         @OA\Schema(type="number", format="float", example=-46.655981)
     *     ),
     *
     *     @OA\Parameter(
     *         name="radius",
     *         in="query",
     *
     *         @OA\Schema(type="number", format="float", example=10)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="List of nearby stations",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function nearby(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:'.config('app.max_search_radius_km', 50),
        ]);

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->radius ?? config('app.default_search_radius_km', 10);

        $stations = FuelStation::active()
            ->nearby($latitude, $longitude, $radius)
            ->with('currentPrices')
            ->get();

        return response()->json([
            'data' => $stations,
            'meta' => [
                'total' => $stations->count(),
                'radius_km' => $radius,
                'center' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ],
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/stations/{id}",
     *     summary="Get station details",
     *     tags={"Stations"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Station details",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(response=404, description="Station not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $station = FuelStation::with(['currentPrices', 'reviews' => function ($query) {
            $query->visible()->latest()->limit(10);
        }, 'promotions' => function ($query) {
            $query->active();
        }])->findOrFail($id);

        return response()->json(['data' => $station]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/stations/{id}/prices",
     *     summary="Get station prices",
     *     tags={"Stations"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Station prices",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *
     *     @OA\Response(response=404, description="Station not found")
     * )
     */
    public function prices(int $id): JsonResponse
    {
        $station = FuelStation::findOrFail($id);
        $prices = $station->currentPrices;

        return response()->json([
            'data' => $prices,
            'station' => [
                'id' => $station->id,
                'name' => $station->name,
                'brand' => $station->brand,
            ],
        ]);
    }
}
