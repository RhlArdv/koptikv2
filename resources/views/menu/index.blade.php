@extends('layouts.app')

@section('title', 'Kelola Menu')

@section('page-header')
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Kelola Menu</h2>
            <p class="text-sm text-gray-500 mt-0.5">Daftar semua menu yang tersedia di Kopi Titik</p>
        </div>
        @if(auth()->user()->hasPermission('create_menu'))
            <a href="{{ route('menu.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-sky-500 hover:bg-sky-600
                      text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Menu
            </a>
        @endif
    </div>
@endsection

@section('content')

    {{-- Filter Kategori --}}
    <div class="flex flex-wrap gap-2 mb-4">
        <button onclick="filterKategori('')"
                id="filter-all"
                class="filter-btn px-3 py-1.5 text-xs font-medium rounded-lg border border-sky-500 bg-sky-500 text-white transition-colors">
            Semua
        </button>
        @foreach($kategoris as $kategori)
            <button onclick="filterKategori('{{ $kategori->nama }}')"
                    id="filter-{{ $kategori->id }}"
                    class="filter-btn px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200
                           text-gray-600 hover:border-sky-400 hover:text-sky-600 transition-colors">
                {{ $kategori->nama }}
            </button>
        @endforeach
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="tabel-menu" class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-10">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-16">Foto</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Menu</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        @if(auth()->user()->hasPermission('edit_menu') || auth()->user()->hasPermission('delete_menu'))
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50"></tbody>
            </table>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus --}}
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
            <h3 class="text-center font-semibold text-gray-900 mb-1">Hapus Menu?</h3>
            <p class="text-center text-sm text-gray-500 mb-6">
                Menu <span id="nama-menu-hapus" class="font-semibold text-gray-800"></span>
                akan dihapus permanen beserta gambarnya.
            </p>
            <div class="flex gap-3">
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
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <style>
        /* ── Wrapper layout ── */
        #tabel-menu_wrapper {
            font-family: inherit;
        }

        /* ── Top bar: length + filter ── */
        #tabel-menu_wrapper .dataTables_length,
        #tabel-menu_wrapper .dataTables_filter {
            padding: 1rem 1rem 0.5rem;
            font-size: 0.875rem;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        #tabel-menu_wrapper .dataTables_filter {
            justify-content: flex-end;
        }

        /* Length select */
        #tabel-menu_wrapper .dataTables_length select {
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
        #tabel-menu_wrapper .dataTables_length select:focus {
            outline: none;
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15);
        }

        /* Search input */
        #tabel-menu_wrapper .dataTables_filter input {
            padding: 0.35rem 0.75rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 0.8125rem;
            color: #374151;
            background-color: #fff;
            transition: border-color 0.15s, box-shadow 0.15s;
            width: 200px;
        }
        #tabel-menu_wrapper .dataTables_filter input:focus {
            outline: none;
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15);
        }

        /* ── Bottom bar: info + pagination ── */
        #tabel-menu_wrapper .dataTables_info,
        #tabel-menu_wrapper .dataTables_paginate {
            padding: 0.75rem 1rem;
            font-size: 0.8125rem;
            color: #6b7280;
        }

        /* ── Pagination ── */
        #tabel-menu_wrapper .dataTables_paginate {
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }
        #tabel-menu_wrapper .dataTables_paginate span {
            display: inline-flex;
            gap: 0.25rem;
        }
        #tabel-menu_wrapper .dataTables_paginate .paginate_button {
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
        /* Previous / Next */
        #tabel-menu_wrapper .dataTables_paginate .previous,
        #tabel-menu_wrapper .dataTables_paginate .next {
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #374151;
            margin: 0 0.25rem;
        }
        #tabel-menu_wrapper .dataTables_paginate .previous:hover:not(.disabled),
        #tabel-menu_wrapper .dataTables_paginate .next:hover:not(.disabled) {
            border-color: #38bdf8;
            color: #0284c7;
            background: #f0f9ff;
        }
        #tabel-menu_wrapper .dataTables_paginate .previous.disabled,
        #tabel-menu_wrapper .dataTables_paginate .next.disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }
        /* Number buttons */
        #tabel-menu_wrapper .dataTables_paginate .paginate_button:not(.previous):not(.next):hover:not(.current) {
            background: #f0f9ff;
            color: #0284c7;
            border-color: #bae6fd;
        }
        /* Active / current */
        #tabel-menu_wrapper .dataTables_paginate .paginate_button.current,
        #tabel-menu_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #0ea5e9 !important;
            color: #fff !important;
            border-color: #0ea5e9 !important;
            box-shadow: 0 1px 3px rgba(14, 165, 233, 0.35);
        }
        /* Ellipsis */
        #tabel-menu_wrapper .dataTables_paginate .ellipsis {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            height: 2rem;
            color: #9ca3af;
            font-size: 0.8125rem;
        }

        /* ── Processing overlay ── */
        #tabel-menu_wrapper .dataTables_processing {
            font-size: 0.8125rem;
            color: #6b7280;
            background: rgba(255,255,255,0.9);
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
    </style>
