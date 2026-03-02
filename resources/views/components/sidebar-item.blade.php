{{--
    Component: sidebar-item
    Dipakai di partials/sidebar.blade.php

    Props:
    - route  : nama route Laravel (misal: 'dashboard', 'menu.index')
    - label  : teks label menu
    - icon   : SVG path string (isi dari <path> tag)

    Contoh pakai:
    <x-sidebar-item
        route="dashboard"
        label="Dashboard"
        icon='<path stroke-linecap="round" .../>'
    />
--}}

@props(['route', 'label', 'icon'])

@php
    $isActive = request()->routeIs($route) || request()->routeIs($route . '.*');
@endphp

<a
    href="{{ route($route) }}"
    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150
           {{ $isActive
               ? 'bg-amber-500 text-white shadow-sm'
               : 'text-gray-400 hover:bg-gray-800 hover:text-white'
           }}"
>
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        {!! $icon !!}
    </svg>
    <span>{{ $label }}</span>
</a>