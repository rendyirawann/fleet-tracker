<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrackingController extends Controller
{
    public function updateLocation(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        // Nanti di sini kita masukkan rumus Haversine untuk hitung jarak.
        // Untuk sekarang, kita catat dulu di log Laravel untuk memastikan koneksi lancar.
        Log::info('Lokasi User ' . auth()->user()->id . ' : ' . $request->lat . ', ' . $request->lng);

        return response()->json(['status' => 'success', 'message' => 'Titik kordinat diterima']);
    }
}