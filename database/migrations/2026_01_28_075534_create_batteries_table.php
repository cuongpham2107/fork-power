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
        Schema::create('batteries', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // 19, 11, VII 455, VII 470
            $table->string('type')->nullable(); // Loại pin // Lithium, Alkaline
            $table->string('capacity')->nullable(); // Dung lượng pin // 100Ah, 200Ah
            $table->string('voltage')->nullable(); // Điện áp pin // 12V, 24V
            $table->string('size')->nullable(); // Kích thước pin // 200x150x100mm
            $table->decimal('total_working_hours', 10, 2)->default(0); // Tổng số giờ làm việc
            $table->enum('status', ['standby', 'in_use', 'charging', 'maintenance'])->default('standby'); // standby, in_use, charging, maintenance
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batteries');
    }
};
