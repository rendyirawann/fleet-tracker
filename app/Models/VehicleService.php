<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleService extends Model
{
    protected $fillable = ['vehicle_id', 'service_km', 'notes'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}