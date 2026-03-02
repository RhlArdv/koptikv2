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

    {{-- Search bar --}}
    <div class="mb-4">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text"
                   id="search-menu"
                   placeholder="Cari menu..."
                   class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-lg
                          focus:outline-none focus:border-sky-400 focus:ring-1 focus:ring-sky-100">
        </div>
    </div>

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
    <div class="space-y-6" id="wrapper-kategori">

        @forelse($kategoris as $kategori)
            @if($kategori->menus->count() > 0)

                {{-- Header kategori --}}
                <div class="kategori-section" data-kategori="{{ $kategori->id }}">
                    <div class="flex items-center gap-3 mb-3">
                        <h3 class="font-semibold text-gray-800">{{ $kategori->nama }}</h3>
                        <span class="text-xs text-gray-400">{{ $kategori->menus->count() }} item</span>
                    </div>

                    {{-- Grid card menu --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach($kategori->menus as $menu)
                            <div class="menu-card bg-white rounded-xl border border-gray-200 overflow-hidden
                                        hover:shadow-md hover:border-gray-300 transition-all"
                                 data-id="{{ $menu->id }}"
                                 data-stok="{{ $menu->stok }}"
                                 data-nama="{{ $menu->nama }}">

                                {{-- Gambar & Badge Stok --}}
                                <div class="relative h-36 bg-gray-100">
                                    <img src="{{ $menu->gambar_url }}"
                                         alt="{{ $menu->nama }}"
                                         class="w-full h-full object-cover">
                                    {{-- Badge stok overlay --}}
                                    <div class="absolute top-2 right-2">
                                        @if($menu->stok === 0)
                                            <span class="px-2 py-1 text-xs font-bold bg-red-500 text-white rounded-full">
                                                Habis
                                            </span>
                                        @elseif($menu->stok <= 5)
                                            <span class="px-2 py-1 text-xs font-bold bg-yellow-500 text-white rounded-full">
                                                {{ $menu->stok }}
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-bold bg-green-500 text-white rounded-full">
                                                {{ $menu->stok }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Info menu --}}
                                <div class="p-3">
                                    <h4 class="font-medium text-gray-800 text-sm truncate">{{ $menu->nama }}</h4>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $menu->harga_format }}</p>

                                    {{-- Input dan tombol aksi --}}
                                    <div class="mt-3 space-y-2">
                                        {{-- Input jumlah --}}
                                        <div class="flex items-center justify-center border border-gray-200 rounded-lg overflow-hidden">
                                            <button type="button"
                                                    onclick="ubahInput({{ $menu->id }}, -1)"
                                                    class="px-3 py-1.5 text-gray-400 hover:bg-gray-50 hover:text-gray-600
                                                           transition-colors text-sm">
                                                −
                                            </button>
                                            <input type="number"
                                                   id="input-stok-{{ $menu->id }}"
                                                   value="1"
                                                   min="1"
                                                   max="9999"
                                                   class="w-16 text-center text-sm border-x border-gray-200 py-1.5
                                                          focus:outline-none focus:bg-sky-50">
                                            <button type="button"
                                                    onclick="ubahInput({{ $menu->id }}, 1)"
                                                    class="px-3 py-1.5 text-gray-400 hover:bg-gray-50 hover:text-gray-600
                                                           transition-colors text-sm">
                                                +
                                            </button>
                                        </div>

                                        {{-- Tombol aksi --}}
                                        <div class="flex gap-2">
                                            <button type="button"
                                                    onclick="tambahStok({{ $menu->id }})"
                                                    id="btn-tambah-{{ $menu->id }}"
                                                    class="flex-1 px-3 py-2 text-xs font-medium text-white bg-sky-500
                                                           hover:bg-sky-600 rounded-lg transition-colors">
                                                + Tambah
                                            </button>
                                            <button type="button"
                                                    onclick="bukaModalSet({{ $menu->id }}, '{{ addslashes($menu->nama) }}', {{ $menu->stok }})"
                                                    class="flex-1 px-3 py-2 text-xs font-medium text-gray-600 bg-gray-100
                                                           hover:bg-gray-200 rounded-lg transition-colors">
                                                Set
                                            </button>
                                        </div>
                                    </div>
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

    {{-- Pesan jika tidak ada hasil pencarian/filter --}}
    <div id="no-results" class="hidden bg-white rounded-xl border border-gray-200 p-12 text-center">
        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-gray-400">Tidak ada menu yang ditemukan.</p>
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
    let currentFilter = 'semua';
    let searchQuery  = '';

    // Fungsi search
    document.getElementById('search-menu')?.addEventListener('input', function(e) {
        searchQuery = e.target.value.toLowerCase();
        applyFilters();
    });

    function filterStok(tipe) {
        currentFilter = tipe;
        document.querySelectorAll('.filter-stok-btn').forEach(btn => {
            btn.classList.remove('bg-sky-500', 'text-white', 'border-sky-500');
            btn.classList.add('border-gray-200', 'text-gray-600');
        });
        document.getElementById('filter-' + tipe).classList.add('bg-sky-500', 'text-white', 'border-sky-500');
        document.getElementById('filter-' + tipe).classList.remove('border-gray-200', 'text-gray-600');

        applyFilters();
    }

    function applyFilters() {
        const cards = document.querySelectorAll('.menu-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const stok = parseInt(card.dataset.stok);
            const nama = card.dataset.nama.toLowerCase();

            // Filter berdasarkan stok
            let passFilter = true;
            if (currentFilter === 'habis') passFilter = stok === 0;
            if (currentFilter === 'tipis') passFilter = stok > 0 && stok <= 5;

            // Filter berdasarkan search
            let passSearch = true;
            if (searchQuery) {
                passSearch = nama.includes(searchQuery);
            }

            const shouldShow = passFilter && passSearch;
            card.style.display = shouldShow ? '' : 'none';
            if (shouldShow) visibleCount++;
        });

        // Show/hide kategori sections dan no results
        document.querySelectorAll('.kategori-section').forEach(section => {
            const adaYangTampil = [...section.querySelectorAll('.menu-card')]
                .some(card => card.style.display !== 'none');
            section.style.display = adaYangTampil ? '' : 'none';
        });

        // Show/hide no results message
        const noResults = document.getElementById('no-results');
        if (noResults) {
            noResults.classList.toggle('hidden', visibleCount > 0);
        }
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
        const card = document.querySelector(`.menu-card[data-id="${id}"]`);
        if (!card) return;
        card.dataset.stok = stokBaru;

        // Update badge overlay pada gambar
        const imgContainer = card.querySelector('.relative.h-36');
        if (imgContainer) {
            const existingBadge = imgContainer.querySelector('.absolute.top-2.right-2');
            if (existingBadge) {
                let badgeClass, badgeText;
                if (stokBaru === 0) {
                    badgeClass = 'bg-red-500';
                    badgeText = 'Habis';
                } else if (stokBaru <= 5) {
                    badgeClass = 'bg-yellow-500';
                    badgeText = stokBaru;
                } else {
                    badgeClass = 'bg-green-500';
                    badgeText = stokBaru;
                }
                existingBadge.className = `absolute top-2 right-2`;
                existingBadge.innerHTML = `<span class="px-2 py-1 text-xs font-bold ${badgeClass} text-white rounded-full">${badgeText}</span>`;
            }
        }

        // Re-apply filters setelah update stok
        applyFilters();
    }

    function updateCounterRingkasan() {
        const cards = document.querySelectorAll('.menu-card');
        let habis = 0, tipis = 0;
        cards.forEach(card => {
            const stok = parseInt(card.dataset.stok);
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