<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('permissions')->orderBy('display_name')->get();
        return view('roles.index', compact('roles'));
    }

    /**
     * Halaman edit permission per role.
     */
    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        $permissionGroups = Permission::orderBy('group')->orderBy('display_name')
            ->get()
            ->groupBy('group');

        $assignedIds = $role->permissions->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'permissionGroups', 'assignedIds'));
    }

    /**
     * Simpan assignment permission ke role.
     */
    public function update(Request $request, $id)
    {
        try {
            $role = Role::findOrFail($id);

            $permissionIds = $request->input('permissions', []);
            $validIds = Permission::whereIn('id', $permissionIds)->pluck('id')->toArray();

            $role->permissions()->sync($validIds);

            return response()->json([
                'success' => true,
                'message' => 'Permission role ' . $role->display_name . ' berhasil diperbarui. '
                    . count($validIds) . ' permission aktif.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui permission.',
            ], 500);
        }
    }

    /**
     * Reset ke default permission sesuai seeder.
     */
    public function reset($id)
    {
        try {
            $role = Role::findOrFail($id);

            if ($role->name === 'admin') {
                // Admin = semua permission
                $role->permissions()->sync(Permission::pluck('id'));
                return response()->json([
                    'success' => true,
                    'message' => 'Admin direset — semua permission diaktifkan.',
                ]);
            }

            $defaults = $this->getDefaultPermissions($role->name);
            $ids = Permission::whereIn('key', $defaults)->pluck('id');
            $role->permissions()->sync($ids);

            return response()->json([
                'success' => true,
                'message' => 'Permission role ' . $role->display_name . ' berhasil direset ke default.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mereset permission.',
            ], 500);
        }
    }

    private function getDefaultPermissions(string $roleName): array
    {
        return match($roleName) {
            'kasir' => [
                'view_dashboard',
                'view_pesanan',
                'konfirmasi_pembayaran',
                'view_histori_pesanan',
            ],
            'head_bar' => [
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
            ],
            default => [],
        };
    }
}