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
    Schema::create('vehicle_services', function (Blueprint $table) {
        $table->id();
        $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
        $table->decimal('service_km', 10, 2); // Pada KM berapa servis ini dilakukan
        $table->text('notes')->nullable(); // Catatan opsional (misal: Ganti Oli & Kanvas Rem)
        $table->timestamps(); // Menyimpan tanggal dan waktu servis
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_services');
    }
};
