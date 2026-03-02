<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Kopi Titik</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'DM Sans', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-[#F3F4F6] flex items-center justify-center p-4">

    <div class="w-full max-w-[420px]">

        {{-- Logo & Brand --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-500
                        shadow-lg shadow-amber-200 mb-4">
                <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M2 21v-2h2V7a1 1 0 011-1h14a1 1 0 011 1v2h1a2 2 0 010 4h-1v5h2v2H2zm4-2h10v-5H6v5zm0-7h10V8H6v4zm12-2v-2h1a.5.5 0 000 1h-1v1z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Kopi Titik</h1>
            <p class="text-[13px] text-gray-500 mt-1">Masuk untuk melanjutkan</p>
        </div>

        {{-- Card Login --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-7">

            {{-- Session Status (misal: baru logout) --}}
            @if (session('status'))
                <div class="mb-4 text-sm text-green-700 bg-green-50 border border-green-200
                            rounded-xl px-4 py-3">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-4">
                    <label for="email"
                           class="block text-[13px] font-semibold text-gray-700 mb-1.5">
                        Email
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           autocomplete="username"
                           placeholder="admin@kopititik.com"
                           class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm
                                  text-gray-800 placeholder-gray-400
                                  focus:outline-none focus:border-amber-400 focus:ring-2
                                  focus:ring-amber-100 transition-all
                                  @error('email') border-red-400 bg-red-50 @enderror">
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password"
                               class="text-[13px] font-semibold text-gray-700">
                            Password
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="text-xs text-amber-600 hover:text-amber-700 font-medium">
                                Lupa password?
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <input type="password"
                               id="password"
                               name="password"
                               required
                               autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm
                                      text-gray-800 placeholder-gray-400 pr-11
                                      focus:outline-none focus:border-amber-400 focus:ring-2
                                      focus:ring-amber-100 transition-all
                                      @error('password') border-red-400 bg-red-50 @enderror">
                        {{-- Toggle show/hide password --}}
                        <button type="button"
                                onclick="togglePassword()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400
                                       hover:text-gray-600 transition-colors p-1">
                            <svg id="icon-eye" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="icon-eye-off" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember me --}}
                <div class="flex items-center gap-2 mb-5">
                    <input type="checkbox"
                           id="remember_me"
                           name="remember"
                           class="w-4 h-4 rounded border-gray-300 text-amber-500
                                  focus:ring-amber-400 focus:ring-offset-0 cursor-pointer">
                    <label for="remember_me" class="text-[13px] text-gray-600 cursor-pointer">
                        Ingat saya
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold
                               text-sm rounded-xl py-3 transition-all active:scale-95
                               shadow-sm shadow-amber-200">
                    Masuk
                </button>

            </form>

        </div>

        {{-- Footer --}}
        <p class="text-center text-xs text-gray-400 mt-6">
            © {{ date('Y') }} Kopi Titik — Sistem Informasi Reservasi Menu
        </p>

    </div>

    <script>
        function togglePassword() {
            const input   = document.getElementById('password');
            const iconEye = document.getElementById('icon-eye');
            const iconOff = document.getElementById('icon-eye-off');

            if (input.type === 'password') {
                input.type    = 'text';
                iconEye.classList.add('hidden');
                iconOff.classList.remove('hidden');
            } else {
                input.type    = 'password';
                iconEye.classList.remove('hidden');
                iconOff.classList.add('hidden');
            }
        }
    </script>

</body>
</html>