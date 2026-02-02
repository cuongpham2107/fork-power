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
        Schema::create('battery_chargers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Mã sạc pin
            $table->string('location')->nullable(); // Vị trí đặt sạc pin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battery_chargers');
    }
};
