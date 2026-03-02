<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class KategoriMenu extends Model
{
    protected $table = 'kategori_menu';

    protected $fillable = [
        'nama',
        'slug',
        'urutan',
    ];

    /**
     * Auto-generate slug dari nama jika tidak diisi.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($kategori) {
            if (empty($kategori->slug)) {
                $kategori->slug = Str::slug($kategori->nama);
            }
        });
    }

    /**
     * Menu yang ada di kategori ini.
     */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class, 'kategori_id');
    }

    /**
     * Menu aktif saja (untuk halaman pelanggan).
     */
    public function menusAktif(): HasMany
    {
        return $this->hasMany(Menu::class, 'kategori_id')
                    ->where('is_aktif', true)
                    ->orderBy('nama');
    }
}