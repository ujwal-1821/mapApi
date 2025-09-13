<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DistanceController extends Controller
{
       public function index()
    {
        $locations = Location::all();
        return view('distance.calculate', compact('locations'));
    }

 public function calculate(Request $request)
{
    $data = $request->validate([
        'from_id' => 'required|exists:locations,id',
        'to_id'   => 'required|exists:locations,id|different:from_id',
    ]);

    $from = Location::findOrFail($data['from_id']);
    $to   = Location::findOrFail($data['to_id']);

    $apiKey = config('services.ors.key');

    if (!$apiKey) {
        return response()->json(['error' => 'ORS API key not configured'], 500);
    }

    try {
      $response = Http::withHeaders([
    'Authorization' => $apiKey,
    'Content-Type' => 'application/json',
])->withOptions([
    'verify' => false, // disable SSL verification
])->post('https://api.openrouteservice.org/v2/directions/driving-car/geojson', [
    'coordinates' => [
        [(float) $from->longitude, (float) $from->latitude],
        [(float) $to->longitude,   (float) $to->latitude],
    ],
]);


        $response->throw(); // <-- will throw an exception if status >= 400

        $json = $response->json();
        $summary = $json['features'][0]['properties']['summary'] ?? null;

        return response()->json([
            'from' => ['id' => $from->id, 'name' => $from->name, 'lat' => $from->latitude, 'lon' => $from->longitude],
            'to' => ['id' => $to->id, 'name' => $to->name, 'lat' => $to->latitude, 'lon' => $to->longitude],
            'distance_km' => round(($summary['distance'] ?? 0)/1000, 2),
            'duration_s'  => $summary['duration'] ?? 0,
            'geojson' => $json,
        ]);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


}