@endpush

@push('scripts')
    {{-- jQuery + DataTables --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <script>
        let table;
        let menuIdHapus = null;

        // ============================================
        // INIT DATATABLES
        // ============================================
        $(document).ready(function () {
            table = $('#tabel-menu').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('menu.index') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'gambar',   name: 'gambar',   orderable: false, searchable: false },
                    { data: 'nama',     name: 'nama' },
                    { data: 'kategori', name: 'kategori.nama' },
                    { data: 'harga',    name: 'harga' },
                    { data: 'stok',     name: 'stok' },
                    { data: 'status',   name: 'is_aktif', orderable: false, searchable: false },
                    @if(auth()->user()->hasPermission('edit_menu') || auth()->user()->hasPermission('delete_menu'))
                    { data: 'aksi', name: 'aksi', orderable: false, searchable: false },
                    @endif
                ],
                language: {
                    processing:     'Memuat data...',
                    search:         '',
                    searchPlaceholder: 'Cari menu...',
                    lengthMenu:     'Tampilkan _MENU_ data',
                    info:           'Menampilkan _START_ – _END_ dari _TOTAL_ data',
                    infoEmpty:      'Tidak ada data',
                    infoFiltered:   '(difilter dari _MAX_ total data)',
                    zeroRecords:    'Tidak ada data yang cocok',
                    emptyTable:     'Belum ada menu tersedia',
                    paginate: {
                        first:    '«',
                        last:     '»',
                        next:     '›',
                        previous: '‹',
                    },
                },
                lengthMenu: [10, 25, 50, 100],
                pageLength: 10,
                order: [[2, 'asc']],
                drawCallback: function () {
                    $('#tabel-menu tbody tr').addClass('hover:bg-gray-50 transition-colors');
                    $('#tabel-menu tbody td').addClass('px-4 py-3 text-gray-700');
                }
            });
        });

        // ============================================
        // FILTER KATEGORI
        // ============================================
        function filterKategori(nama) {
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('bg-sky-500', 'text-white', 'border-sky-500');
                btn.classList.add('border-gray-200', 'text-gray-600');
            });

            const aktifBtn = nama === ''
                ? document.getElementById('filter-all')
                : event.currentTarget;
            aktifBtn.classList.add('bg-sky-500', 'text-white', 'border-sky-500');
            aktifBtn.classList.remove('border-gray-200', 'text-gray-600');

            table.column(3).search(nama).draw();
        }

        // ============================================
        // EDIT MENU
        // ============================================
        function editMenu(id) {
            window.location.href = `/menu/${id}/edit`;
        }

        // ============================================
        // HAPUS MENU
        // ============================================
        function hapusMenu(id, nama) {
            menuIdHapus = id;
            document.getElementById('nama-menu-hapus').textContent = nama;
            document.getElementById('modal-hapus').classList.remove('hidden');
            document.getElementById('modal-hapus').classList.add('flex');
        }

        function tutupModalHapus() {
            menuIdHapus = null;
            document.getElementById('modal-hapus').classList.add('hidden');
            document.getElementById('modal-hapus').classList.remove('flex');
        }

        document.getElementById('btn-konfirmasi-hapus').addEventListener('click', function () {
            if (!menuIdHapus) return;

            const btn = this;
            btn.textContent = 'Menghapus...';
            btn.disabled    = true;

            fetch(`/menu/${menuIdHapus}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept':       'application/json',
                },
            })
            .then(res => res.json())
            .then(data => {
                tutupModalHapus();
                if (data.success) {
                    table.ajax.reload(null, false);
                    tampilkanNotif('success', data.message);
                } else {
                    tampilkanNotif('error', data.message);
                }
            })
            .catch(() => {
                tampilkanNotif('error', 'Terjadi kesalahan. Silakan coba lagi.');
            })
            .finally(() => {
                btn.textContent = 'Ya, Hapus';
                btn.disabled    = false;
            });
        });

        // ============================================
        // NOTIFIKASI TOAST
        // ============================================
        function tampilkanNotif(tipe, pesan) {
            const warna = tipe === 'success'
                ? 'bg-green-50 border-green-200 text-green-800'
                : 'bg-red-50 border-red-200 text-red-800';

            const notif = document.createElement('div');
            notif.className = `fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3
                               rounded-xl border shadow-lg text-sm font-medium ${warna}
                               animate-fade-in`;
            notif.textContent = pesan;

            document.body.appendChild(notif);
            setTimeout(() => notif.remove(), 4000);
        }
    </script>
@endpush