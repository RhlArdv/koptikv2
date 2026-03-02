<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    /**
     * Role punya banyak permission (many-to-many)
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /**
     * Role punya banyak user (one-to-many)
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Cek apakah role ini punya permission tertentu
     */
    public function hasPermission(string $key): bool
    {
        return $this->permissions()->where('key', $key)->exists();
    }
}