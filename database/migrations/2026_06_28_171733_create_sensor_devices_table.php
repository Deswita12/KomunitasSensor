<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sensor_devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->unique();  // ID dari platform Smart Citizen, mis. "19684"
            $table->string('name')->nullable();      // nama label internal, mis. "Node Tigaraksa"
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensor_devices');
    }
};