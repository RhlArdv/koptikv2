<?php

namespace App\Http\Controllers;

use App\Models\KategoriMenu;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StokController extends Controller
{
    /**
     * Halaman utama stok — tampil semua menu dikelompokkan per kategori.
     * Head bar bisa isi ulang stok langsung dari halaman ini.
     */
    public function index()
    {
        $kategoris = KategoriMenu::with(['menus' => function ($q) {
            $q->orderBy('nama');
        }])->orderBy('urutan')->get();

        return view('stok.index', compact('kategoris'));
    }

    /**
     * Tambah stok menu tertentu.
     * Dipanggil via AJAX dari halaman stok.
     *
     * Logic: stok baru = stok lama + jumlah yang ditambahkan
     * (bukan replace, tapi penambahan — karena head bar isi ulang tiap hari)
     */
    public function tambah(Request $request, $id)
    {
        try {
            $request->validate([
                'jumlah' => 'required|integer|min:1|max:9999',
            ]);

            $menu = Menu::findOrFail($id);

            $stokLama  = $menu->stok;
            $menu->stok = $stokLama + $request->jumlah;
            $menu->save();

            return response()->json([
                'success'   => true,
                'message'   => 'Stok "' . $menu->nama . '" berhasil ditambah ' . $request->jumlah . ' porsi.',
                'stok_baru' => $menu->stok,
                'stok_lama' => $stokLama,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui stok. Silakan coba lagi.',
            ], 500);
        }
    }

    /**
     * Set stok menu ke angka tertentu (override/reset).
     * Digunakan jika head bar ingin set ulang stok dari awal,
     * bukan menambah dari stok yang ada.
     */
    public function set(Request $request, $id)
    {
        try {
            $request->validate([
                'stok' => 'required|integer|min:0|max:9999',
            ]);

            $menu = Menu::findOrFail($id);

            $stokLama  = $menu->stok;
            $menu->stok = $request->stok;
            $menu->save();

            return response()->json([
                'success'   => true,
                'message'   => 'Stok "' . $menu->nama . '" berhasil diset ke ' . $request->stok . ' porsi.',
                'stok_baru' => $menu->stok,
                'stok_lama' => $stokLama,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah stok. Silakan coba lagi.',
            ], 500);
        }
    }
}