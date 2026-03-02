<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================================
        // PERMISSIONS
        // ============================================================
        $permissions = [

            // Dashboard
            ['key' => 'view_dashboard',        'display_name' => 'Lihat Dashboard',        'group' => 'Dashboard'],

            // Kategori Menu
            ['key' => 'view_kategori',          'display_name' => 'Lihat Kategori',          'group' => 'Kategori Menu'],
            ['key' => 'create_kategori',        'display_name' => 'Tambah Kategori',         'group' => 'Kategori Menu'],
            ['key' => 'edit_kategori',          'display_name' => 'Edit Kategori',           'group' => 'Kategori Menu'],
            ['key' => 'delete_kategori',        'display_name' => 'Hapus Kategori',          'group' => 'Kategori Menu'],

            // Menu
            ['key' => 'view_menu',              'display_name' => 'Lihat Menu',              'group' => 'Menu'],
            ['key' => 'create_menu',            'display_name' => 'Tambah Menu',             'group' => 'Menu'],
            ['key' => 'edit_menu',              'display_name' => 'Edit Menu',               'group' => 'Menu'],
            ['key' => 'delete_menu',            'display_name' => 'Hapus Menu',              'group' => 'Menu'],

            // Stok
            ['key' => 'view_stok',              'display_name' => 'Lihat Stok',              'group' => 'Stok'],
            ['key' => 'manage_stok',            'display_name' => 'Kelola Stok (isi ulang)', 'group' => 'Stok'],

            // Pesanan
            ['key' => 'view_pesanan',           'display_name' => 'Lihat Pesanan Masuk',     'group' => 'Pesanan'],
            ['key' => 'proses_pesanan',         'display_name' => 'Update Status Pesanan',   'group' => 'Pesanan'],
            ['key' => 'konfirmasi_pembayaran',  'display_name' => 'Konfirmasi Pembayaran',   'group' => 'Pesanan'],
            ['key' => 'delete_pesanan',         'display_name' => 'Hapus Pesanan',           'group' => 'Pesanan'],
            ['key' => 'view_histori_pesanan',   'display_name' => 'Lihat Histori Pesanan',   'group' => 'Pesanan'],

            // Laporan
            ['key' => 'view_laporan',           'display_name' => 'Lihat Laporan',           'group' => 'Laporan'],
            ['key' => 'download_laporan',       'display_name' => 'Download Laporan',        'group' => 'Laporan'],

            // Manajemen Pengguna
            ['key' => 'view_users',             'display_name' => 'Lihat Pengguna',          'group' => 'Manajemen Pengguna'],
            ['key' => 'create_users',           'display_name' => 'Tambah Pengguna',         'group' => 'Manajemen Pengguna'],
            ['key' => 'edit_users',             'display_name' => 'Edit Pengguna',           'group' => 'Manajemen Pengguna'],
            ['key' => 'delete_users',           'display_name' => 'Hapus Pengguna',          'group' => 'Manajemen Pengguna'],

            // Manajemen Role
            ['key' => 'view_roles',             'display_name' => 'Lihat Role',              'group' => 'Manajemen Role'],
            ['key' => 'edit_roles',             'display_name' => 'Edit Permission Role',    'group' => 'Manajemen Role'],
        ];

        foreach ($permissions as $p) {
            Permission::updateOrCreate(
                ['key' => $p['key']],
                array_merge($p, ['description' => null])
            );
        }

        // ============================================================
        // ROLES + PERMISSION ASSIGNMENT
        // ============================================================

        // --- ADMIN: akses penuh ke semua ---
        $admin = Role::updateOrCreate(
            ['name' => 'admin'],
            ['display_name' => 'Administrator', 'description' => 'Akses penuh ke seluruh sistem']
        );
        $admin->permissions()->sync(Permission::pluck('id'));

        // --- KASIR: fokus pesanan & pembayaran ---
        $kasir = Role::updateOrCreate(
            ['name' => 'kasir'],
            ['display_name' => 'Kasir', 'description' => 'Proses pesanan dan pembayaran']
        );
        $kasir->permissions()->sync(
            Permission::whereIn('key', [
                'view_dashboard',
                'view_pesanan',
                'konfirmasi_pembayaran',
                'view_histori_pesanan',
            ])->pluck('id')
        );

        // --- HEAD BAR: kelola menu, stok, pantau pesanan ---
        $headBar = Role::updateOrCreate(
            ['name' => 'head_bar'],
            ['display_name' => 'Head Bar', 'description' => 'Kelola menu, stok, dan proses pesanan']
        );
        $headBar->permissions()->sync(
            Permission::whereIn('key', [
                'view_dashboard',
                'view_kategori',
                'create_kategori',
                'edit_kategori',
                'view_menu',
                'create_menu',
                'edit_menu',
                'view_stok',
                'manage_stok',
                'view_pesanan',
                'proses_pesanan',
                'view_histori_pesanan',
            ])->pluck('id')
        );

        // ============================================================
        // USER ADMIN DEFAULT
        // ============================================================
        $userAdmin = User::updateOrCreate(
            ['email' => 'admin@kopititik.com'],
            [
                'name'     => 'Administrator',
                'password' => Hash::make('admin123'),
                'role_id'  => $admin->id,
            ]
        );

        // User kasir contoh
        User::updateOrCreate(
            ['email' => 'kasir@kopititik.com'],
            [
                'name'     => 'Kasir',
                'password' => Hash::make('kasir123'),
                'role_id'  => $kasir->id,
            ]
        );

        // User head bar contoh
        User::updateOrCreate(
            ['email' => 'headbar@kopititik.com'],
            [
                'name'     => 'Head Bar',
                'password' => Hash::make('headbar123'),
                'role_id'  => $headBar->id,
            ]
        );

        $this->command->info('✓ RBAC selesai: ' . Permission::count() . ' permissions, ' . Role::count() . ' roles, ' . User::count() . ' users');
    }
}