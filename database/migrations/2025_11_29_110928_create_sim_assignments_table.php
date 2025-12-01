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
        Schema::create('sim_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sim_id')->constrained('sim_cards')->onDelete('cascade');
            $table->foreignId('device_id')->nullable()->constrained('gps_devices')->onDelete('set null');
            $table->foreignId('truck_id')->nullable()->constrained('trucks')->onDelete('set null');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('ended_at')->nullable();
            $table->string('status')->default('active'); // active, history
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sim_assignments');
    }
};
