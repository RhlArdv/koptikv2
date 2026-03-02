<?php

namespace App\Http\Controllers;

use App\Models\KategoriMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class KategoriMenuController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return $this->dataTable();
        }

        return view('kategori.index');
    }

    public function dataTable()
    {
        $builder = KategoriMenu::withCount('menus')
            ->orderBy('urutan');

        return DataTables::of($builder)
            ->addIndexColumn()
            ->addColumn('jumlah_menu', function ($row) {
                return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs
                               font-medium bg-amber-50 text-amber-700">'
                    . $row->menus_count . ' menu'
                    . '</span>';
            })
            ->addColumn('aksi', function ($row) {
                $editBtn = '';
                $hapusBtn = '';

                /** @var \App\Models\User $user */
                $user = Auth::user();

                if ($user->hasPermission('edit_kategori')) {
                    $editBtn = '
                        <button
                            onclick="editKategori(' . $row->id . ', \'' . addslashes($row->nama) . '\', ' . $row->urutan . ')"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg
                                   bg-amber-50 text-amber-700 hover:bg-amber-100 transition-colors"
                        >
                            Edit
                        </button>';
                }

                if ($user->hasPermission('delete_kategori')) {
                    $hapusBtn = '
                        <button
                            onclick="hapusKategori(' . $row->id . ', \'' . addslashes($row->nama) . '\', ' . $row->menus_count . ')"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg
                                   bg-red-50 text-red-700 hover:bg-red-100 transition-colors"
                        >
                            Hapus
                        </button>';
                }

                return '<div class="flex items-center gap-2">' . $editBtn . $hapusBtn . '</div>';
            })
            ->rawColumns(['jumlah_menu', 'aksi'])
            ->make(true);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama'    => 'required|string|max:100|unique:kategori_menu,nama',
                'urutan'  => 'required|integer|min:0',
            ]);

            KategoriMenu::create([
                'nama'   => $request->nama,
                'slug'   => Str::slug($request->nama),
                'urutan' => $request->urutan,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kategori "' . $request->nama . '" berhasil ditambahkan.',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors(),
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan kategori. Silakan coba lagi.',
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'nama'   => 'required|string|max:100|unique:kategori_menu,nama,' . $id,
                'urutan' => 'required|integer|min:0',
            ]);

            $kategori = KategoriMenu::findOrFail($id);

            $kategori->update([
                'nama'   => $request->nama,
                'slug'   => Str::slug($request->nama),
                'urutan' => $request->urutan,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diperbarui.',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors(),
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui kategori. Silakan coba lagi.',
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $kategori = KategoriMenu::withCount('menus')->findOrFail($id);

            // Cegah hapus jika masih ada menu di kategori ini
            if ($kategori->menus_count > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori tidak bisa dihapus karena masih memiliki '
                        . $kategori->menus_count . ' menu. Pindahkan atau hapus menu terlebih dahulu.',
                ], 422);
            }

            $kategori->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kategori "' . $kategori->nama . '" berhasil dihapus.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kategori. Silakan coba lagi.',
            ], 500);
        }
    }
}