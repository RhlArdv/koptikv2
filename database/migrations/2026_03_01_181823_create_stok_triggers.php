<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * SQL Triggers untuk mengelola stok menu secara otomatis.
     *
     * TRIGGER 1 - after_detail_pesanan_insert:
     *   Setiap kali item baru ditambahkan ke pesanan,
     *   kurangi stok menu sebesar qty yang dipesan.
     *
     * TRIGGER 2 - after_detail_pesanan_update:
     *   Jika qty diubah (misal dari 2 jadi 3),
     *   sesuaikan stok dengan selisih qty baru - qty lama.
     *
     * TRIGGER 3 - after_detail_pesanan_delete:
     *   Jika item pesanan dihapus (misal pelanggan batalkan item),
     *   kembalikan stok sebesar qty yang dihapus.
     *
     * PENTING: Stok tidak akan pernah negatif karena ada
     * validasi di aplikasi (Model/Controller) sebelum INSERT.
     * Trigger ini sebagai lapisan pengaman kedua di DB level.
     */
    public function up(): void
    {
        // TRIGGER 1: Kurangi stok saat item baru dipesan
        DB::unprepared('
            CREATE TRIGGER after_detail_pesanan_insert
            AFTER INSERT ON detail_pesanan
            FOR EACH ROW
            BEGIN
                UPDATE menu
                SET stok = stok - NEW.qty
                WHERE id = NEW.menu_id AND stok >= NEW.qty;
            END
        ');

        // TRIGGER 2: Sesuaikan stok jika qty diupdate
        DB::unprepared('
            CREATE TRIGGER after_detail_pesanan_update
            AFTER UPDATE ON detail_pesanan
            FOR EACH ROW
            BEGIN
                -- Hitung selisih: jika qty bertambah, stok dikurangi
                -- Jika qty berkurang, stok dikembalikan
                UPDATE menu
                SET stok = stok - (NEW.qty - OLD.qty)
                WHERE id = NEW.menu_id;
            END
        ');

        // TRIGGER 3: Kembalikan stok jika item pesanan dihapus
        DB::unprepared('
            CREATE TRIGGER after_detail_pesanan_delete
            AFTER DELETE ON detail_pesanan
            FOR EACH ROW
            BEGIN
                UPDATE menu
                SET stok = stok + OLD.qty
                WHERE id = OLD.menu_id;
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS after_detail_pesanan_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS after_detail_pesanan_update');
        DB::unprepared('DROP TRIGGER IF EXISTS after_detail_pesanan_delete');
    }
};