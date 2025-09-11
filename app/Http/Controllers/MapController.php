<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MapController extends Controller
{
    public function index()
    {
        return view('map');
    }

    public function geocode(Request $request)
    {
        $apiKey = config('services.ors.key');

        if ($request->has('text')) {
            // Forward geocoding
            $request->validate(['text' => 'required|string']);
            $url = "https://api.openrouteservice.org/geocode/search?api_key={$apiKey}&text=" . urlencode($request->input('text')) . "&size=5";

            $context = stream_context_create([
                "http" => [
                    "header" => "Accept: application/geo+json\r\n"
                ]
            ]);

            $response = @file_get_contents($url, false, $context);

            if ($response === false) {
                return response()->json([
                    'error' => 'Forward geocoding failed',
                    'url'   => $url
                ], 500);
            }

            return response()->json(json_decode($response, true));
        } elseif ($request->has('lat') && $request->has('lon')) {
            // Reverse geocoding
            $request->validate([
                'lat' => 'required|numeric',
                'lon' => 'required|numeric'
            ]);
            $lat = $request->input('lat');
            $lon = $request->input('lon');
            $url = "https://api.openrouteservice.org/geocode/reverse?api_key={$apiKey}&point.lat={$lat}&point.lon={$lon}";

            $context = stream_context_create([
                "http" => [
                    "header" => "Accept: application/geo+json\r\n"
                ]
            ]);

            $response = @file_get_contents($url, false, $context);

            if ($response === false) {
                return response()->json([
                    'error' => 'Reverse geocoding failed',
                    'url'   => $url
                ], 500);
            }

            return response()->json(json_decode($response, true));
        } else {
            // Invalid request
            return response()->json([
                'error' => 'Either "text" (for search) or "lat/lon" (for reverse) is required'
            ], 400);
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $location = Location::create([
            'name' => $data['name'] ?? null,
            'latitude' => (float) $data['latitude'],
            'longitude' => (float) $data['longitude'],
        ]);

        return response()->json($location);
    }

    public function show()
    {
        $data['locations'] = Location::latest()->get();

        return view('show', $data);
    }
}
