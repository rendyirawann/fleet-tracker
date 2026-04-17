<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = auth()->user()->vehicles;
        $activeVehicleId = auth()->user()->active_vehicle_id;
        return view('vehicles.index', compact('vehicles', 'activeVehicleId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'license_plate' => 'required|unique:vehicles',
            'service_interval_km' => 'required|numeric'
        ]);

        auth()->user()->vehicles()->create($request->all());

        return back()->with('success', 'Kendaraan berhasil ditambahkan');
    }

    public function setActive($id)
    {
        auth()->user()->update(['active_vehicle_id' => $id]);
        return back()->with('success', 'Kendaraan aktif berhasil diubah');
    }

    public function deactivate()
    {
        auth()->user()->update(['active_vehicle_id' => null]);
        return back()->with('success', 'Sesi penggunaan kendaraan dihentikan.');
    }

    public function serviceNow(Request $request, $id)
    {
        $vehicle = auth()->user()->vehicles()->findOrFail($id);

        // 1. Simpan riwayat servis
        $vehicle->services()->create([
            'service_km' => $vehicle->current_accumulated_km,
            'notes' => 'Servis Rutin Berkala'
        ]);

        // 2. Perbarui penanda KM Servis Terakhir
        $vehicle->update([
            'last_service_km' => $vehicle->current_accumulated_km
        ]);

        return back()->with('success', 'Kendaraan berhasil diservis! Perhitungan interval selanjutnya telah direset otomatis.');
    }


}