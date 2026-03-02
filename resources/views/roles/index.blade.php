@extends('layouts.app')

@section('title', 'Role & Permission')

@section('page-header')
<div class="flex items-center justify-between flex-wrap gap-3">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Role & Permission</h1>
        <p class="text-[13px] text-gray-500 mt-0.5">Kelola role dan hak akses pengguna sistem</p>
    </div>
    @if(auth()->user()->hasPermission('create_roles'))
    <button onclick="bukaModalTambah()"
            class="flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600
                   text-white text-sm font-semibold rounded-xl transition-colors shadow-sm shadow-amber-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Role
    </button>
    @endif
</div>
@endsection

@section('content')

{{-- Grid role cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4" id="roles-grid">
    @foreach($roles as $role)
    @php
        $isSystem = in_array($role->name, ['admin', 'kasir', 'head_bar']);
        $colorMap = [
            'admin'    => ['bg'=>'bg-purple-500','light'=>'bg-purple-50','text'=>'text-purple-700','border'=>'border-purple-100'],
            'kasir'    => ['bg'=>'bg-blue-500',  'light'=>'bg-blue-50',  'text'=>'text-blue-700',  'border'=>'border-blue-100'],
            'head_bar' => ['bg'=>'bg-amber-500', 'light'=>'bg-amber-50', 'text'=>'text-amber-700', 'border'=>'border-amber-100'],
        ];
        $color = $colorMap[$role->name] ?? [
            'bg'=>'bg-teal-500','light'=>'bg-teal-50',
            'text'=>'text-teal-700','border'=>'border-teal-100'
        ];
        $totalPermission = \App\Models\Permission::count();
        $pct = $totalPermission > 0 ? round(($role->permissions_count / $totalPermission) * 100) : 0;
    @endphp

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden
                hover:shadow-md transition-shadow" id="role-card-{{ $role->id }}">

        {{-- Header --}}
        <div class="{{ $color['light'] }} border-b {{ $color['border'] }} px-5 py-4">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl {{ $color['bg'] }} flex items-center
                                justify-center shadow-sm flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 leading-none">{{ $role->display_name }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $role->description ?? 'Tidak ada deskripsi' }}</p>
                    </div>
                </div>

                {{-- Badge sistem / custom --}}
                <div class="flex-shrink-0">
                    @if($isSystem)
                        <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-gray-100 text-gray-500">
                            Sistem
                        </span>
                    @else
                        <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-teal-100 text-teal-600">
                            Custom
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="px-5 py-4">

            {{-- Progress permission --}}
            <div class="flex items-center justify-between mb-1.5">
                <p class="text-xs text-gray-400">Permission aktif</p>
                <p class="text-xs font-bold {{ $color['text'] }}">
                    {{ $role->permissions_count }} / {{ $totalPermission }}
                </p>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5 mb-4">
                <div class="h-1.5 rounded-full {{ $color['bg'] }}" style="width: {{ $pct }}%"></div>
            </div>

            {{-- User count --}}
            <div class="flex items-center gap-1.5 text-xs text-gray-400 mb-4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/>
                </svg>
                {{ $role->users_count }} pengguna menggunakan role ini
            </div>

            {{-- Tombol aksi --}}
            <div class="flex gap-2">
                @if(auth()->user()->hasPermission('edit_roles'))
                <a href="{{ route('roles.edit', $role->id) }}"
                   class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold
                          {{ $color['light'] }} {{ $color['text'] }} border {{ $color['border'] }}
                          rounded-xl hover:opacity-80 transition-opacity">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Permission
                </a>
                @endif

                @if(!$isSystem && auth()->user()->hasPermission('delete_roles'))
                <button onclick="hapusRole({{ $role->id }}, '{{ addslashes($role->display_name) }}', {{ $role->users_count }})"
                        class="px-3 py-2 text-xs font-semibold text-red-600 bg-red-50
                               border border-red-100 rounded-xl hover:bg-red-100 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
                @endif
            </div>

        </div>
    </div>
    @endforeach
</div>

{{-- Info catatan --}}
<div class="mt-5 bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-start gap-3">
    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div>
        <p class="text-sm font-semibold text-amber-800">Catatan</p>
        <p class="text-xs text-amber-700 mt-0.5">
            Role bertanda <strong>Sistem</strong> (Admin, Kasir, Head Bar) tidak bisa dihapus dan namanya tidak bisa diubah.
            Role <strong>Custom</strong> bisa dihapus selama tidak ada user yang menggunakannya.
            Setelah tambah role baru, atur permission-nya melalui tombol <em>Permission</em>.
        </p>
    </div>
</div>

{{-- ============================================
     MODAL TAMBAH ROLE
     ============================================ --}}
<div id="modal-tambah"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4"
     onclick="if(event.target===this)tutupModal()">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm">

        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900">Tambah Role Baru</h3>
            <button onclick="tutupModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="px-6 py-5 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    Nama Role <span class="text-red-400">*</span>
                </label>
                <input type="text" id="inp-display-name"
                       placeholder="Contoh: Supervisor, Barista..."
                       maxlength="100"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                              focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-100">
                <p class="text-[11px] text-gray-400 mt-1">
                    Key otomatis dibuat dari nama. Contoh: "Head Bar 2" → <code>head_bar_2</code>
                </p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    Deskripsi <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <input type="text" id="inp-description"
                       placeholder="Deskripsi singkat role ini..."
                       maxlength="255"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                              focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-100">
            </div>
            <p id="err-tambah" class="text-xs text-red-500 hidden"></p>
        </div>

        <div class="px-6 pb-6 flex gap-3">
            <button onclick="tutupModal()"
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100
                           hover:bg-gray-200 rounded-xl transition-colors">
                Batal
            </button>
            <button id="btn-simpan-role" onclick="simpanRole()"
                    class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-amber-500
                           hover:bg-amber-600 rounded-xl transition-colors">
                Tambah Role
            </button>
        </div>

    </div>
</div>

{{-- ============================================
     MODAL HAPUS ROLE
     ============================================ --}}
<div id="modal-hapus"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4"
     onclick="if(event.target===this)tutupModalHapus()">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </div>
        <h3 class="font-bold text-gray-900 mb-1">Hapus Role?</h3>
        <p class="text-sm text-gray-500 mb-6">
            Role <span id="nama-hapus" class="font-semibold text-gray-800"></span>
            akan dihapus permanen beserta semua permission-nya.
        </p>
        <div class="flex gap-3">
            <button onclick="tutupModalHapus()"
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100
                           hover:bg-gray-200 rounded-xl transition-colors">
                Batal
            </button>
            <button id="btn-konfirmasi-hapus"
                    class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-red-600
                           hover:bg-red-700 rounded-xl transition-colors">
                Ya, Hapus
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let hapusId = null;

// ============================================
// MODAL TAMBAH
// ============================================
function bukaModalTambah() {
    document.getElementById('inp-display-name').value = '';
    document.getElementById('inp-description').value  = '';
    document.getElementById('err-tambah').classList.add('hidden');
    document.getElementById('modal-tambah').classList.remove('hidden');
    document.getElementById('modal-tambah').classList.add('flex');
    setTimeout(() => document.getElementById('inp-display-name').focus(), 100);
}
function tutupModal() {
    document.getElementById('modal-tambah').classList.add('hidden');
    document.getElementById('modal-tambah').classList.remove('flex');
}

// ============================================
// SIMPAN ROLE BARU
// ============================================
function simpanRole() {
    const btn     = document.getElementById('btn-simpan-role');
    const errEl   = document.getElementById('err-tambah');
    const nama    = document.getElementById('inp-display-name').value.trim();
    const deskripsi = document.getElementById('inp-description').value.trim();

    errEl.classList.add('hidden');

    if (!nama) {
        errEl.textContent = 'Nama role wajib diisi.';
        errEl.classList.remove('hidden');
        return;
    }

    btn.textContent = 'Menyimpan...';
    btn.disabled    = true;

    fetch('/roles', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'Accept':       'application/json',
        },
        body: JSON.stringify({ display_name: nama, description: deskripsi }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            tutupModal();
            toast('success', data.message);
            // Reload halaman supaya card baru muncul
            setTimeout(() => location.reload(), 800);
        } else {
            errEl.textContent = data.message;
            errEl.classList.remove('hidden');
        }
    })
    .catch(() => {
        errEl.textContent = 'Terjadi kesalahan. Coba lagi.';
        errEl.classList.remove('hidden');
    })
    .finally(() => {
        btn.textContent = 'Tambah Role';
        btn.disabled    = false;
    });
}

