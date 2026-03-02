<?php

namespace Database\Seeders;

use App\Models\KategoriMenu;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Seed data menu awal berdasarkan menu board Kopi Titik
     * yang terlihat di dokumentasi lapangan (Gambar 2.1 di proposal).
     *
     * Harga dalam rupiah (tanpa desimal, misal 15000).
     * Stok diisi 0 secara default — admin isi ulang via UI.
     */
    public function run(): void
    {
        // =====================================================
        // KATEGORI
        // =====================================================
        $coffee = KategoriMenu::firstOrCreate(
            ['slug' => 'coffee'],
            ['nama' => 'Coffee', 'urutan' => 1]
        );

        $nonCoffee = KategoriMenu::firstOrCreate(
            ['slug' => 'non-coffee'],
            ['nama' => 'Non Coffee', 'urutan' => 2]
        );

        $makananBerat = KategoriMenu::firstOrCreate(
            ['slug' => 'makanan-berat'],
            ['nama' => 'Makanan Berat', 'urutan' => 3]
        );

        $cemilan = KategoriMenu::firstOrCreate(
            ['slug' => 'cemilan'],
            ['nama' => 'Cemilan', 'urutan' => 4]
        );

        // =====================================================
        // MENU: COFFEE
        // =====================================================
        $coffeeItems = [
            // Kopi Susu
            ['nama' => 'Kopi Susu Titik',            'harga' => 15000],
            ['nama' => 'Kopi Susu Titik with Flavour','harga' => 20000],
            ['nama' => 'Kopi Susu Creamy',            'harga' => 17000],
            ['nama' => 'Kopi Susu Creamy with Flavour','harga' => 22000],
            ['nama' => 'Kopi Susu Coconut',           'harga' => 18000],
            ['nama' => 'Kopi Susu Coklat',            'harga' => 18000],
            ['nama' => 'Kopi Susu Sweetness',         'harga' => 16000],
            // Espresso Based
            ['nama' => 'Espresso',                    'harga' => 10000],
            ['nama' => 'Americano',                   'harga' => 13000],
            ['nama' => 'Americano Aren',               'harga' => 15000],
            ['nama' => 'Cafe Latte',                  'harga' => 18000],
            ['nama' => 'Cafe Latte with Flavour',     'harga' => 22000],
            ['nama' => 'Cappuccino',                  'harga' => 18000],
            ['nama' => 'Piccolo',                     'harga' => 20000],
            ['nama' => 'Caramel Macchiato',           'harga' => 25000],
        ];

        foreach ($coffeeItems as $item) {
            Menu::firstOrCreate(
                ['nama' => $item['nama'], 'kategori_id' => $coffee->id],
                array_merge($item, ['kategori_id' => $coffee->id, 'stok' => 0])
            );
        }

        // =====================================================
        // MENU: NON COFFEE
        // =====================================================
        $nonCoffeeItems = [
            // Latte
            ['nama' => 'Coklat Latte',                'harga' => 18000],
            ['nama' => 'Coklat Latte with Flavour',   'harga' => 23000],
            ['nama' => 'Red Velvet Latte',            'harga' => 18000],
            ['nama' => 'Taro Latte',                  'harga' => 18000],
            ['nama' => 'Matcha Latte',                'harga' => 20000],
            ['nama' => 'Pandan Lava',                 'harga' => 22000],
            // Tea
            ['nama' => 'Lychee Tea',                  'harga' => 20000],
            ['nama' => 'Lemon Tea',                   'harga' => 15000],
            ['nama' => 'Green Tea',                   'harga' => 15000],
            ['nama' => 'Ice Tea',                     'harga' => 10000],
            ['nama' => 'Mineral',                     'harga' => 5000],
            // Mojito
            ['nama' => 'Blue Curacao',                'harga' => 18000],
            ['nama' => 'Lemon Squash',                'harga' => 18000],
            ['nama' => 'Strawberry Squash',           'harga' => 18000],
        ];

        foreach ($nonCoffeeItems as $item) {
            Menu::firstOrCreate(
                ['nama' => $item['nama'], 'kategori_id' => $nonCoffee->id],
                array_merge($item, ['kategori_id' => $nonCoffee->id, 'stok' => 0])
            );
        }

        // =====================================================
        // MENU: MAKANAN BERAT
        // =====================================================
        $makananBeratItems = [
            ['nama' => 'Burger Titik',        'harga' => 18000],
            ['nama' => 'Nasi Ayam Titik',     'harga' => 20000],
            ['nama' => 'Nasi Ayam Geprek',    'harga' => 22000],
            ['nama' => 'Nasi Goreng Titik',   'harga' => 15000],
            ['nama' => 'Mie Goreng',          'harga' => 15000],
            ['nama' => 'Mie Rebus',           'harga' => 15000],
            ['nama' => 'Mie Bihun',           'harga' => 15000],
            ['nama' => 'Mie Kwetiau',         'harga' => 17000],
        ];

        foreach ($makananBeratItems as $item) {
            Menu::firstOrCreate(
                ['nama' => $item['nama'], 'kategori_id' => $makananBerat->id],
                array_merge($item, ['kategori_id' => $makananBerat->id, 'stok' => 0])
            );
        }

        // =====================================================
        // MENU: CEMILAN
        // =====================================================
        $cemilanItems = [
            ['nama' => 'French Fries',            'harga' => 15000],
            ['nama' => 'Roti Bakar Coklat Keju',  'harga' => 15000],
            ['nama' => 'Risoles',                  'harga' => 15000],
            ['nama' => 'Nugget',                   'harga' => 15000],
            ['nama' => 'Sosis',                    'harga' => 15000],
        ];

        foreach ($cemilanItems as $item) {
            Menu::firstOrCreate(
                ['nama' => $item['nama'], 'kategori_id' => $cemilan->id],
                array_merge($item, ['kategori_id' => $cemilan->id, 'stok' => 0])
            );
        }

        $this->command->info('✅ Menu Seeder selesai! ' . Menu::count() . ' menu berhasil dibuat.');
    }
}