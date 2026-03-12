<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
// use App\Models\DetailPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PesananController extends Controller
{
    /**
     * Halaman utama pesanan — card view dengan filter.
     * Semua role yang punya view_pesanan bisa akses,
     * tapi tombol aksi muncul sesuai permission masing-masing.
     */
    public function index(Request $request)
    {
        $query = Pesanan::with(['details.menu', 'kasir'])
            ->orderByRaw("FIELD(status_pesanan, 'menunggu', 'diproses', 'selesai')")
            ->orderByDesc('created_at');

        // Filter by status pesanan
        if ($request->filled('status_pesanan')) {
            $query->where('status_pesanan', $request->status_pesanan);
        }

        // Filter by status pembayaran
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        // Filter by tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        } else {
            // Default: tampilkan hari ini
            $query->whereDate('created_at', today());
        }

        // Filter by nomor meja
        if ($request->filled('nomor_meja')) {
            $query->where('nomor_meja', 'like', '%' . $request->nomor_meja . '%');
        }

        $pesanans = $query->paginate(12)->withQueryString();

        // Hitung ringkasan untuk badge counter di filter
        $ringkasan = [
            'menunggu'   => Pesanan::whereDate('created_at', today())->where('status_pesanan', 'menunggu')->count(),
            'diproses'   => Pesanan::whereDate('created_at', today())->where('status_pesanan', 'diproses')->count(),
            'selesai'    => Pesanan::whereDate('created_at', today())->where('status_pesanan', 'selesai')->count(),
            'belum_bayar'=> Pesanan::whereDate('created_at', today())->where('status_pembayaran', 'belum_bayar')->count(),
        ];

        return view('pesanan.index', compact('pesanans', 'ringkasan'));
    }

    /**
     * Detail pesanan — return JSON untuk ditampilkan di modal.
     */
    public function show($id)
    {
        $pesanan = Pesanan::with(['details.menu.kategori', 'kasir'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                  => $pesanan->id,
                'kode_pesanan'        => $pesanan->kode_pesanan,
                'nama_pelanggan'      => $pesanan->nama_pelanggan,
                'nomor_meja'          => $pesanan->nomor_meja,
                'catatan'             => $pesanan->catatan,
                'total_harga'         => $pesanan->total_harga,
                'total_format'        => $pesanan->total_format,
                'status_pesanan'      => $pesanan->status_pesanan,
                'status_pesanan_label'=> $pesanan->status_pesanan_label,
                'status_pembayaran'   => $pesanan->status_pembayaran,
                'metode_pembayaran'   => $pesanan->metode_pembayaran,
                'nominal_bayar'       => $pesanan->nominal_bayar,
                'kembalian'           => $pesanan->kembalian,
                'waktu_bayar'         => $pesanan->waktu_bayar?->format('d/m/Y H:i'),
                'kasir_nama'          => $pesanan->kasir?->name,
                'created_at'          => $pesanan->created_at->format('d/m/Y H:i'),
                'details'             => $pesanan->details->map(fn($d) => [
                    'nama'         => $d->nama_menu_saat_pesan,
                    'qty'          => $d->qty,
                    'harga'        => $d->harga_format,
                    'subtotal'     => $d->subtotal_format,
                    'catatan_item' => $d->catatan_item,
                ]),
            ],
        ]);
    }

    /**
     * Update status pesanan (menunggu → diproses → selesai).
     * Hanya yang punya permission: proses_pesanan (head bar & admin).
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:menunggu,diproses,selesai',
            ]);

            $pesanan = Pesanan::findOrFail($id);
            $pesanan->update(['status_pesanan' => $request->status]);

            return response()->json([
                'success'        => true,
                'message'        => 'Status pesanan ' . $pesanan->kode_pesanan . ' diperbarui.',
                'status_baru'    => $pesanan->status_pesanan,
                'label_baru'     => $pesanan->status_pesanan_label,
                'badge_baru'     => $pesanan->status_pesanan_badge,
            ]);

        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui status.'], 500);
        }
    }

    /**
     * Konfirmasi pembayaran — cash atau qris.
     * Hanya yang punya permission: konfirmasi_pembayaran (kasir & admin).
     */
    public function konfirmasiBayar(Request $request, $id)
    {
        try {
            $request->validate([
                'metode_pembayaran' => 'required|in:cash,qris',
                'nominal_bayar'     => 'required_if:metode_pembayaran,cash|nullable|numeric|min:0',
            ]);

            $pesanan = Pesanan::findOrFail($id);

            if ($pesanan->status_pembayaran === 'lunas') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan ini sudah lunas.',
                ], 422);
            }

            $nominalBayar = null;
            $kembalian    = null;

            if ($request->metode_pembayaran === 'cash') {
                $nominalBayar = $request->nominal_bayar;

                if ($nominalBayar < $pesanan->total_harga) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nominal bayar kurang dari total tagihan (Rp ' .
                            number_format($pesanan->total_harga, 0, ',', '.') . ').',
                    ], 422);
                }

                $kembalian = $nominalBayar - $pesanan->total_harga;
            }

            $pesanan->update([
                'status_pembayaran'  => 'lunas',
                'metode_pembayaran'  => $request->metode_pembayaran,
                'nominal_bayar'      => $nominalBayar,
                'kembalian'          => $kembalian,
                'kasir_id'           => Auth::id(),
                'waktu_bayar'        => now(),
                // Auto selesaikan pesanan jika belum selesai
                'status_pesanan'     => $pesanan->status_pesanan === 'selesai' ? 'selesai' : 'selesai',
            ]);

            // Reload pesanan dengan relations untuk struk
            $pesanan->load(['details.menu', 'kasir']);

            return response()->json([
                'success'      => true,
                'message'      => 'Pembayaran pesanan ' . $pesanan->kode_pesanan . ' berhasil dikonfirmasi.',
                'print_struk'  => true,
                'kembalian'    => $kembalian,
                'kembalian_format' => $kembalian !== null
                    ? 'Rp ' . number_format($kembalian, 0, ',', '.')
                    : null,
                'struk'        => [
                    'toko'         => [
                        'nama'    => config('app.name', 'Koptik'),
                        'alamat'  => ' Jl. Bougenville No. 17, 
Flamboyan, Kota Padang, Sumatera Barat.',
                        'telepon' => 'kopitik.com',
                    ],
                    'pesanan'     => [
                        'kode'           => $pesanan->kode_pesanan,
                        'tanggal'        => $pesanan->created_at->format('d/m/Y H:i'),
                        'nama_pelanggan' => $pesanan->nama_pelanggan,
                        'nomor_meja'     => $pesanan->nomor_meja,
                    ],
                    'items'       => $pesanan->details->map(fn($d) => [
                        'nama'     => $d->nama_menu_saat_pesan,
                        'qty'      => $d->qty,
                        'harga'    => $d->harga_format,
                        'subtotal' => $d->subtotal_format,
                    ]),
                    'pembayaran'  => [
                        'total'            => $pesanan->total_format,
                        'metode'           => $request->metode_pembayaran,
                        'nominal_bayar'    => $nominalBayar !== null
                            ? 'Rp ' . number_format($nominalBayar, 0, ',', '.')
                            : null,
                        'kembalian'        => $kembalian !== null
                            ? 'Rp ' . number_format($kembalian, 0, ',', '.')
                            : null,
                        'kasir'            => $pesanan->kasir?->name ?? '-',
                        'waktu_bayar'      => $pesanan->waktu_bayar->format('d/m/Y H:i'),
                    ],
                ],
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal konfirmasi pembayaran.'], 500);
        }
    }

    /**
     * Hapus pesanan — hanya admin.
     */
    public function destroy($id)
    {
        try {
            $pesanan = Pesanan::findOrFail($id);
            $kode    = $pesanan->kode_pesanan;
            $pesanan->delete(); // detail_pesanan ikut terhapus (cascade)

            return response()->json([
                'success' => true,
                'message' => 'Pesanan ' . $kode . ' berhasil dihapus.',
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus pesanan.'], 500);
        }
    }

    /**
     * Histori pesanan — semua tanggal, untuk laporan/review.
     * Permission: view_histori_pesanan
     */
    public function histori(Request $request)
    {
        $query = Pesanan::with(['kasir'])
            ->orderByDesc('created_at');

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        $pesanans = $query->paginate(20)->withQueryString();

        $totalOmzet = Pesanan::lunas()
            ->when($request->filled('tanggal_dari'), fn($q) => $q->whereDate('created_at', '>=', $request->tanggal_dari))
            ->when($request->filled('tanggal_sampai'), fn($q) => $q->whereDate('created_at', '<=', $request->tanggal_sampai))
            ->sum('total_harga');

        return view('pesanan.histori', compact('pesanans', 'totalOmzet'));
    }
}