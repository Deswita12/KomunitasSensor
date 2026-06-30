<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');                  // "Dokumentasi: Merakit Massal di Tigaraksa"
            $table->string('subtitle')->nullable();    // "Bengkel Udara Community x Pemkab Tangerang (0:45)"

            // Salah satu dari dua ini wajib diisi:
            $table->string('video_path')->nullable();  // file upload lokal (mp4)
            $table->string('embed_url')->nullable();   // link YouTube/Vimeo, jika tidak upload file

            $table->string('thumbnail_path')->nullable(); // poster/cover sebelum video diklik play

            $table->boolean('is_featured')->default(false); // hanya 1 yang boleh true — ditegakkan di Resource
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_videos');
    }
};