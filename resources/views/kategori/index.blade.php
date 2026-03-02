@extends('layouts.app')

@section('title', 'Kategori Menu')

@section('page-header')
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Kategori Menu</h2>
            <p class="text-sm text-gray-500 mt-0.5">Kelola pengelompokan menu Kopi Titik</p>
        </div>
        @if(auth()->user()->hasPermission('create_kategori'))
            <button onclick="bukaModalTambah()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-sky-500 hover:bg-sky-600
                           text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Kategori
            </button>
        @endif
    </div>
@endsection

@section('content')

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="tabel-kategori" class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-10">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Slug</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Urutan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah Menu</th>
                        @if(auth()->user()->hasPermission('edit_kategori') || auth()->user()->hasPermission('delete_kategori'))
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50"></tbody>
            </table>
        </div>
    </div>

    {{-- ============================================
         MODAL TAMBAH / EDIT KATEGORI
         ============================================ --}}
    <div id="modal-form"
         class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40"
         onclick="if(event.target===this) tutupModal()">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6">

            <div class="flex items-center justify-between mb-5">
                <h3 id="modal-judul" class="font-semibold text-gray-900 text-base">Tambah Kategori</h3>
                <button onclick="tutupModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="input-nama"
                       placeholder="Contoh: Coffee, Non Coffee, Cemilan"
                       maxlength="100"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm
                              focus:outline-none focus:border-sky-400 focus:ring-1 focus:ring-sky-100">
                <p id="error-nama" class="text-xs text-red-500 mt-1 hidden"></p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Urutan Tampil <span class="text-red-500">*</span>
                </label>
                <input type="number"
                       id="input-urutan"
                       min="0"
                       value="0"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm
                              focus:outline-none focus:border-sky-400 focus:ring-1 focus:ring-sky-100">
                <p class="text-xs text-gray-400 mt-1">Angka kecil tampil lebih dulu. Contoh: Coffee = 1, Non Coffee = 2</p>
                <p id="error-urutan" class="text-xs text-red-500 mt-1 hidden"></p>
            </div>

            <div class="flex gap-3">
                <button onclick="tutupModal()"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100
                               hover:bg-gray-200 rounded-lg transition-colors">
                    Batal
                </button>
                <button id="btn-simpan"
                        onclick="simpanKategori()"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-sky-500
                               hover:bg-sky-600 rounded-lg transition-colors">
                    Simpan
                </button>
            </div>

        </div>
    </div>

    {{-- ============================================
         MODAL KONFIRMASI HAPUS
         ============================================ --}}
    <div id="modal-hapus"
         class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40"
         onclick="if(event.target===this) tutupModalHapus()">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6">

            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 mx-auto mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>

            <h3 class="text-center font-semibold text-gray-900 mb-1">Hapus Kategori?</h3>
            <p class="text-center text-sm text-gray-500 mb-2">
                Kategori <span id="nama-kategori-hapus" class="font-semibold text-gray-800"></span>
                akan dihapus permanen.
            </p>
            <p id="warning-hapus" class="hidden text-center text-xs text-red-500 bg-red-50 rounded-lg px-3 py-2 mb-4"></p>

            <div class="flex gap-3 mt-4">
                <button onclick="tutupModalHapus()"
                        class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100
                               hover:bg-gray-200 rounded-lg transition-colors">
                    Batal
                </button>
                <button id="btn-konfirmasi-hapus"
                        class="flex-1 px-4 py-2 text-sm font-medium text-white bg-red-600
                               hover:bg-red-700 rounded-lg transition-colors">
                    Ya, Hapus
                </button>
            </div>

        </div>
    </div>

