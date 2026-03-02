{{--
    NAVBAR TOP
    - Hamburger button untuk toggle sidebar di mobile
    - Judul halaman (diambil dari @yield('title'))
    - Info user + role di kanan
--}}

<header class="sticky top-0 z-10 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center gap-4 px-6 py-3">

        {{-- Hamburger (mobile only) --}}
        <button
            @click="sidebarOpen = !sidebarOpen"
            class="lg:hidden text-gray-500 hover:text-gray-700"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- Judul halaman --}}
        <h1 class="text-base font-semibold text-gray-800">
            @yield('title', 'Dashboard')
        </h1>

        {{-- Spacer --}}
        <div class="flex-1"></div>

        {{-- Badge role user --}}
        <span class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
            {{ auth()->user()->role?->display_name ?? 'Tanpa Role' }}
        </span>

        {{-- Nama user --}}
        <span class="hidden sm:block text-sm font-medium text-gray-700">
            {{ auth()->user()->name }}
        </span>

    </div>
</header>