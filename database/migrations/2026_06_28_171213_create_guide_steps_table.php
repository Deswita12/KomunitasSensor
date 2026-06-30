<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guide_steps', function (Blueprint $table) {
            $table->id();

            // Identitas & urutan
            $table->string('title');                  // "Persiapan Alat"
            $table->unsignedInteger('order')->default(0);
            $table->string('icon')->nullable();        // nama material-symbols, mis. "memory"
            $table->boolean('is_active')->default(true);

            // Tipe template — menentukan field mana yang relevan
            $table->enum('type', [
                'text_image',     // Model A: Teks + Gambar
                'video',          // Model B: Video
                'wiring_diagram', // Model C: Tabel Wiring + Diagram
                'code_block',     // Model D: Code Block
                'tool_list',      // Model E: Daftar Alat (Basic/Plus)
            ]);

            // Tip dari "Oyen" (opsional di semua tipe)
            $table->text('tip_text')->nullable();

            // --- Field untuk type: text_image ---
            $table->longText('body_text')->nullable();     // rich text / markdown
            $table->string('image_path')->nullable();

            // --- Field untuk type: video ---
            $table->string('video_path_basic')->nullable();
            $table->string('video_path_plus')->nullable();
            $table->text('video_caption')->nullable();

            // --- Field untuk type: wiring_diagram ---
            $table->json('wiring_rows')->nullable();        // [{sensor_pin, board_pin, function}, ...]
            $table->string('diagram_image_basic')->nullable();
            $table->string('diagram_image_plus')->nullable();
            $table->text('warning_text')->nullable();       // box peringatan merah

            // --- Field untuk type: code_block ---
            $table->longText('code_content')->nullable();
            $table->string('code_language')->default('cpp');
            $table->text('code_note')->nullable();           // tips box di bawah code

            // --- Field untuk type: tool_list ---
            // tool_list sebenarnya menarik dari tabel tool_items (lihat Langkah 5)
            // kolom ini hanya untuk teks pengantar di atas grid alat
            $table->text('tool_list_intro')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guide_steps');
    }
};