// ============================================
// HAPUS ROLE
// ============================================
function hapusRole(id, nama, userCount) {
    if (userCount > 0) {
        toast('error', `Role "${nama}" tidak bisa dihapus — masih dipakai ${userCount} user.`);
        return;
    }
    hapusId = id;
    document.getElementById('nama-hapus').textContent = nama;
    document.getElementById('modal-hapus').classList.remove('hidden');
    document.getElementById('modal-hapus').classList.add('flex');
}
function tutupModalHapus() {
    document.getElementById('modal-hapus').classList.add('hidden');
    document.getElementById('modal-hapus').classList.remove('flex');
}

document.getElementById('btn-konfirmasi-hapus').addEventListener('click', function() {
    const btn = this;
    btn.textContent = 'Menghapus...';
    btn.disabled    = true;

    fetch(`/roles/${hapusId}`, {
        method:  'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(data => {
        tutupModalHapus();
        if (data.success) {
            toast('success', data.message);
            // Hapus card dari DOM
            const card = document.getElementById('role-card-' + hapusId);
            if (card) card.remove();
        } else {
            toast('error', data.message);
        }
    })
    .catch(() => toast('error', 'Gagal menghapus role.'))
    .finally(() => {
        btn.textContent = 'Ya, Hapus';
        btn.disabled    = false;
    });
});

// ============================================
// ENTER SUBMIT
// ============================================
document.addEventListener('keydown', e => {
    if (e.key === 'Enter' && document.getElementById('modal-tambah').classList.contains('flex')) {
        simpanRole();
    }
});

// ============================================
// TOAST
// ============================================
function toast(tipe, pesan) {
    const warna = tipe === 'success'
        ? 'bg-green-50 border-green-200 text-green-800'
        : 'bg-red-50 border-red-200 text-red-800';
    const el = document.createElement('div');
    el.className = `fixed bottom-6 right-6 z-[99999] px-4 py-3 rounded-xl border
                    shadow-lg text-sm font-medium max-w-sm ${warna}`;
    el.textContent = pesan;
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 4000);
}
</script>
@endpush