<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;
protected $fillable = [
        'user_id',
        'license_plate',
        'name',
        'initial_km',
        'current_accumulated_km',
        'last_service_km', // <--- Tambahkan ini
        'service_interval_km',
    ];

    // Tambahkan relasi ini di bagian bawah
    public function services()
    {
        return $this->hasMany(VehicleService::class)->latest();
    }

    // (Opsional) Relasi balik ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs()
{
    return $this->hasMany(VehicleLog::class);
}
}