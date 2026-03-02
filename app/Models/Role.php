<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    /**
     * Permissions yang dimiliki role ini.
     * Diakses via: $role->permissions
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /**
     * Users yang memiliki role ini.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Cek apakah role ini memiliki permission key tertentu.
     * Digunakan di logic proteksi route.
     *
     * Contoh: $role->hasPermission('view_laporan')
     */
    public function hasPermission(string $key): bool
    {
        return $this->permissions->contains('key', $key);
    }
}