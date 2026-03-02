<?php

namespace App\Models;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // =========================================================
    // RELASI
    // =========================================================

    /**
     * Role yang dimiliki user ini.
     * Diakses via: $user->role
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Pesanan yang diproses kasir ini (kasir_id di tabel pesanan).
     */
    public function pesananDiproses(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'kasir_id');
    }

    // =========================================================
    // RBAC HELPER METHODS
    // =========================================================

    /**
     * Cek apakah user memiliki permission key tertentu.
     * Proteksi di sistem ini berbasis PERMISSION, bukan role langsung.
     *
     * Contoh penggunaan:
     *   $user->can('view_laporan')          → via Gate (setelah setup di AuthServiceProvider)
     *   $user->hasPermission('view_laporan') → langsung di Blade/Controller
     *
     * @param string $key  Permission key, misal: 'view_laporan'
     */
    public function hasPermission(string $key): bool
    {
        // Admin (role name = 'admin') punya akses ke semua permission
        if ($this->isAdmin()) {
            return true;
        }

        if (! $this->role) {
            return false;
        }

        // Load permissions dari relasi role (di-cache Laravel secara otomatis)
        return $this->role->permissions->contains('key', $key);
    }

    /**
     * Cek apakah user adalah admin.
     * Admin punya akses penuh tanpa perlu cek permission satu per satu.
     */
    public function isAdmin(): bool
    {
        return $this->role?->name === 'admin';
    }

    /**
     * Cek apakah user punya role tertentu.
     * Digunakan untuk keperluan tampilan (misal: tampilkan menu tertentu).
     *
     * Contoh: $user->hasRole('kasir')
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role?->name === $roleName;
    }

    /**
     * Ambil semua permission key yang dimiliki user ini.
     * Berguna untuk pass ke JavaScript (misal: untuk kontrol tampilan dinamis).
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPermissions()
    {
        if ($this->isAdmin()) {
            return Permission::pluck('key');
        }

        return $this->role?->permissions->pluck('key') ?? collect();
    }
}