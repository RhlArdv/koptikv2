<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Urutan seeder penting!
     * RbacSeeder harus jalan dulu karena membuat roles
     * yang dibutuhkan saat buat user admin.
     */
    public function run(): void
    {
        $this->call([
            RbacSeeder::class,  // Roles, Permissions, User Admin
            MenuSeeder::class,  // Kategori & Menu Kopi Titik
        ]);
    }
}