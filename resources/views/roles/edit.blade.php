@extends('layouts.app')

@section('title', 'Edit Permission — ' . $role->display_name)

@section('page-header')
<div class="flex items-center justify-between flex-wrap gap-3">
    <div class="flex items-center gap-3">
        <a href="{{ route('roles.index') }}"
           class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200
                  text-gray-500 hover:bg-gray-100 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Edit Permission</h1>
            <p class="text-[13px] text-gray-500 mt-0.5">Role: <span class="font-semibold text-gray-700">{{ $role->display_name }}</span></p>
        </div>
    </div>

    <div class="flex items-center gap-2">
        {{-- Reset ke default --}}
        @if($role->name !== 'admin')
        <button onclick="resetDefault()"
                class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200
                       hover:bg-gray-50 rounded-xl transition-colors">
            ↩ Reset Default
        </button>
        @endif

        {{-- Simpan --}}
        <button onclick="simpanPermission()"
                id="btn-simpan"
                class="flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600
                       text-white text-sm font-semibold rounded-xl transition-colors shadow-sm shadow-amber-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Simpan Permission
        </button>
    </div>
</div>
@endsection

@section('content')

{{-- Info admin bypass --}}
@if($role->name === 'admin')
<div class="mb-5 bg-purple-50 border border-purple-200 rounded-2xl p-4 flex items-start gap-3">
    <svg class="w-5 h-5 text-purple-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
    </svg>
    <div>
        <p class="text-sm font-semibold text-purple-800">Administrator — Akses Penuh</p>
        <p class="text-xs text-purple-700 mt-0.5">
            Admin otomatis bypass semua pengecekan permission. Checkbox di bawah hanya untuk referensi,
            tidak mempengaruhi akses admin.
        </p>
    </div>
</div>
@endif

