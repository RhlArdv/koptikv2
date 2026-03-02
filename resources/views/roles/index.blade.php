@extends('layouts.app')

@section('title', 'Role & Permission')

@section('page-header')
<div>
    <h1 class="text-xl font-bold text-gray-900">Role & Permission</h1>
    <p class="text-[13px] text-gray-500 mt-0.5">Kelola hak akses setiap role pengguna</p>
</div>
@endsection

@section('content')

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">

    @foreach($roles as $role)
    @php
        $colorMap = [
            'admin'    => ['bg' => 'bg-purple-500', 'light' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-100'],
            'kasir'    => ['bg' => 'bg-blue-500',   'light' => 'bg-blue-50',   'text' => 'text-blue-700',   'border' => 'border-blue-100'],
            'head_bar' => ['bg' => 'bg-amber-500',  'light' => 'bg-amber-50',  'text' => 'text-amber-700',  'border' => 'border-amber-100'],
        ];
        $color = $colorMap[$role->name] ?? ['bg' => 'bg-gray-500', 'light' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-100'];
        $totalPermission = \App\Models\Permission::count();
        $userCount = \App\Models\User::where('role_id', $role->id)->count();
    @endphp

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow">

        <div class="p-5 {{ $color['light'] }} border-b {{ $color['border'] }}">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl {{ $color['bg'] }} flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-gray-900">{{ $role->display_name }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $role->description ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="px-5 py-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs text-gray-500">Permission aktif</p>
                <p class="text-xs font-bold {{ $color['text'] }}">{{ $role->permissions_count }} / {{ $totalPermission }}</p>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5 mb-4">
                <div class="h-1.5 rounded-full {{ $color['bg'] }}"
                     style="width: {{ $totalPermission > 0 ? round(($role->permissions_count / $totalPermission) * 100) : 0 }}%"></div>
            </div>

            <div class="flex items-center gap-2 text-xs text-gray-400 mb-4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/>
                </svg>
                {{ $userCount }} pengguna dengan role ini
            </div>

            @if(auth()->user()->hasPermission('edit_roles'))
            <a href="{{ route('roles.edit', $role->id) }}"
               class="flex items-center justify-center gap-2 w-full px-4 py-2.5 text-sm font-semibold
                      {{ $color['light'] }} {{ $color['text'] }} border {{ $color['border'] }}
                      rounded-xl hover:opacity-80 transition-opacity">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Kelola Permission
            </a>
            @endif
        </div>
    </div>
    @endforeach

</div>

<div class="mt-5 bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-start gap-3">
    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div>
        <p class="text-sm font-semibold text-amber-800">Catatan Penting</p>
        <p class="text-xs text-amber-700 mt-1">
            Role <strong>Administrator</strong> secara otomatis memiliki akses ke semua fitur sistem.
            Perubahan permission pada role Admin tidak berpengaruh karena admin selalu bypass pengecekan permission.
        </p>
    </div>
</div>

@endsection