<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel permissions menyimpan semua permission key yang ada di sistem.
     * Permission dikelompokkan per modul dengan kolom 'group' agar
     * tampil rapi di UI (misal: grup "Laporan", grup "Menu", dst).
     *
     * Contoh permissions:
     *   - view_dashboard
     *   - view_menu, create_menu, edit_menu, delete_menu
     *   - view_pesanan, proses_pesanan, konfirmasi_pembayaran
     *   - view_laporan, download_laporan
     *   - manage_users, manage_roles, manage_stock
     */
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();            // Permission key unik, misal: 'view_dashboard'
            $table->string('display_name');             // Label tampilan, misal: 'Lihat Dashboard'
            $table->string('group')->default('Umum');   // Grup untuk pengelompokan di UI
            $table->text('description')->nullable();    // Deskripsi opsional
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};