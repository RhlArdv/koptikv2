<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel pesanan adalah header dari setiap transaksi.
     * Alur status pesanan:
     *   menunggu → diproses → selesai
     *
     * Alur status pembayaran:
     *   belum_bayar → lunas
     *
     * Metode pembayaran:
     *   - cash   : pelanggan bayar tunai, kasir input nominal, sistem hitung kembalian
     *   - qris   : pelanggan scan QR statis, kasir konfirmasi manual di sistem
     *
     * 'kode_pesanan' digunakan untuk identifikasi cepat,
     * formatnya: KT-YYYYMMDD-XXXX (misal: KT-20260302-0001)
     */
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pesanan')->unique();           // Kode unik untuk referensi
            $table->string('nama_pelanggan');                   // Nama yang diinput pelanggan saat scan QR
            $table->string('nomor_meja');                       // Nomor meja yang diinput pelanggan
            $table->decimal('total_harga', 10, 2)->default(0); // Total harga semua item

            // Status pemrosesan pesanan oleh dapur/bar
            $table->enum('status_pesanan', ['menunggu', 'diproses', 'selesai'])
                  ->default('menunggu');

            // Status pembayaran
            $table->enum('status_pembayaran', ['belum_bayar', 'lunas'])
                  ->default('belum_bayar');

            // Metode pembayaran (diisi saat kasir konfirmasi)
            $table->enum('metode_pembayaran', ['cash', 'qris'])->nullable();

            // Kolom untuk transaksi cash
            $table->decimal('nominal_bayar', 10, 2)->nullable();    // Uang yang diberikan pelanggan
            $table->decimal('kembalian', 10, 2)->nullable();        // Otomatis: nominal_bayar - total_harga

            // User kasir yang memproses pembayaran (nullable karena diisi saat checkout)
            $table->foreignId('kasir_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            $table->timestamp('waktu_bayar')->nullable();           // Kapan pembayaran dikonfirmasi
            $table->text('catatan')->nullable();                    // Catatan tambahan dari pelanggan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};