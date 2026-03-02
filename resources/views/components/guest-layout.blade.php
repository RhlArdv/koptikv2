<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Login' }} — Kopi Titik</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @stack('styles')

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-amber-100 flex items-center justify-center p-4">

    {{-- Main Container with Card --}}
    <div class="w-full max-w-md">

        {{-- Logo & Title Section --}}
        <div class="text-center mb-8">
            {{-- Logo --}}
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-amber-600 to-amber-800 rounded-2xl shadow-lg mb-4">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M2 21v-2h2V7a1 1 0 011-1h14a1 1 0 011 1v2h1a2 2 0 010 4h-1v5h2v2H2zm4-2h10v-5H6v5zm0-7h10V8H6v4zm12-2v-2h1a.5.5 0 000 1h-1v1z"/>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-800">Kopi Titik</h1>
            <p class="text-gray-500 text-sm mt-1">{{ $subtitle ?? 'Masuk untuk melanjutkan' }}</p>
        </div>

        {{-- Card Container --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

            {{-- Flash Messages --}}
            <div class="p-6 pt-4">
                @include('partials.alert')
            </div>

            {{-- Content --}}
            <div class="px-6 pb-6">
                {{ $slot }}
            </div>

        </div>

        {{-- Footer --}}
        <div class="text-center mt-6">
            {{ $footer ?? '' }}
        </div>

    </div>

    @stack('scripts')

</body>
</html>
