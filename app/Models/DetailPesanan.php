<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPesanan extends Model
{
    protected $table = 'detail_pesanan';

    protected $fillable = [
        'pesanan_id',
        'menu_id',
        'nama_menu_saat_pesan',
        'harga_saat_pesan',
        'qty',
        'subtotal',
        'catatan_item',
    ];

    protected $casts = [
        'harga_saat_pesan' => 'decimal:2',
        'subtotal'         => 'decimal:2',
        'qty'              => 'integer',
    ];

    // =========================================================
    // BOOT - AUTO FILL SNAPSHOT & SUBTOTAL
    // =========================================================

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($detail) {
            // Auto-fill snapshot nama dan harga dari relasi menu
            if ($detail->menu_id && ! $detail->nama_menu_saat_pesan) {
                $menu = Menu::find($detail->menu_id);
                if ($menu) {
                    $detail->nama_menu_saat_pesan = $menu->nama;
                    $detail->harga_saat_pesan     = $menu->harga;
                }
            }

            // Auto-hitung subtotal
            $detail->subtotal = $detail->qty * $detail->harga_saat_pesan;
        });

        static::updating(function ($detail) {
            // Recalculate subtotal jika qty berubah
            $detail->subtotal = $detail->qty * $detail->harga_saat_pesan;
        });
    }

    // =========================================================
    // RELASI
    // =========================================================

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    // =========================================================
    // HELPERS
    // =========================================================

    public function getSubtotalFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getHargaFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_saat_pesan, 0, ',', '.');
    }
}