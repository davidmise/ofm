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
        Schema::create('commands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('truck_id')->constrained('trucks')->onDelete('cascade');
            $table->foreignId('device_id')->nullable()->constrained('gps_devices')->onDelete('set null');
            $table->foreignId('sim_id')->nullable()->constrained('sim_cards')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Who sent it
            $table->string('command_type'); // Immobilize, Resume, Locate, etc.
            $table->json('payload')->nullable();
            $table->string('status')->default('pending'); // pending, sent, delivered, failed, success
            $table->text('response')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commands');
    }
};
