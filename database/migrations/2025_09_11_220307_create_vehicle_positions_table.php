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
        Schema::create('vehicle_positions', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_id');
            $table->string('registration')->nullable(); // no polisi
            $table->decimal('latitude', 10, 6);
            $table->decimal('longitude', 10, 6);
            $table->string('event_description')->nullable(); // IGNITION_ON, IDLING, dll
            $table->timestamp('event_ts')->nullable();       // waktu event
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_positions');
    }
};