@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <style>
        /* ── Length + Filter ── */
        #tabel-kategori_wrapper .dataTables_length,
        #tabel-kategori_wrapper .dataTables_filter {
            padding: 1rem 1rem 0.5rem;
            font-size: 0.875rem;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        #tabel-kategori_wrapper .dataTables_filter {
            justify-content: flex-end;
        }

        /* Length select */
        #tabel-kategori_wrapper .dataTables_length select {
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 8px center;
            padding: 0.35rem 2rem 0.35rem 0.65rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 0.8125rem;
            color: #374151;
            background-color: #fff;
            cursor: pointer;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        #tabel-kategori_wrapper .dataTables_length select:focus {
            outline: none;
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15);
        }

        /* Search input */
        #tabel-kategori_wrapper .dataTables_filter input {
            padding: 0.35rem 0.75rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 0.8125rem;
            color: #374151;
            transition: border-color 0.15s, box-shadow 0.15s;
            width: 200px;
        }
        #tabel-kategori_wrapper .dataTables_filter input:focus {
            outline: none;
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15);
        }

        /* ── Info + Pagination ── */
        #tabel-kategori_wrapper .dataTables_info,
        #tabel-kategori_wrapper .dataTables_paginate {
            padding: 0.75rem 1rem;
            font-size: 0.8125rem;
            color: #6b7280;
        }
        #tabel-kategori_wrapper .dataTables_paginate {
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }
        #tabel-kategori_wrapper .dataTables_paginate span {
            display: inline-flex;
            gap: 0.25rem;
        }
        #tabel-kategori_wrapper .dataTables_paginate .paginate_button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            height: 2rem;
            padding: 0 0.5rem;
            border-radius: 0.5rem;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #374151;
            background: transparent;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.15s;
            line-height: 1;
        }
        #tabel-kategori_wrapper .dataTables_paginate .previous,
        #tabel-kategori_wrapper .dataTables_paginate .next {
            border: 1px solid #e5e7eb;
            background: #fff;
            margin: 0 0.25rem;
        }
        #tabel-kategori_wrapper .dataTables_paginate .previous:hover:not(.disabled),
        #tabel-kategori_wrapper .dataTables_paginate .next:hover:not(.disabled) {
            border-color: #38bdf8;
            color: #0284c7;
            background: #f0f9ff;
        }
        #tabel-kategori_wrapper .dataTables_paginate .previous.disabled,
        #tabel-kategori_wrapper .dataTables_paginate .next.disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }
        #tabel-kategori_wrapper .dataTables_paginate .paginate_button:not(.previous):not(.next):hover:not(.current) {
            background: #f0f9ff;
            color: #0284c7;
            border-color: #bae6fd;
        }
        #tabel-kategori_wrapper .dataTables_paginate .paginate_button.current,
        #tabel-kategori_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #0ea5e9 !important;
            color: #fff !important;
            border-color: #0ea5e9 !important;
            box-shadow: 0 1px 3px rgba(14, 165, 233, 0.35);
        }
        #tabel-kategori_wrapper .dataTables_paginate .ellipsis {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            height: 2rem;
            color: #9ca3af;
            font-size: 0.8125rem;
        }
        #tabel-kategori_wrapper .dataTables_processing {
            font-size: 0.8125rem;
            color: #6b7280;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <script>
        const csrfToken  = document.querySelector('meta[name="csrf-token"]').content;
        let table;
        let modeModal    = 'tambah';
        let idEditTarget = null;
        let idHapusTarget = null;

        $(document).ready(function () {
            table = $('#tabel-kategori').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('kategori.index') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nama',         name: 'nama' },
                    { data: 'slug',         name: 'slug' },
                    { data: 'urutan',       name: 'urutan' },
                    { data: 'jumlah_menu',  name: 'jumlah_menu', orderable: false, searchable: false },
                    @if(auth()->user()->hasPermission('edit_kategori') || auth()->user()->hasPermission('delete_kategori'))
                    { data: 'aksi', name: 'aksi', orderable: false, searchable: false },
                    @endif
                ],
                language: {
                    processing:  'Memuat data...',
                    search:      '',
                    searchPlaceholder: 'Cari kategori...',
                    lengthMenu:  'Tampilkan _MENU_ data',
                    info:        'Menampilkan _START_ – _END_ dari _TOTAL_ data',
                    infoEmpty:   'Tidak ada data',
                    zeroRecords: 'Tidak ada kategori yang cocok',
                    emptyTable:  'Belum ada kategori',
                    paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                },
                lengthMenu: [10, 25, 50, 100],
                order: [[3, 'asc']],
                drawCallback: function () {
                    $('#tabel-kategori tbody tr').addClass('hover:bg-gray-50 transition-colors');
                    $('#tabel-kategori tbody td').addClass('px-4 py-3 text-gray-700');
                }
            });
        });

        function bukaModalTambah() {
            modeModal    = 'tambah';
            idEditTarget = null;
            document.getElementById('modal-judul').textContent = 'Tambah Kategori';
            document.getElementById('btn-simpan').textContent  = 'Simpan';
            document.getElementById('input-nama').value        = '';
            document.getElementById('input-urutan').value      = '0';
            bersihkanError();
            bukaModal('modal-form');
            setTimeout(() => document.getElementById('input-nama').focus(), 100);
        }

        function editKategori(id, nama, urutan) {
            modeModal    = 'edit';
            idEditTarget = id;
            document.getElementById('modal-judul').textContent = 'Edit Kategori';
            document.getElementById('btn-simpan').textContent  = 'Simpan Perubahan';
            document.getElementById('input-nama').value        = nama;
            document.getElementById('input-urutan').value      = urutan;
            bersihkanError();
            bukaModal('modal-form');
            setTimeout(() => document.getElementById('input-nama').focus(), 100);
        }

        function simpanKategori() {
            const nama   = document.getElementById('input-nama').value.trim();
            const urutan = document.getElementById('input-urutan').value;
            const btn    = document.getElementById('btn-simpan');
            bersihkanError();

            if (!nama) { tampilError('error-nama', 'Nama kategori wajib diisi.'); return; }

            btn.textContent = 'Menyimpan...';
            btn.disabled    = true;

            const url    = modeModal === 'tambah' ? '{{ route('kategori.store') }}' : `/kategori/${idEditTarget}`;
            const method = modeModal === 'tambah' ? 'POST' : 'PUT';

            fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ nama, urutan }),
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    tutupModal();
                    table.ajax.reload(null, false);
                    tampilToast('success', data.message);
                } else {
                    if (data.errors) {
                        if (data.errors.nama)   tampilError('error-nama',   data.errors.nama[0]);
                        if (data.errors.urutan) tampilError('error-urutan', data.errors.urutan[0]);
                    } else {
                        tampilToast('error', data.message);
                    }
                }
            })
            .catch(() => tampilToast('error', 'Terjadi kesalahan. Coba lagi.'))
            .finally(() => {
                btn.textContent = modeModal === 'tambah' ? 'Simpan' : 'Simpan Perubahan';
                btn.disabled    = false;
            });
        }

        function hapusKategori(id, nama, jumlahMenu) {
            idHapusTarget = id;
            document.getElementById('nama-kategori-hapus').textContent = nama;

            const warning = document.getElementById('warning-hapus');
            if (jumlahMenu > 0) {
                warning.textContent = `Kategori ini masih memiliki ${jumlahMenu} menu dan tidak bisa dihapus.`;
                warning.classList.remove('hidden');
                document.getElementById('btn-konfirmasi-hapus').disabled = true;
                document.getElementById('btn-konfirmasi-hapus').classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                warning.classList.add('hidden');
                document.getElementById('btn-konfirmasi-hapus').disabled = false;
                document.getElementById('btn-konfirmasi-hapus').classList.remove('opacity-50', 'cursor-not-allowed');
            }
            bukaModal('modal-hapus');
        }

        function tutupModalHapus() {
            idHapusTarget = null;
            tutupModal('modal-hapus');
        }

        document.getElementById('btn-konfirmasi-hapus').addEventListener('click', function () {
            if (!idHapusTarget) return;
            const btn = this;
            btn.textContent = 'Menghapus...';
            btn.disabled    = true;

            fetch(`/kategori/${idHapusTarget}`, {
                method:  'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            })
            .then(res => res.json())
            .then(data => {
                tutupModalHapus();
                if (data.success) { table.ajax.reload(null, false); tampilToast('success', data.message); }
                else tampilToast('error', data.message);
            })
            .catch(() => tampilToast('error', 'Terjadi kesalahan. Coba lagi.'))
            .finally(() => { btn.textContent = 'Ya, Hapus'; btn.disabled = false; });
        });

        function bukaModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.getElementById(id).classList.add('flex');
        }
        function tutupModal(id = 'modal-form') {
            document.getElementById(id).classList.add('hidden');
            document.getElementById(id).classList.remove('flex');
        }
        function bersihkanError() {
            ['error-nama', 'error-urutan'].forEach(id => {
                const el = document.getElementById(id);
                if (el) { el.textContent = ''; el.classList.add('hidden'); }
            });
        }
        function tampilError(id, pesan) {
            const el = document.getElementById(id);
            if (el) { el.textContent = pesan; el.classList.remove('hidden'); }
        }
        ['input-nama', 'input-urutan'].forEach(id => {
            document.getElementById(id)?.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') simpanKategori();
            });
        });
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
    </script>
@endpush