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
        Schema::create('fork_lifts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Komatsu 01, Toyota 02
            $table->string('brand'); // KOMATSU, TOYOTA
            $table->string('serial_number'); // Serial number Car
            $table->decimal('total_working_hours', 10, 2)->default(0); // Số giờ hoạt động trên màn hình
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active'); // Hoạt động, không hoạt động, bảo trì
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fork_lifts');
    }
};
