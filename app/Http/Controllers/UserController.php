<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->dataTable();
        }

        $roles = Role::orderBy('display_name')->get();
        return view('users.index', compact('roles'));
    }

    private function dataTable()
    {
        $users = User::with('role')->orderBy('name')->get();

        return datatables()->of($users)
            ->addIndexColumn()
            ->addColumn('role_badge', function ($user) {
                $color = match($user->role?->name) {
                    'admin'    => 'bg-purple-100 text-purple-700',
                    'kasir'    => 'bg-blue-100 text-blue-700',
                    'head_bar' => 'bg-amber-100 text-amber-700',
                    default    => 'bg-gray-100 text-gray-600',
                };
                $label = $user->role?->display_name ?? 'Tanpa Role';
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ' . $color . '">' . $label . '</span>';
            })
            ->addColumn('avatar', function ($user) {
                $initials = strtoupper(substr($user->name, 0, 2));
                return '<div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-400 to-orange-500
                            flex items-center justify-center text-white text-xs font-bold">'
                    . $initials . '</div>';
            })
            ->addColumn('aksi', function ($user) {
                /** @var User $currentUser */
                $currentUser = Auth::user();
                $btn = '';
                if ($currentUser->hasPermission('edit_users')) {
                    $btn .= '<button onclick="editUser(' . $user->id . ')"
                                class="px-3 py-1.5 text-xs font-medium text-amber-700 bg-amber-50
                                       hover:bg-amber-100 rounded-lg transition-colors mr-1">
                                Edit
                             </button>';
                }
                if ($currentUser->hasPermission('delete_users') && $user->id !== Auth::id()) {
                    $btn .= '<button onclick="hapusUser(' . $user->id . ', \'' . addslashes($user->name) . '\')"
                                class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50
                                       hover:bg-red-100 rounded-lg transition-colors">
                                Hapus
                             </button>';
                }
                return $btn ?: '<span class="text-xs text-gray-400">—</span>';
            })
            ->rawColumns(['avatar', 'role_badge', 'aksi'])
            ->make(true);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name'     => 'required|string|max:100',
                'email'    => 'required|email|unique:users,email',
                'password' => ['required', Password::min(6)],
                'role_id'  => 'required|exists:roles,id',
            ], [
                'name.required'     => 'Nama wajib diisi.',
                'email.required'    => 'Email wajib diisi.',
                'email.unique'      => 'Email sudah digunakan.',
                'password.required' => 'Password wajib diisi.',
                'role_id.required'  => 'Role wajib dipilih.',
            ]);

            User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role_id'  => $request->role_id,
            ]);

            return response()->json(['success' => true, 'message' => 'User ' . $request->name . ' berhasil ditambahkan.']);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menambahkan user.'], 500);
        }
    }

    public function show($id)
    {
        $user = User::with('role')->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => [
                'id'      => $user->id,
                'name'    => $user->name,
                'email'   => $user->email,
                'role_id' => $user->role_id,
                'role'    => $user->role?->display_name,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $request->validate([
                'name'     => 'required|string|max:100',
                'email'    => 'required|email|unique:users,email,' . $id,
                'password' => ['nullable', Password::min(6)],
                'role_id'  => 'required|exists:roles,id',
            ], [
                'email.unique' => 'Email sudah digunakan user lain.',
            ]);

            $data = [
                'name'    => $request->name,
                'email'   => $request->email,
                'role_id' => $request->role_id,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            return response()->json(['success' => true, 'message' => 'User ' . $user->name . ' berhasil diperbarui.']);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui user.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            if ((int)$id === Auth::id()) {
                return response()->json(['success' => false, 'message' => 'Tidak bisa menghapus akun sendiri.'], 422);
            }

            $user = User::findOrFail($id);
            $nama = $user->name;
            $user->delete();

            return response()->json(['success' => true, 'message' => 'User ' . $nama . ' berhasil dihapus.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus user.'], 500);
        }
    }
}