<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom role_id ke tabel users yang sudah dibuat oleh Laravel Breeze.
     * Setiap user hanya punya SATU role (single role per user).
     * Role membawa kumpulan permissions, sehingga proteksi
     * tetap berbasis permission (bukan langsung cek role).
     *
     * Catatan: nullable() agar user yang belum diassign role
     * tidak error, nanti admin assign role via UI.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')
                  ->nullable()
                  ->after('email')
                  ->constrained('roles')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};