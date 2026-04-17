<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TrackingController extends Controller
{
    public function updateLocation(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $user = auth()->user();
        
        // PERBAIKAN 1: Ambil kendaraan yang sedang di-set AKTIF
        $vehicle = $user->activeVehicle; 

        if (!$vehicle) {
            return response()->json(['error' => 'Tidak ada kendaraan aktif yang dipilih. Silakan pilih di menu Garasi.'], 400);
        }

        $newLat = $request->lat;
        $newLng = $request->lng;

        // Ambil koordinat sebelumnya dari Cache
        $cacheKey = 'last_location_user_' . $user->id;
        $lastLocation = Cache::get($cacheKey);

        $distanceKm = 0;

        if ($lastLocation) {
            // Hitung jarak menggunakan Haversine
            $distanceKm = $this->calculateHaversine($lastLocation['lat'], $lastLocation['lng'], $newLat, $newLng);
            
            // FILTER NOISE (GPS Bouncing):
            if ($distanceKm > 0.01 && $distanceKm < 5) {
                // PERBAIKAN 2: Blok logika cukup satu kali saja agar tidak double-counting
                $vehicle->current_accumulated_km += $distanceKm;
                $vehicle->save();

                // SIMPAN HISTORY KE LOG
                \App\Models\VehicleLog::create([
                    'vehicle_id' => $vehicle->id,
                    'distance_km' => $distanceKm
                ]);
            }
        }

        // Simpan posisi terbaru ke Cache untuk perhitungan berikutnya (kedaluwarsa 12 jam)
        Cache::put($cacheKey, ['lat' => $newLat, 'lng' => $newLng], now()->addHours(12));

        // Cek apakah sudah waktunya servis
        $needsService = $vehicle->current_accumulated_km >= $vehicle->service_interval_km;

        return response()->json([
            'status' => 'success',
            'added_km' => round($distanceKm, 4),
            'total_km' => round($vehicle->current_accumulated_km, 2),
            'needs_service' => $needsService
        ]);
    }

    /**
     * Formula Haversine untuk menghitung jarak antara 2 titik koordinat bumi
     */
    private function calculateHaversine($lat1, $lon1, $lat2, $lon2) 
    {
        $earthRadius = 6371; // Radius bumi dalam kilometer

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * asin(sqrt($a));
        
        return $earthRadius * $c; // Hasil dalam Kilometer
    }
}