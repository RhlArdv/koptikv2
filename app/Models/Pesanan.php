<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = [
        'kode_pesanan',
        'nama_pelanggan',
        'nomor_meja',
        'total_harga',
        'status_pesanan',
        'status_pembayaran',
        'metode_pembayaran',
        'nominal_bayar',
        'kembalian',
        'kasir_id',
        'waktu_bayar',
        'catatan',
    ];

    protected $casts = [
        'total_harga'   => 'decimal:2',
        'nominal_bayar' => 'decimal:2',
        'kembalian'     => 'decimal:2',
        'waktu_bayar'   => 'datetime',
    ];

    // =========================================================
    // BOOT - AUTO GENERATE KODE PESANAN
    // =========================================================

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($pesanan) {
            if (empty($pesanan->kode_pesanan)) {
                $pesanan->kode_pesanan = self::generateKode();
            }
        });
    }

    /**
     * Generate kode pesanan unik.
     * Format: KT-YYYYMMDD-XXXX
     * Contoh: KT-20260302-0001
     */
    public static function generateKode(): string
    {
        $tanggal = Carbon::now()->format('Ymd');
        $prefix  = "KT-{$tanggal}-";

        // Cari nomor urut terakhir hari ini
        $last = self::where('kode_pesanan', 'like', $prefix . '%')
                    ->orderByDesc('kode_pesanan')
                    ->value('kode_pesanan');

        $urut = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);
    }

    // =========================================================
    // RELASI
    // =========================================================

    public function details(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'pesanan_id');
    }

    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    // =========================================================
    // HELPERS / ACCESSORS
    // =========================================================

    /**
     * Format total harga ke rupiah.
     */
    public function getTotalFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->total_harga, 0, ',', '.');
    }

    /**
     * Label status pesanan untuk tampilan.
     */
    public function getStatusPesananLabelAttribute(): string
    {
        return match($this->status_pesanan) {
            'menunggu'  => 'Menunggu',
            'diproses'  => 'Diproses',
            'selesai'   => 'Selesai',
            default     => ucfirst($this->status_pesanan),
        };
    }

    /**
     * Warna badge Tailwind berdasarkan status pesanan.
     * Digunakan di Blade: class="{{ $pesanan->statusPesananBadge }}"
     */
    public function getStatusPesananBadgeAttribute(): string
    {
        return match($this->status_pesanan) {
            'menunggu' => 'bg-yellow-100 text-yellow-800',
            'diproses' => 'bg-blue-100 text-blue-800',
            'selesai'  => 'bg-green-100 text-green-800',
            default    => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Warna badge status pembayaran.
     */
    public function getStatusBayarBadgeAttribute(): string
    {
        return $this->status_pembayaran === 'lunas'
            ? 'bg-green-100 text-green-800'
            : 'bg-red-100 text-red-800';
    }

    /**
     * Hitung kembalian dari nominal bayar.
     * Dipanggil sebelum konfirmasi pembayaran cash.
     */
    public function hitungKembalian(): float
    {
        return max(0, $this->nominal_bayar - $this->total_harga);
    }

    /**
     * Recalculate total_harga dari detail pesanan.
     * Dipanggil setelah semua item ditambahkan.
     */
    public function recalculateTotal(): void
    {
        $this->total_harga = $this->details()->sum('subtotal');
        $this->save();
    }

    // =========================================================
    // SCOPES
    // =========================================================

    public function scopeHariIni($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeBelumBayar($query)
    {
        return $query->where('status_pembayaran', 'belum_bayar');
    }

    public function scopeLunas($query)
    {
        return $query->where('status_pembayaran', 'lunas');
    }
}