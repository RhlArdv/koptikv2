<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Menu extends Model
{
    protected $table = 'menu';

    protected $fillable = [
        'kategori_id',
        'nama',
        'slug',
        'deskripsi',
        'harga',
        'gambar',
        'stok',
        'is_aktif',
    ];

    protected $casts = [
        'harga'    => 'decimal:2',
        'stok'     => 'integer',
        'is_aktif' => 'boolean',
    ];

    /**
     * Auto-generate slug dari nama jika tidak diisi.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($menu) {
            if (empty($menu->slug)) {
                $menu->slug = Str::slug($menu->nama);
            }
        });
    }

    // =========================================================
    // RELASI
    // =========================================================

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriMenu::class, 'kategori_id');
    }

    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'menu_id');
    }

    // =========================================================
    // HELPERS / ACCESSORS
    // =========================================================

    /**
     * Apakah menu ini habis stok?
     * Digunakan di Blade untuk tampilkan overlay "Habis".
     *
     * Contoh di Blade:
     *   @if($menu->isHabis()) ... @endif
     */
    public function isHabis(): bool
    {
        return $this->stok <= 0;
    }

    /**
     * Format harga ke rupiah tanpa desimal.
     * Contoh: 15000.00 → "Rp 15.000"
     */
    public function getHargaFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    /**
     * URL gambar menu. Jika tidak ada gambar, gunakan placeholder.
     */
    public function getGambarUrlAttribute(): string
    {
        if ($this->gambar && file_exists(public_path('storage/' . $this->gambar))) {
            return asset('storage/' . $this->gambar);
        }

        return asset('images/menu-placeholder.png');
    }

    // =========================================================
    // SCOPES
    // =========================================================

    /**
     * Scope: hanya menu yang aktif.
     * Contoh: Menu::aktif()->get()
     */
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    /**
     * Scope: hanya menu yang masih ada stoknya.
     * Contoh: Menu::tersedia()->get()
     */
    public function scopeTersedia($query)
    {
        return $query->where('stok', '>', 0);
    }
}