<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel menu menyimpan semua produk yang dijual Kopi Titik.
     * Kolom 'stok' akan dikurangi otomatis via SQL TRIGGER
     * ketika item masuk ke detail_pesanan.
     *
     * Jika stok = 0, tampilan halaman pelanggan akan menampilkan
     * overlay "Habis" dan gambar menu menjadi abu-abu (handled di View).
     */
    public function up(): void
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')
                  ->constrained('kategori_menu')
                  ->onDelete('cascade');
            $table->string('nama');                         // Nama menu, misal: 'Kopi Susu Titik'
            $table->string('slug')->unique();               // URL-friendly
            $table->text('deskripsi')->nullable();          // Deskripsi singkat menu
            $table->decimal('harga', 10, 2);                // Harga dalam rupiah, misal: 15000.00
            $table->string('gambar')->nullable();           // Path gambar, misal: 'menu/kopi-susu.jpg'
            $table->unsignedInteger('stok')->default(0);    // Stok saat ini (dikurangi by trigger)
            $table->boolean('is_aktif')->default(true);     // Bisa dinonaktifkan tanpa hapus data
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};