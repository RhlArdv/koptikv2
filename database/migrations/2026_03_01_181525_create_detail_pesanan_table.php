<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel detail_pesanan menyimpan item-item dalam satu pesanan.
     *
     * 'harga_saat_pesan' dan 'nama_menu_saat_pesan' disimpan snapshot-nya
     * agar laporan historis tidak berubah meski harga/nama menu diupdate nantinya.
     *
     * SQL TRIGGER akan dibuat untuk mengurangi stok di tabel 'menu'
     * secara otomatis setiap kali row baru INSERT ke tabel ini.
     * Trigger juga perlu handle UPDATE (jika qty diubah) dan DELETE.
     */
    public function up(): void
    {
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')
                  ->constrained('pesanan')
                  ->onDelete('cascade');
            $table->foreignId('menu_id')
                  ->constrained('menu')
                  ->onDelete('cascade');

            // Snapshot data menu saat pemesanan (anti historical drift)
            $table->string('nama_menu_saat_pesan');
            $table->decimal('harga_saat_pesan', 10, 2);

            $table->unsignedInteger('qty');                     // Jumlah yang dipesan
            $table->decimal('subtotal', 10, 2);                 // qty * harga_saat_pesan
            $table->text('catatan_item')->nullable();           // Misal: "less sugar", "extra shot"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan');
    }
};