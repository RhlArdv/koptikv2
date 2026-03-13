<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pesan Menu') — Kopi Titik</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/css/order.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body style="margin:0;padding:0;background:#F8F9FA;font-family:'DM Sans',sans-serif;">
    @yield('content')
    @stack('scripts')
</body>
</html>