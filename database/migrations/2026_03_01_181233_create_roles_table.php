<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel roles menyimpan daftar role yang bisa diassign ke user.
     * Contoh role: admin, kasir, head_bar
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();           // Nama role, misal: 'admin', 'kasir'
            $table->string('display_name');             // Label tampilan, misal: 'Administrator', 'Kasir'
            $table->text('description')->nullable();    // Deskripsi opsional role ini
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};