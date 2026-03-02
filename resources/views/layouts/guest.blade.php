<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') — Kopi Titik </title>

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
            <div class="inline-flex items-center justify-center w-40 h-24 bg-gradient-to-br  rounded-2xl shadow-lg mb-4 overflow-hidden">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Kopi Titik Logo" class="">
            </div>

            {{-- <h1 class="text-2xl font-bold text-gray-800">Kopi Titik</h1> --}}
            {{-- <p class="text-gray-500 text-sm mt-1">@yield('guest-subtitle', 'Masuk untuk melanjutkan')</p> --}}
        </div>

        {{-- Card Container --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

            {{-- Flash Messages --}}
            <div class="p-6 pt-4">
                @include('partials.alert')
            </div>

            {{-- Content --}}
            <div class="px-6 pb-6">
                @yield('content')
            </div>

        </div>

        {{-- Footer --}}
        <div class="text-center mt-6">
            @yield('guest-footer')
        </div>

    </div>

    @stack('scripts')

</body>
</html>