{{-- Counter summary --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-5 flex items-center gap-6 flex-wrap">
    <div>
        <p class="text-xs text-gray-400">Total permission</p>
        <p class="text-lg font-bold text-gray-900" id="total-all">{{ \App\Models\Permission::count() }}</p>
    </div>
    <div class="w-px h-8 bg-gray-100"></div>
    <div>
        <p class="text-xs text-gray-400">Dipilih</p>
        <p class="text-lg font-bold text-amber-600" id="counter-dipilih">{{ count($assignedIds) }}</p>
    </div>
    <div class="w-px h-8 bg-gray-100"></div>
    <div>
        <p class="text-xs text-gray-400">Tidak dipilih</p>
        <p class="text-lg font-bold text-gray-400" id="counter-tidak">{{ \App\Models\Permission::count() - count($assignedIds) }}</p>
    </div>
    <div class="ml-auto flex items-center gap-2">
        <button onclick="pilihSemua()"
                class="px-3 py-1.5 text-xs font-semibold text-green-700 bg-green-50
                       hover:bg-green-100 border border-green-200 rounded-lg transition-colors">
            ✓ Pilih Semua
        </button>
        <button onclick="hapusSemua()"
                class="px-3 py-1.5 text-xs font-semibold text-red-600 bg-red-50
                       hover:bg-red-100 border border-red-200 rounded-lg transition-colors">
            ✕ Hapus Semua
        </button>
    </div>
</div>

{{-- Permission groups --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    @foreach($permissionGroups as $group => $permissions)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- Group header --}}
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between bg-gray-50">
            <div class="flex items-center gap-2.5">
                {{-- Checkbox pilih semua dalam group --}}
                <input type="checkbox"
                       class="group-checkbox w-4 h-4 rounded border-gray-300 text-amber-500
                              focus:ring-amber-400 focus:ring-offset-0 cursor-pointer"
                       data-group="{{ $group }}"
                       onchange="toggleGroup('{{ $group }}', this.checked)"
                       id="group-{{ Str::slug($group) }}">
                <label for="group-{{ Str::slug($group) }}"
                       class="text-sm font-bold text-gray-800 cursor-pointer">
                    {{ $group }}
                </label>
            </div>
            <span class="text-xs text-gray-400 group-count" data-group="{{ $group }}">
                {{ $permissions->whereIn('id', $assignedIds)->count() }}/{{ $permissions->count() }}
            </span>
        </div>

        {{-- Permission list --}}
        <div class="p-4 space-y-2.5">
            @foreach($permissions as $permission)
            <label class="flex items-center gap-3 cursor-pointer group/item">
                <input type="checkbox"
                       class="permission-checkbox w-4 h-4 rounded border-gray-300 text-amber-500
                              focus:ring-amber-400 focus:ring-offset-0 cursor-pointer"
                       name="permissions[]"
                       value="{{ $permission->id }}"
                       data-group="{{ $group }}"
                       {{ in_array($permission->id, $assignedIds) ? 'checked' : '' }}
                       onchange="onPermissionChange()">
                <div class="flex-1 min-w-0">
                    <p class="text-[13px] font-medium text-gray-800 group-hover/item:text-amber-700
                              transition-colors">
                        {{ $permission->display_name }}
                    </p>
                    <p class="text-[11px] text-gray-400 font-mono">{{ $permission->key }}</p>
                </div>
            </label>
            @endforeach
        </div>

    </div>
    @endforeach
</div>

{{-- Sticky save bar bawah --}}
<div class="sticky bottom-0 -mx-6 px-6 pb-4 pt-3 mt-4 bg-gradient-to-t from-gray-50 to-transparent">
    <div class="bg-white border border-gray-200 rounded-2xl px-5 py-3.5 flex items-center
                justify-between shadow-lg shadow-gray-200/50">
        <p class="text-sm text-gray-500">
            <span class="font-semibold text-gray-800" id="counter-dipilih-bottom">{{ count($assignedIds) }}</span>
            permission dipilih untuk role <span class="font-semibold text-amber-700">{{ $role->display_name }}</span>
        </p>
        <button onclick="simpanPermission()"
                class="flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600
                       text-white text-sm font-semibold rounded-xl transition-colors shadow-sm shadow-amber-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Simpan
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const csrfToken  = document.querySelector('meta[name="csrf-token"]').content;
    const roleId     = {{ $role->id }};
    const totalAll   = parseInt(document.getElementById('total-all').textContent);

    // ============================================
    // UPDATE COUNTER
    // ============================================
    function updateCounter() {
        const checked = document.querySelectorAll('.permission-checkbox:checked').length;
        const tidak   = totalAll - checked;

        document.getElementById('counter-dipilih').textContent        = checked;
        document.getElementById('counter-tidak').textContent          = tidak;
        document.getElementById('counter-dipilih-bottom').textContent = checked;

        // Update group checkboxes & counter
        document.querySelectorAll('.group-checkbox').forEach(groupCb => {
            const group   = groupCb.dataset.group;
            const all     = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`);
            const allChecked = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]:checked`);

            groupCb.checked       = all.length === allChecked.length;
            groupCb.indeterminate = allChecked.length > 0 && allChecked.length < all.length;

            // Update count label
            const countEl = document.querySelector(`.group-count[data-group="${group}"]`);
            if (countEl) countEl.textContent = `${allChecked.length}/${all.length}`;
        });
    }

    function onPermissionChange() {
        updateCounter();
    }

    // ============================================
    // TOGGLE SELURUH GROUP
    // ============================================
    function toggleGroup(group, checked) {
        document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`)
            .forEach(cb => cb.checked = checked);
        updateCounter();
    }

    // ============================================
    // PILIH/HAPUS SEMUA
    // ============================================
    function pilihSemua() {
        document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = true);
        document.querySelectorAll('.group-checkbox').forEach(cb => cb.checked = true);
        updateCounter();
    }

    function hapusSemua() {
        document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
        document.querySelectorAll('.group-checkbox').forEach(cb => { cb.checked = false; cb.indeterminate = false; });
        updateCounter();
    }

    // ============================================
    // SIMPAN
    // ============================================
    function simpanPermission() {
        const btn = document.getElementById('btn-simpan');
        const ids = [...document.querySelectorAll('.permission-checkbox:checked')]
                        .map(cb => parseInt(cb.value));

        btn.textContent = 'Menyimpan...';
        btn.disabled    = true;

        fetch(`/roles/${roleId}`, {
            method:  'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept':       'application/json',
            },
            body: JSON.stringify({ permissions: ids }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                tampilToast('success', data.message);
            } else {
                tampilToast('error', data.message);
            }
        })
        .catch(() => tampilToast('error', 'Gagal menyimpan permission.'))
        .finally(() => {
            btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg> Simpan Permission`;
            btn.disabled = false;
        });
    }

    // ============================================
    // RESET DEFAULT
    // ============================================
    function resetDefault() {
        if (!confirm('Reset permission ke default untuk role ini?')) return;

        fetch(`/roles/${roleId}/reset`, {
            method:  'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                tampilToast('success', data.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                tampilToast('error', data.message);
            }
        })
        .catch(() => tampilToast('error', 'Gagal mereset permission.'));
    }

    // ============================================
    // TOAST
    // ============================================
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

    // Init counter saat halaman load
    updateCounter();
</script>
@endpush