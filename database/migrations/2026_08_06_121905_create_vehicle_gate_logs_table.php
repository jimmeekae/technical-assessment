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
        Schema::create('vehicle_gate_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained();
            $table->foreignId('driver_id')->constrained();
            $table->string('driver_id_number');
            $table->string('driver_phone');

            // Gate In tracking
            $table->timestamp('gated_in_at');
            $table->foreignId('gated_in_by')->constrained('users');

            // Gate Out tracking
            $table->timestamp('gated_out_at')->nullable();
            $table->foreignId('gated_out_by')->nullable()->constrained('users');

            $table->string('status')->default('GATED_IN'); // GATED_IN or GATED_OUT
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_gate_logs');
    }
};
