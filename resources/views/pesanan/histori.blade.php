@extends('layouts.app')

@section('title', 'Histori Pesanan')

@section('page-header')
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Histori Pesanan</h2>
            <p class="text-sm text-gray-500 mt-0.5">Semua transaksi pesanan Kopi Titik</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-2 text-right">
            <p class="text-xs text-green-600">Total Omzet (filter aktif)</p>
            <p class="text-lg font-bold text-green-800">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</p>
        </div>
    </div>
@endsection

@section('content')

    {{-- Filter --}}
    <form method="GET" action="{{ route('pesanan.histori') }}"
          class="bg-white rounded-xl border border-gray-200 p-4 mb-5">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Dari Tanggal</label>
                <input type="date" name="tanggal_dari"
                       value="{{ request('tanggal_dari') }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:border-amber-400">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Sampai Tanggal</label>
                <input type="date" name="tanggal_sampai"
                       value="{{ request('tanggal_sampai') }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:border-amber-400">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Status Pembayaran</label>
                <div class="flex gap-2">
                    <select name="status_pembayaran"
                            class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm
                                   focus:outline-none focus:border-amber-400">
                        <option value="">Semua</option>
                        <option value="belum_bayar" {{ request('status_pembayaran') === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                        <option value="lunas"       {{ request('status_pembayaran') === 'lunas'       ? 'selected' : '' }}>Lunas</option>
                    </select>
                    <button type="submit"
                            class="px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white
                                   rounded-lg text-sm font-medium transition-colors">
                        Filter
                    </button>
                </div>
            </div>

        </div>
        @if(request()->hasAny(['tanggal_dari', 'tanggal_sampai', 'status_pembayaran']))
            <div class="mt-3 pt-3 border-t border-gray-100">
                <a href="{{ route('pesanan.histori') }}"
                   class="text-xs text-amber-600 hover:text-amber-700 font-medium">
                    ↩ Reset filter
                </a>
            </div>
        @endif
    </form>

    {{-- Tabel histori --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Meja</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Bayar</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kasir</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($pesanans as $pesanan)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-mono text-xs font-semibold text-gray-700">{{ $pesanan->kode_pesanan }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $pesanan->nama_pelanggan }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $pesanan->nomor_meja }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-800">{{ $pesanan->total_format }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                             {{ $pesanan->status_bayar_badge }}">
                                    {{ $pesanan->status_pembayaran === 'lunas' ? 'Lunas' : 'Belum Bayar' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $pesanan->kasir?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-400 text-xs">
                                {{ $pesanan->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                                Tidak ada pesanan ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pesanans->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $pesanans->links() }}
            </div>
        @endif
    </div>

@endsection