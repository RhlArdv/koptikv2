@extends('layouts.app')

@section('title', 'Stok Menu')

@section('page-header')
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Stok Menu</h2>
            <p class="text-sm text-gray-500 mt-0.5">Isi ulang stok menu harian Kopi Titik</p>
        </div>
        {{-- Ringkasan cepat --}}
        <div class="hidden sm:flex items-center gap-3">
            <div class="text-right">
                <p class="text-xs text-gray-400">Stok Habis</p>
                <p class="text-lg font-bold text-red-600" id="count-habis">
                    {{ $kategoris->flatMap->menus->where('stok', 0)->count() }}
                </p>
            </div>
            <div class="w-px h-8 bg-gray-200"></div>
            <div class="text-right">
                <p class="text-xs text-gray-400">Hampir Habis (≤5)</p>
                <p class="text-lg font-bold text-yellow-600" id="count-tipis">
                    {{ $kategoris->flatMap->menus->where('stok', '>', 0)->where('stok', '<=', 5)->count() }}
                </p>
            </div>
        </div>
    </div>
@endsection

@section('content')

    {{-- Filter cepat --}}
    <div class="flex flex-wrap gap-2 mb-5">
        <button onclick="filterStok('semua')"
                id="filter-semua"
                class="filter-stok-btn aktif px-3 py-1.5 text-xs font-medium rounded-lg border
                       border-sky-500 bg-sky-500 text-white transition-colors">
            Semua Menu
        </button>
        <button onclick="filterStok('habis')"
                id="filter-habis"
                class="filter-stok-btn px-3 py-1.5 text-xs font-medium rounded-lg border
                       border-gray-200 text-gray-600 hover:border-red-400 hover:text-red-600 transition-colors">
            Stok Habis
        </button>
        <button onclick="filterStok('tipis')"
                id="filter-tipis"
                class="filter-stok-btn px-3 py-1.5 text-xs font-medium rounded-lg border
                       border-gray-200 text-gray-600 hover:border-yellow-400 hover:text-yellow-600 transition-colors">
            Hampir Habis
        </button>
    </div>

    {{-- Daftar menu per kategori --}}
    <div class="space-y-5" id="wrapper-kategori">

        @forelse($kategoris as $kategori)
            @if($kategori->menus->count() > 0)

                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden kategori-section"
                     data-kategori="{{ $kategori->id }}">

                    <div class="px-5 py-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-700 text-sm">{{ $kategori->nama }}</h3>
                        <span class="text-xs text-gray-400">{{ $kategori->menus->count() }} item</span>
                    </div>

                    <div class="divide-y divide-gray-50">
                        @foreach($kategori->menus as $menu)
                            <div class="menu-row flex items-center gap-4 px-5 py-4"
                                 data-id="{{ $menu->id }}"
                                 data-stok="{{ $menu->stok }}"
                                 data-nama="{{ $menu->nama }}">

                                <div class="flex-shrink-0">
                                    <img src="{{ $menu->gambar_url }}"
                                         alt="{{ $menu->nama }}"
                                         class="w-11 h-11 rounded-lg object-cover border border-gray-100">
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ $menu->nama }}</p>
                                    <p class="text-xs text-gray-400">{{ $menu->harga_format }}</p>
                                </div>

                                <div class="flex-shrink-0 text-center min-w-[72px]">
                                    <p class="text-xs text-gray-400 mb-0.5">Stok</p>
                                    @include('stok._badge_stok', ['stok' => $menu->stok, 'menuId' => $menu->id])
                                </div>

                                <div class="flex-shrink-0 flex items-center gap-2">
                                    <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                                        <button type="button"
                                                onclick="ubahInput({{ $menu->id }}, -1)"
                                                class="px-2.5 py-2 text-gray-400 hover:bg-gray-50 hover:text-gray-600
                                                       transition-colors text-base leading-none">
                                            −
                                        </button>
                                        <input type="number"
                                               id="input-stok-{{ $menu->id }}"
                                               value="1"
                                               min="1"
                                               max="9999"
                                               class="w-14 text-center text-sm border-x border-gray-200 py-2
                                                      focus:outline-none focus:bg-sky-50">
                                        <button type="button"
                                                onclick="ubahInput({{ $menu->id }}, 1)"
                                                class="px-2.5 py-2 text-gray-400 hover:bg-gray-50 hover:text-gray-600
                                                       transition-colors text-base leading-none">
                                            +
                                        </button>
                                    </div>

                                    <button type="button"
                                            onclick="tambahStok({{ $menu->id }})"
                                            id="btn-tambah-{{ $menu->id }}"
                                            class="px-3 py-2 text-xs font-medium text-white bg-sky-500
                                                   hover:bg-sky-600 rounded-lg transition-colors whitespace-nowrap">
                                        + Tambah
                                    </button>

                                    <button type="button"
                                            onclick="bukaModalSet({{ $menu->id }}, '{{ addslashes($menu->nama) }}', {{ $menu->stok }})"
                                            class="px-3 py-2 text-xs font-medium text-gray-600 bg-gray-100
                                                   hover:bg-gray-200 rounded-lg transition-colors whitespace-nowrap">
                                        Set
                                    </button>
                                </div>

                            </div>
                        @endforeach
                    </div>

                </div>

            @endif
        @empty
            <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                <p class="text-gray-400">Belum ada menu tersedia.</p>
                <a href="{{ route('menu.create') }}" class="text-sky-600 text-sm hover:underline mt-1 inline-block">
                    Tambah menu sekarang →
                </a>
            </div>
        @endforelse

    </div>

    {{-- ============================================
         MODAL SET STOK
         ============================================ --}}
    <div id="modal-set"
         class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40"
         onclick="if(event.target===this) tutupModalSet()">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6">

            <h3 class="font-semibold text-gray-900 mb-1">Set Stok Menu</h3>
            <p class="text-sm text-gray-500 mb-1">
                <span id="modal-set-nama" class="font-medium text-gray-800"></span>
            </p>
            <p class="text-xs text-gray-400 mb-4">
                Stok saat ini: <span id="modal-set-stok-lama" class="font-semibold text-gray-700"></span>
                <br>Masukkan angka baru untuk mengganti stok secara langsung.
            </p>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Stok Baru</label>
                <input type="number"
                       id="input-set-stok"
                       min="0"
                       max="9999"
                       placeholder="0"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm
                              focus:outline-none focus:border-sky-400 focus:ring-1 focus:ring-sky-100">
            </div>

            <div class="flex gap-3">
                <button onclick="tutupModalSet()"
                        class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100
                               hover:bg-gray-200 rounded-lg transition-colors">
                    Batal
                </button>
                <button onclick="setStok()"
                        id="btn-set-stok"
                        class="flex-1 px-4 py-2 text-sm font-medium text-white bg-sky-500
                               hover:bg-sky-600 rounded-lg transition-colors">
                    Set Stok
                </button>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let menuIdSet   = null;

    function filterStok(tipe) {
        document.querySelectorAll('.filter-stok-btn').forEach(btn => {
            btn.classList.remove('bg-sky-500', 'text-white', 'border-sky-500');
            btn.classList.add('border-gray-200', 'text-gray-600');
        });
        document.getElementById('filter-' + tipe).classList.add('bg-sky-500', 'text-white', 'border-sky-500');
        document.getElementById('filter-' + tipe).classList.remove('border-gray-200', 'text-gray-600');

        document.querySelectorAll('.menu-row').forEach(row => {
            const stok = parseInt(row.dataset.stok);
            let tampil = true;
            if (tipe === 'habis') tampil = stok === 0;
            if (tipe === 'tipis') tampil = stok > 0 && stok <= 5;
            row.style.display = tampil ? '' : 'none';
        });

        document.querySelectorAll('.kategori-section').forEach(section => {
            const adaYangTampil = [...section.querySelectorAll('.menu-row')]
                .some(row => row.style.display !== 'none');
            section.style.display = adaYangTampil ? '' : 'none';
        });
    }

    function ubahInput(id, delta) {
        const input = document.getElementById('input-stok-' + id);
        const nilai = Math.max(1, (parseInt(input.value) || 1) + delta);
        input.value = nilai;
    }

    function tambahStok(id) {
        const input  = document.getElementById('input-stok-' + id);
        const jumlah = parseInt(input.value);
        const btn    = document.getElementById('btn-tambah-' + id);

        if (!jumlah || jumlah < 1) { tampilToast('error', 'Jumlah harus minimal 1.'); return; }

        btn.textContent = '...';
        btn.disabled    = true;

        fetch(`/stok/${id}/tambah`, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ jumlah }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateBadgeStok(id, data.stok_baru);
                input.value = 1;
                tampilToast('success', data.message);
                updateCounterRingkasan();
            } else {
                tampilToast('error', data.message);
            }
        })
        .catch(() => tampilToast('error', 'Terjadi kesalahan. Coba lagi.'))
        .finally(() => { btn.textContent = '+ Tambah'; btn.disabled = false; });
    }

    function bukaModalSet(id, nama, stokLama) {
        menuIdSet = id;
        document.getElementById('modal-set-nama').textContent      = nama;
        document.getElementById('modal-set-stok-lama').textContent = stokLama;
        document.getElementById('input-set-stok').value            = stokLama;
        document.getElementById('modal-set').classList.remove('hidden');
        document.getElementById('modal-set').classList.add('flex');
        setTimeout(() => document.getElementById('input-set-stok').focus(), 100);
    }

    function tutupModalSet() {
        menuIdSet = null;
        document.getElementById('modal-set').classList.add('hidden');
        document.getElementById('modal-set').classList.remove('flex');
    }

    function setStok() {
        if (!menuIdSet) return;
        const stok = parseInt(document.getElementById('input-set-stok').value);
        const btn  = document.getElementById('btn-set-stok');

        if (isNaN(stok) || stok < 0) { tampilToast('error', 'Stok tidak boleh negatif.'); return; }

        btn.textContent = 'Menyimpan...';
        btn.disabled    = true;

        fetch(`/stok/${menuIdSet}/set`, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ stok }),
        })
        .then(res => res.json())
        .then(data => {
            tutupModalSet();
            if (data.success) {
                updateBadgeStok(menuIdSet, data.stok_baru);
                tampilToast('success', data.message);
                updateCounterRingkasan();
            } else {
                tampilToast('error', data.message);
            }
        })
        .catch(() => tampilToast('error', 'Terjadi kesalahan. Coba lagi.'))
        .finally(() => { btn.textContent = 'Set Stok'; btn.disabled = false; });
    }

    function updateBadgeStok(id, stokBaru) {
        const row = document.querySelector(`.menu-row[data-id="${id}"]`);
        if (!row) return;
        row.dataset.stok = stokBaru;
        const badgeContainer = row.querySelector('.badge-stok-container');
        if (badgeContainer) badgeContainer.innerHTML = renderBadge(stokBaru);
    }

    function renderBadge(stok) {
        if (stok === 0) {
            return `<span class="badge-stok inline-flex items-center justify-center min-w-[52px] px-2.5 py-1
                          rounded-full text-xs font-bold bg-red-100 text-red-700">Habis</span>`;
        } else if (stok <= 5) {
            return `<span class="badge-stok inline-flex items-center justify-center min-w-[52px] px-2.5 py-1
                          rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">${stok}</span>`;
        }
        return `<span class="badge-stok inline-flex items-center justify-center min-w-[52px] px-2.5 py-1
                      rounded-full text-xs font-bold bg-green-100 text-green-700">${stok}</span>`;
    }

    function updateCounterRingkasan() {
        const rows = document.querySelectorAll('.menu-row');
        let habis = 0, tipis = 0;
        rows.forEach(row => {
            const stok = parseInt(row.dataset.stok);
            if (stok === 0) habis++;
            else if (stok <= 5) tipis++;
        });
        const elHabis = document.getElementById('count-habis');
        const elTipis = document.getElementById('count-tipis');
        if (elHabis) elHabis.textContent = habis;
        if (elTipis) elTipis.textContent = tipis;
    }

    function tampilToast(tipe, pesan) {
        const warna = tipe === 'success'
            ? 'bg-green-50 border-green-200 text-green-800'
            : 'bg-red-50 border-red-200 text-red-800';
        const toast = document.createElement('div');
        toast.className = `fixed bottom-6 right-6 z-50 flex items-center gap-2 px-4 py-3
                           rounded-xl border shadow-lg text-sm font-medium max-w-sm ${warna}`;
        toast.textContent = pesan;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }

    document.getElementById('input-set-stok')?.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') setStok();
    });
</script>
@endpush