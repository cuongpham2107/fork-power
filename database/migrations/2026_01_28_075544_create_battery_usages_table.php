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
        Schema::create('battery_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('battery_id')->constrained('batteries')->onDelete('cascade');
            $table->foreignId('fork_lift_id')->constrained('fork_lifts')->onDelete('cascade');

            //Dữ liệu lúc lắp
            $table->integer('charger_bar')->nullable(); //Số vạch hiển thị trên máy nạp 1-4
            $table->decimal('screen_bar', 8, 2)->nullable(); //Số vạch hiển thị trên màn hình
            $table->decimal('hour_initial', 10, 2)->nullable(); // Số giờ lắp vào
            $table->timestamp('installed_at')->nullable(); // Thời gian lắp pin

            //Dữ liệu lúc tháo 
            $table->decimal('hour_out', 10, 2)->nullable(); // Số giờ tháo ra
            $table->timestamp('removed_at')->nullable(); // Thời gian tháo pin

            //Kết quả
            $table->decimal('working_hours', 10, 2)->nullable(); // Số giờ làm việc hour_out - hour_in

            //Người thao tác 
            $table->foreignId('installed_by')->nullable()->constrained('users')->onDelete('set null');

            $table->foreignId('removed_by')->nullable()->constrained('users')->onDelete('set null');

            $table->enum('status', ['running', 'finished'])->default('running'); // Trạng thái sử dụng pin "đang chạy" | "hoàn thành"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battery_usages');
    }
};
