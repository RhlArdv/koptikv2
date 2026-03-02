<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel kategori_menu untuk mengelompokkan menu.
     * Berdasarkan menu Kopi Titik, kategori yang ada:
     *   - Coffee (Kopi Susu, Espresso Based, dll)
     *   - Non Coffee (Latte, Tea, Mojito, dll)
     *   - Makanan Berat
     *   - Cemilan
     */
    public function up(): void
    {
        Schema::create('kategori_menu', function (Blueprint $table) {
            $table->id();
            $table->string('nama');                     // Nama kategori, misal: 'Coffee', 'Non Coffee'
            $table->string('slug')->unique();           // URL-friendly, misal: 'coffee', 'non-coffee'
            $table->unsignedInteger('urutan')->default(0); // Urutan tampil di halaman menu
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_menu');
    }
};