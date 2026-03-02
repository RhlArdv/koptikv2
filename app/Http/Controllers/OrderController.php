<?php

namespace App\Http\Controllers;

use App\Models\KategoriMenu;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Halaman utama order pelanggan.
     * Tidak perlu auth — siapapun yang scan QR bisa akses.
     */
    public function index()
    {
        $kategoris = KategoriMenu::with(['menusAktif' => function ($q) {
            $q->orderBy('nama');
        }])->orderBy('urutan')->get();

        // Hanya tampilkan kategori yang punya menu aktif
        $kategoris = $kategoris->filter(fn($k) => $k->menusAktif->count() > 0)->values();

        return view('order.index', compact('kategoris'));
    }

    /**
     * Submit pesanan dari pelanggan.
     * Menerima data dari localStorage yang dikirim via form POST.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_pelanggan' => 'required|string|max:100',
                'nomor_meja'     => 'required|string|max:20',
                'catatan'        => 'nullable|string|max:300',
                'items'          => 'required|array|min:1',
                'items.*.menu_id'=> 'required|exists:menu,id',
                'items.*.qty'    => 'required|integer|min:1|max:99',
                'items.*.catatan_item' => 'nullable|string|max:200',
            ]);

            // Validasi stok & hitung total
            $items      = [];
            $totalHarga = 0;

            foreach ($request->items as $item) {
                $menu = Menu::where('id', $item['menu_id'])
                    ->where('is_aktif', true)
                    ->first();

                if (!$menu) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Salah satu menu tidak tersedia. Silakan refresh halaman.',
                    ], 422);
                }

                if ($menu->stok < $item['qty']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok "' . $menu->nama . '" tidak mencukupi. '
                            . 'Tersisa ' . $menu->stok . ' porsi.',
                    ], 422);
                }

                $subtotal     = $menu->harga * $item['qty'];
                $totalHarga  += $subtotal;

                $items[] = [
                    'menu'         => $menu,
                    'qty'          => $item['qty'],
                    'catatan_item' => $item['catatan_item'] ?? null,
                    'subtotal'     => $subtotal,
                ];
            }

            // Simpan ke database dalam satu transaksi
            DB::transaction(function () use ($request, $items, $totalHarga) {

                $pesanan = Pesanan::create([
                    'kode_pesanan'      => Pesanan::generateKode(),
                    'nama_pelanggan'    => $request->nama_pelanggan,
                    'nomor_meja'        => $request->nomor_meja,
                    'catatan'           => $request->catatan,
                    'total_harga'       => $totalHarga,
                    'status_pesanan'    => 'menunggu',
                    'status_pembayaran' => 'belum_bayar',
                ]);

                foreach ($items as $item) {
                    DetailPesanan::create([
                        'pesanan_id'            => $pesanan->id,
                        'menu_id'               => $item['menu']->id,
                        'nama_menu_saat_pesan'  => $item['menu']->nama,
                        'harga_saat_pesan'      => $item['menu']->harga,
                        'qty'                   => $item['qty'],
                        'subtotal'              => $item['subtotal'],
                        'catatan_item'          => $item['catatan_item'],
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dikirim! Silakan tunggu pesanan kamu diproses.',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim pesanan. Silakan coba lagi.',
            ], 500);
        }
    }
}