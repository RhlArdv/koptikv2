<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount(['permissions', 'users'])->orderBy('display_name')->get();
        return view('roles.index', compact('roles'));
    }

    /**
     * Tambah role baru.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'display_name' => 'required|string|max:100',
                'description'  => 'nullable|string|max:255',
            ], [
                'display_name.required' => 'Nama role wajib diisi.',
            ]);

            // Generate key dari display_name: "Head Bar 2" → "head_bar_2"
            $key = Str::slug($request->display_name, '_');

            if (Role::where('name', $key)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role dengan nama tersebut sudah ada.',
                ], 422);
            }

            $role = Role::create([
                'name'         => $key,
                'display_name' => $request->display_name,
                'description'  => $request->description,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role "' . $role->display_name . '" berhasil ditambahkan.',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan role.',
            ], 500);
        }
    }

    /**
     * Update nama/deskripsi role (bukan permission).
     */
    public function updateInfo(Request $request, $id)
    {
        try {
            $role = Role::findOrFail($id);

            // Proteksi role bawaan sistem
            if (in_array($role->name, ['admin', 'kasir', 'head_bar'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role bawaan sistem tidak bisa diubah namanya.',
                ], 422);
            }

            $request->validate([
                'display_name' => 'required|string|max:100',
                'description'  => 'nullable|string|max:255',
            ]);

            $role->update([
                'display_name' => $request->display_name,
                'description'  => $request->description,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role berhasil diperbarui.',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui role.',
            ], 500);
        }
    }

    /**
     * Hapus role — hanya role custom (bukan admin/kasir/head_bar).
     */
    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);

            // Proteksi role bawaan
            if (in_array($role->name, ['admin', 'kasir', 'head_bar'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role bawaan sistem tidak bisa dihapus.',
                ], 422);
            }

            // Cek apakah ada user yang masih pakai role ini
            if ($role->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role tidak bisa dihapus karena masih dipakai ' . $role->users()->count() . ' user.',
                ], 422);
            }

            $nama = $role->display_name;
            $role->permissions()->detach(); // hapus pivot permission dulu
            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Role "' . $nama . '" berhasil dihapus.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus role.',
            ], 500);
        }
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
                'view_dashboard', 'view_pesanan',
                'konfirmasi_pembayaran', 'view_histori_pesanan',
            ],
            'head_bar' => [
                'view_dashboard', 'view_kategori', 'create_kategori',
                'edit_kategori', 'view_menu', 'create_menu', 'edit_menu',
                'view_stok', 'manage_stok', 'view_pesanan',
                'proses_pesanan', 'view_histori_pesanan',
            ],
            default => [],
        };
    }
}