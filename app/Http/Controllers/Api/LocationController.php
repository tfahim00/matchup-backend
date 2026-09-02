<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $lat = $request->query('lat');
        $lng = $request->query('lng');
        $radiusKm = $request->query('radius', 5);
        $perPage = (int) $request->query('per_page', 15);

        $query = Location::query();

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('address', 'like', "%{$q}%")
                    ->orWhere('city', 'like', "%{$q}%")
                    ->orWhere('district', 'like', "%{$q}%");
            });
        }

        if ($lat !== null && $lng !== null) {
            $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))";
            $query->select('*', DB::raw("{$haversine} AS distance"))
                  ->setBindings([$lat, $lng, $lat], 'select')
                  ->whereRaw("{$haversine} <= ?", [$lat, $lng, $lat, $radiusKm])
                  ->orderBy('distance');
        } else {
            $query->orderBy('name');
        }

        return response()->json([
            'success' => true,
            'data' => $query->paginate($perPage),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'city' => ['nullable', 'string'],
            'district' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        if ((array_key_exists('latitude', $data) && ! array_key_exists('longitude', $data)) || (! array_key_exists('latitude', $data) && array_key_exists('longitude', $data))) {
            return response()->json(['success' => false, 'message' => 'Both latitude and longitude must be provided together.'], 422);
        }

        $location = Location::create($data);

        return response()->json(['success' => true, 'data' => $location], 201);
    }
}
