<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('vehicle_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
        $table->decimal('distance_km', 10, 4); // Jarak per sesi/ping
        $table->timestamps(); // record waktu kejadian
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_logs');
    }
};
