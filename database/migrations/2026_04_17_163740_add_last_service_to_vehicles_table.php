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
    Schema::table('vehicles', function (Blueprint $table) {
        // Menyimpan angka KM saat terakhir kali diservis
        $table->decimal('last_service_km', 10, 2)->default(0)->after('current_accumulated_km');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            //
        });
    }
};
