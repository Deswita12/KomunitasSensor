<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_notes', function (Blueprint $table) {
            $table->id();
            $table->string('title');                 // judul singkat, mis. "Membuat Casing Pelindung Hujan dengan 3D Print"
            $table->text('body');                     // teks pendek, tanpa rich editor — cukup plain/markdown sederhana
            $table->string('image')->nullable();      // foto opsional, tidak wajib
            $table->string('tag')->nullable();        // "Tips Hardware", "Modifikasi", dst — teks bebas, opsional
            $table->string('author_name')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_notes');
    }
};