<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pivot table yang menghubungkan role dengan permission.
     * Satu role bisa punya banyak permission, dan satu permission
     * bisa dimiliki oleh banyak role (many-to-many).
     */
    public function up(): void
    {
        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')
                  ->constrained('roles')
                  ->onDelete('cascade');
            $table->foreignId('permission_id')
                  ->constrained('permissions')
                  ->onDelete('cascade');
            $table->timestamps();

            // Pastikan kombinasi role + permission tidak duplikat
            $table->unique(['role_id', 'permission_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permission');
    }
};