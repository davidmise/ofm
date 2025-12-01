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
        Schema::create('sim_cards', function (Blueprint $table) {
            $table->id();
            $table->string('iccid')->unique();
            $table->string('phone_number')->nullable();
            $table->string('network_provider')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('status')->default('active'); // active, inactive, expiring
            $table->string('sim_type')->default('data'); // data, voice, mixed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sim_cards');
    }
};
