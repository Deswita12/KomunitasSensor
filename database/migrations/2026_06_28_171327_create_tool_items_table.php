<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tool_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guide_step_id')->constrained()->cascadeOnDelete();
            $table->string('name');                 // "Seeed XIAO ESP32C3"
            $table->string('description')->nullable(); // "Otak perangkat (Microcontroller)"
            $table->string('icon')->nullable();      // material symbol name, mis. "memory"
            $table->enum('package', ['basic', 'plus', 'all'])->default('all');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tool_items');
    }
};