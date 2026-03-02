@extends('layouts.app')

@section('title', 'Kelola User')

@section('page-header')
<div class="flex items-center justify-between flex-wrap gap-3">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Kelola User</h1>
        <p class="text-[13px] text-gray-500 mt-0.5">Manajemen akun pengguna sistem</p>
    </div>
    @if(auth()->user()->hasPermission('create_users'))
    <button onclick="bukaModalTambah()"
            class="flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600
                   text-white text-sm font-semibold rounded-xl transition-colors shadow-sm shadow-sky-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah User
    </button>
    @endif
</div>
@endsection

@section('content')

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between gap-3 flex-wrap">
            <p class="text-[13px] text-gray-500">Daftar semua pengguna yang terdaftar di sistem.</p>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400">Filter role:</span>
                <select id="filter-role"
                        class="border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-600
                               focus:outline-none focus:border-sky-400 focus:ring-1 focus:ring-sky-100 transition-colors">
                    <option value="">Semua Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->display_name }}">{{ $role->display_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="p-5">
            <table id="tabel-users" class="w-full text-sm" style="width:100%">
                <thead>
                    <tr>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider pb-3">#</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider pb-3">User</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider pb-3">Email</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider pb-3">Role</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider pb-3">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    {{-- ============================================
         MODAL TAMBAH / EDIT USER
         ============================================ --}}
    <div id="modal-user"
         class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4"
         onclick="if(event.target===this) tutupModal()">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">

            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900" id="modal-title">Tambah User</h3>
                <button onclick="tutupModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="px-6 py-5 space-y-4">

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                    <input type="text" id="input-name" placeholder="Nama lengkap..."
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                  focus:outline-none focus:border-sky-400 focus:ring-1 focus:ring-sky-100 transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email <span class="text-red-400">*</span></label>
                    <input type="email" id="input-email" placeholder="email@kopititik.com"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                  focus:outline-none focus:border-sky-400 focus:ring-1 focus:ring-sky-100 transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Password <span id="label-password-hint" class="text-gray-400 font-normal">(opsional saat edit)</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="input-password" placeholder="Min. 6 karakter"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm pr-10
                                      focus:outline-none focus:border-sky-400 focus:ring-1 focus:ring-sky-100 transition-colors">
                        <button type="button" onclick="togglePass()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg id="pass-eye" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Role <span class="text-red-400">*</span></label>
                    <select id="input-role"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                   focus:outline-none focus:border-sky-400 focus:ring-1 focus:ring-sky-100 transition-colors">
                        <option value="">-- Pilih Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                        @endforeach
                    </select>
                </div>

                <p id="error-user" class="text-xs text-red-500 hidden"></p>

            </div>

            <div class="px-6 pb-6 flex gap-3">
                <button onclick="tutupModal()"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100
                               hover:bg-gray-200 rounded-xl transition-colors">
                    Batal
                </button>
                <button id="btn-simpan"
                        onclick="simpanUser()"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-sky-500
                               hover:bg-sky-600 rounded-xl transition-colors">
                    Simpan
                </button>
            </div>

        </div>
    </div>

    {{-- Modal Hapus --}}
    <div id="modal-hapus"
         class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40"
         onclick="if(event.target===this) document.getElementById('modal-hapus').classList.add('hidden')">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6 text-center">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h3 class="font-bold text-gray-900 mb-1">Hapus User?</h3>
            <p class="text-sm text-gray-500 mb-6">
                Akun <span id="nama-hapus" class="font-semibold text-gray-800"></span> akan dihapus permanen.
            </p>
            <div class="flex gap-3">
                <button onclick="document.getElementById('modal-hapus').classList.add('hidden')"
                        class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100
                               hover:bg-gray-200 rounded-xl transition-colors">
                    Batal
                </button>
                <button id="btn-konfirmasi-hapus"
                        class="flex-1 px-4 py-2 text-sm font-semibold text-white bg-red-600
                               hover:bg-red-700 rounded-xl transition-colors">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<style>
    /* ── Length + Filter bar ── */
    #tabel-users_wrapper .dataTables_length,
    #tabel-users_wrapper .dataTables_filter {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8125rem;
        color: #6b7280;
        margin-bottom: 1rem;
    }
    #tabel-users_wrapper .dataTables_filter {
        justify-content: flex-end;
    }

    /* Length select */
    #tabel-users_wrapper .dataTables_length select {
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
    #tabel-users_wrapper .dataTables_length select:focus {
        outline: none;
        border-color: #38bdf8;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15);
    }

    /* Search input */
    #tabel-users_wrapper .dataTables_filter input {
        padding: 0.35rem 0.75rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        font-size: 0.8125rem;
        color: #374151;
        width: 200px;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    #tabel-users_wrapper .dataTables_filter input:focus {
        outline: none;
        border-color: #38bdf8;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15);
    }

    /* Table rows */
    #tabel-users tbody tr { border-bottom: 1px solid #f9fafb; }
    #tabel-users tbody tr:hover td { background: #f0f9ff !important; }
    #tabel-users thead tr th { border-bottom: 2px solid #f3f4f6 !important; }

    /* ── Info + Pagination ── */
    #tabel-users_wrapper .dataTables_info {
        font-size: 0.8125rem;
        color: #6b7280;
        padding-top: 0.75rem;
    }
    #tabel-users_wrapper .dataTables_paginate {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.25rem;
        padding-top: 0.75rem;
    }
    #tabel-users_wrapper .dataTables_paginate span {
        display: inline-flex;
        gap: 0.25rem;
    }
    #tabel-users_wrapper .dataTables_paginate .paginate_button {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        height: 2rem;
        padding: 0 0.5rem !important;
        border-radius: 0.5rem !important;
        font-size: 0.8125rem !important;
        font-weight: 500;
        color: #374151 !important;
        background: transparent !important;
        border: 1px solid transparent !important;
        cursor: pointer;
        transition: all 0.15s;
    }
    #tabel-users_wrapper .dataTables_paginate .previous,
    #tabel-users_wrapper .dataTables_paginate .next {
        border: 1px solid #e5e7eb !important;
        background: #fff !important;
        margin: 0 0.125rem;
    }
    #tabel-users_wrapper .dataTables_paginate .previous:hover:not(.disabled),
    #tabel-users_wrapper .dataTables_paginate .next:hover:not(.disabled) {
        border-color: #38bdf8 !important;
        color: #0284c7 !important;
        background: #f0f9ff !important;
    }
    #tabel-users_wrapper .dataTables_paginate .previous.disabled,
    #tabel-users_wrapper .dataTables_paginate .next.disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }
    #tabel-users_wrapper .dataTables_paginate .paginate_button:not(.previous):not(.next):hover:not(.current) {
        background: #f0f9ff !important;
        color: #0284c7 !important;
        border-color: #bae6fd !important;
    }
    #tabel-users_wrapper .dataTables_paginate .paginate_button.current,
    #tabel-users_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #0ea5e9 !important;
        color: #fff !important;
        border-color: #0ea5e9 !important;
        box-shadow: 0 1px 3px rgba(14, 165, 233, 0.35);
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let modeModal   = 'tambah';
    let userIdEdit  = null;
    let userIdHapus = null;

    const table = $('#tabel-users').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('users.index') }}',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '50px' },
            {
                data: null,
                name: 'name',
                render: (data) =>
                    `<div class="flex items-center gap-3">
                        ${data.avatar}
                        <div>
                            <p class="text-[13px] font-semibold text-gray-800">${data.name}</p>
                        </div>
                    </div>`
            },
            { data: 'email', name: 'email', render: d => `<span class="text-[13px] text-gray-500">${d}</span>` },
            { data: 'role_badge', name: 'role_badge', orderable: false },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false },
        ],
        language: {
            search: '',
            searchPlaceholder: 'Cari user...',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ – _END_ dari _TOTAL_ user',
            infoEmpty: 'Tidak ada data',
            paginate: { previous: '‹', next: '›' },
            processing: '<div class="text-sky-500 text-sm py-4">Memuat data...</div>',
        },
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10,
        dom: '<"flex items-center justify-between mb-4 flex-wrap gap-3"lf>rtip',
    });

    $('#filter-role').on('change', function () {
        table.column(3).search(this.value).draw();
    });

    function bukaModalTambah() {
        modeModal = 'tambah';
        userIdEdit = null;
        resetForm();
        document.getElementById('modal-title').textContent = 'Tambah User';
        document.getElementById('label-password-hint').textContent = '';
        document.getElementById('input-password').required = true;
        bukaModal('modal-user');
        setTimeout(() => document.getElementById('input-name').focus(), 100);
    }

    function editUser(id) {
        fetch(`/users/${id}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        })
        .then(r => r.json())
        .then(({ data }) => {
            modeModal  = 'edit';
            userIdEdit = id;
            resetForm();
            document.getElementById('modal-title').textContent         = 'Edit User';
            document.getElementById('label-password-hint').textContent = '(kosongkan jika tidak diubah)';
            document.getElementById('input-password').required         = false;
            document.getElementById('input-name').value                = data.name;
            document.getElementById('input-email').value               = data.email;
            document.getElementById('input-role').value                = data.role_id;
            bukaModal('modal-user');
            setTimeout(() => document.getElementById('input-name').focus(), 100);
        })
        .catch(() => tampilToast('error', 'Gagal memuat data user.'));
    }

    function simpanUser() {
        const btn     = document.getElementById('btn-simpan');
        const errorEl = document.getElementById('error-user');
        errorEl.classList.add('hidden');

        const payload = {
            name:     document.getElementById('input-name').value.trim(),
            email:    document.getElementById('input-email').value.trim(),
            password: document.getElementById('input-password').value,
            role_id:  document.getElementById('input-role').value,
        };

        const url    = modeModal === 'tambah' ? '/users' : `/users/${userIdEdit}`;
        const method = modeModal === 'tambah' ? 'POST' : 'PUT';

        btn.textContent = 'Menyimpan...';
        btn.disabled    = true;

        fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(payload),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                tutupModal();
                tampilToast('success', data.message);
                table.ajax.reload(null, false);
            } else {
                errorEl.textContent = data.message;
                errorEl.classList.remove('hidden');
            }
        })
        .catch(() => {
            errorEl.textContent = 'Terjadi kesalahan. Coba lagi.';
            errorEl.classList.remove('hidden');
        })
        .finally(() => { btn.textContent = 'Simpan'; btn.disabled = false; });
    }

    function hapusUser(id, nama) {
        userIdHapus = id;
        document.getElementById('nama-hapus').textContent = nama;
        document.getElementById('modal-hapus').classList.remove('hidden');
        document.getElementById('modal-hapus').classList.add('flex');
    }

    document.getElementById('btn-konfirmasi-hapus').addEventListener('click', function () {
        const btn = this;
        btn.textContent = 'Menghapus...';
        btn.disabled    = true;

        fetch(`/users/${userIdHapus}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('modal-hapus').classList.add('hidden');
            document.getElementById('modal-hapus').classList.remove('flex');
            if (data.success) { tampilToast('success', data.message); table.ajax.reload(null, false); }
            else tampilToast('error', data.message);
        })
        .catch(() => tampilToast('error', 'Gagal menghapus user.'))
        .finally(() => { btn.textContent = 'Ya, Hapus'; btn.disabled = false; });
    });

    function bukaModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.getElementById(id).classList.add('flex');
    }
    function tutupModal() {
        document.getElementById('modal-user').classList.add('hidden');
        document.getElementById('modal-user').classList.remove('flex');
    }
    function resetForm() {
        ['input-name', 'input-email', 'input-password'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('input-role').value = '';
        document.getElementById('error-user').classList.add('hidden');
    }
    function togglePass() {
        const input = document.getElementById('input-password');
        input.type  = input.type === 'password' ? 'text' : 'password';
    }
    function tampilToast(tipe, pesan) {
        const warna = tipe === 'success'
            ? 'bg-green-50 border-green-200 text-green-800'
            : 'bg-red-50 border-red-200 text-red-800';
        const toast = document.createElement('div');
        toast.className = `fixed bottom-6 right-6 z-[99999] flex items-center gap-2 px-4 py-3
                           rounded-xl border shadow-lg text-sm font-medium max-w-sm ${warna}`;
        toast.textContent = pesan;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Enter' && document.getElementById('modal-user').classList.contains('flex')) {
            simpanUser();
        }
    });
</script>
@endpush