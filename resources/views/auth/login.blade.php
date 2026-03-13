<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Kopi Titik</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:           #F8F9FA;
            --surface:      #FFFFFF;
            --border:       #E5E7EB;
            --border-focus: #9CA3AF;
            --text:         #111827;
            --text-soft:    #6B7280;
            --text-muted:   #9CA3AF;
            --accent:       #111827;
            --danger:       #EF4444;
            --bean:         #6B3F1F;
        }

        html, body { height: 100%; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px 20px;
            position: relative;
            overflow: hidden;
        }

        /* ─── Fixed bean decorations ─── */
        .bean-deco {
            position: fixed;
            pointer-events: none;
            z-index: 0;
        }

        /* slow gentle rotation on hover-free elements */
        @keyframes floatA {
            0%, 100% { transform: translateY(0px) rotate(var(--r)); }
            50%       { transform: translateY(-8px) rotate(calc(var(--r) + 6deg)); }
        }
        @keyframes floatB {
            0%, 100% { transform: translateY(0px) rotate(var(--r)); }
            50%       { transform: translateY(8px) rotate(calc(var(--r) - 5deg)); }
        }

        .float-a { animation: floatA 6s ease-in-out infinite; }
        .float-b { animation: floatB 7s ease-in-out infinite; }
        .float-c { animation: floatA 8s ease-in-out infinite 1s; }
        .float-d { animation: floatB 5.5s ease-in-out infinite 0.5s; }
        .float-e { animation: floatA 7.5s ease-in-out infinite 2s; }
        .float-f { animation: floatB 6.5s ease-in-out infinite 1.5s; }
        .float-g { animation: floatA 9s ease-in-out infinite 0.8s; }
        .float-h { animation: floatB 6s ease-in-out infinite 2.5s; }

        /* ─── Card ─── */
        .card {
            position: relative;
            z-index: 1;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 40px 36px 36px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 20px rgba(0,0,0,0.05);
            animation: appear 0.5s cubic-bezier(0.16,1,0.3,1) both;
        }

        @keyframes appear {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ─── Logo ─── */
        .logo-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 24px;
        }
        .logo-img {
            width: 160px;
            height: auto;
            display: block;
            object-fit: contain;
        }

        /* ─── Divider ─── */
        .divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 22px;
        }
        .divider-line { flex: 1; height: 1px; background: var(--border); }
        .divider-bean { opacity: 0.28; flex-shrink: 0; }

        /* ─── Heading ─── */
        .heading { margin-bottom: 22px; }
        .heading h2 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text);
            letter-spacing: -0.01em;
        }
        .heading p {
            margin-top: 3px;
            font-size: 13px;
            color: var(--text-soft);
        }

        /* ─── Status ─── */
        .status-msg {
            background: #F0FDF4;
            border: 1px solid #BBF7D0;
            border-radius: 8px;
            padding: 10px 13px;
            font-size: 13px;
            color: #15803D;
            margin-bottom: 16px;
        }

        /* ─── Field ─── */
        .field { margin-bottom: 14px; }
        .field-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
        }
        .field-top label { font-size: 13px; font-weight: 500; color: var(--text); }
        .field-top a { font-size: 12px; color: var(--text-soft); text-decoration: none; transition: color 0.15s; }
        .field-top a:hover { color: var(--text); }

        .field-wrap { position: relative; }
        .field-wrap input {
            width: 100%;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 10px 38px 10px 13px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 400;
            color: var(--text);
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
        }
        .field-wrap input::placeholder { color: var(--text-muted); }
        .field-wrap input:focus {
            border-color: var(--border-focus);
            background: var(--surface);
            box-shadow: 0 0 0 3px rgba(17,24,39,0.06);
        }
        .field-wrap input.is-error { border-color: var(--danger); }

        .field-icon {
            position: absolute;
            right: 12px; top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            display: flex;
            cursor: pointer;
            transition: color 0.15s;
        }
        .field-icon:hover { color: var(--text-soft); }
        .field-error { margin-top: 5px; font-size: 12px; color: var(--danger); }

        /* ─── Remember ─── */
        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 18px;
            margin-top: 6px;
        }
        .remember-row input[type="checkbox"] {
            appearance: none; -webkit-appearance: none;
            width: 15px; height: 15px;
            border: 1.5px solid var(--border);
            border-radius: 4px;
            background: var(--bg);
            cursor: pointer;
            position: relative;
            flex-shrink: 0;
            transition: all 0.15s;
        }
        .remember-row input[type="checkbox"]:checked { background: var(--accent); border-color: var(--accent); }
        .remember-row input[type="checkbox"]:checked::after {
            content: '';
            position: absolute;
            left: 3.5px; top: 1px;
            width: 5px; height: 9px;
            border: 1.5px solid #fff;
            border-top: none; border-left: none;
            transform: rotate(45deg);
        }
        .remember-row label { font-size: 13px; color: var(--text-soft); cursor: pointer; user-select: none; }

        /* ─── Submit ─── */
        .btn-submit {
            width: 100%;
            padding: 11px;
            background: var(--accent);
            border: none;
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 500;
            color: #fff;
            cursor: pointer;
            transition: opacity 0.15s, transform 0.1s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-submit:hover  { opacity: 0.82; }
        .btn-submit:active { transform: scale(0.99); }

        /* ─── Footer ─── */
        .footer { margin-top: 20px; text-align: center; }
        .footer p { font-size: 11.5px; color: var(--text-muted); }
    </style>
</head>
<body>

    {{-- ═══════════════════════════════════════
         COFFEE BEAN DECORATIONS — all corners
         Opacity 0.18–0.22, larger & clearly visible
    ═══════════════════════════════════════ --}}

    {{-- TOP-LEFT: 3 beans --}}
    <svg class="bean-deco float-a" style="--r:-30deg; top:18px; left:28px; width:64px; opacity:.18;" viewBox="0 0 80 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="40" cy="24" rx="38" ry="22" fill="#6B3F1F"/>
        <path d="M40 2 C36 12 36 36 40 46" stroke="#F8F9FA" stroke-width="2.5" stroke-linecap="round"/>
    </svg>

    <svg class="bean-deco float-b" style="--r:20deg; top:68px; left:10px; width:44px; opacity:.14;" viewBox="0 0 80 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="40" cy="24" rx="38" ry="22" fill="#6B3F1F"/>
        <path d="M40 2 C36 12 36 36 40 46" stroke="#F8F9FA" stroke-width="2.5" stroke-linecap="round"/>
    </svg>

    <svg class="bean-deco float-c" style="--r:55deg; top:14px; left:80px; width:52px; opacity:.12;" viewBox="0 0 80 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="40" cy="24" rx="38" ry="22" fill="#6B3F1F"/>
        <path d="M40 2 C36 12 36 36 40 46" stroke="#F8F9FA" stroke-width="2.5" stroke-linecap="round"/>
    </svg>

    {{-- TOP-RIGHT: 3 beans --}}
    <svg class="bean-deco float-d" style="--r:15deg; top:22px; right:32px; width:68px; opacity:.18;" viewBox="0 0 80 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="40" cy="24" rx="38" ry="22" fill="#6B3F1F"/>
        <path d="M40 2 C36 12 36 36 40 46" stroke="#F8F9FA" stroke-width="2.5" stroke-linecap="round"/>
    </svg>

    <svg class="bean-deco float-e" style="--r:-50deg; top:76px; right:18px; width:48px; opacity:.14;" viewBox="0 0 80 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="40" cy="24" rx="38" ry="22" fill="#6B3F1F"/>
        <path d="M40 2 C36 12 36 36 40 46" stroke="#F8F9FA" stroke-width="2.5" stroke-linecap="round"/>
    </svg>

    <svg class="bean-deco float-f" style="--r:70deg; top:10px; right:88px; width:42px; opacity:.12;" viewBox="0 0 80 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="40" cy="24" rx="38" ry="22" fill="#6B3F1F"/>
        <path d="M40 2 C36 12 36 36 40 46" stroke="#F8F9FA" stroke-width="2.5" stroke-linecap="round"/>
    </svg>

    {{-- BOTTOM-LEFT: 3 beans --}}
    <svg class="bean-deco float-g" style="--r:40deg; bottom:24px; left:24px; width:62px; opacity:.18;" viewBox="0 0 80 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="40" cy="24" rx="38" ry="22" fill="#6B3F1F"/>
        <path d="M40 2 C36 12 36 36 40 46" stroke="#F8F9FA" stroke-width="2.5" stroke-linecap="round"/>
    </svg>

    <svg class="bean-deco float-h" style="--r:-20deg; bottom:76px; left:14px; width:46px; opacity:.13;" viewBox="0 0 80 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="40" cy="24" rx="38" ry="22" fill="#6B3F1F"/>
        <path d="M40 2 C36 12 36 36 40 46" stroke="#F8F9FA" stroke-width="2.5" stroke-linecap="round"/>
    </svg>

    <svg class="bean-deco float-a" style="--r:65deg; bottom:18px; left:78px; width:50px; opacity:.11;" viewBox="0 0 80 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="40" cy="24" rx="38" ry="22" fill="#6B3F1F"/>
        <path d="M40 2 C36 12 36 36 40 46" stroke="#F8F9FA" stroke-width="2.5" stroke-linecap="round"/>
    </svg>

    {{-- BOTTOM-RIGHT: 3 beans --}}
    <svg class="bean-deco float-b" style="--r:-35deg; bottom:20px; right:30px; width:66px; opacity:.18;" viewBox="0 0 80 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="40" cy="24" rx="38" ry="22" fill="#6B3F1F"/>
        <path d="M40 2 C36 12 36 36 40 46" stroke="#F8F9FA" stroke-width="2.5" stroke-linecap="round"/>
    </svg>

    <svg class="bean-deco float-c" style="--r:50deg; bottom:72px; right:16px; width:46px; opacity:.13;" viewBox="0 0 80 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="40" cy="24" rx="38" ry="22" fill="#6B3F1F"/>
        <path d="M40 2 C36 12 36 36 40 46" stroke="#F8F9FA" stroke-width="2.5" stroke-linecap="round"/>
    </svg>

    <svg class="bean-deco float-d" style="--r:-70deg; bottom:14px; right:86px; width:44px; opacity:.11;" viewBox="0 0 80 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="40" cy="24" rx="38" ry="22" fill="#6B3F1F"/>
        <path d="M40 2 C36 12 36 36 40 46" stroke="#F8F9FA" stroke-width="2.5" stroke-linecap="round"/>
    </svg>

    {{-- MID-LEFT: 2 lone beans --}}
    <svg class="bean-deco float-e" style="--r:30deg; top:42%; left:16px; width:54px; opacity:.14;" viewBox="0 0 80 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="40" cy="24" rx="38" ry="22" fill="#6B3F1F"/>
        <path d="M40 2 C36 12 36 36 40 46" stroke="#F8F9FA" stroke-width="2.5" stroke-linecap="round"/>
    </svg>

    <svg class="bean-deco float-f" style="--r:-55deg; top:58%; left:8px; width:38px; opacity:.10;" viewBox="0 0 80 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="40" cy="24" rx="38" ry="22" fill="#6B3F1F"/>
        <path d="M40 2 C36 12 36 36 40 46" stroke="#F8F9FA" stroke-width="2.5" stroke-linecap="round"/>
    </svg>

    {{-- MID-RIGHT: 2 lone beans --}}
    <svg class="bean-deco float-g" style="--r:-25deg; top:38%; right:14px; width:58px; opacity:.14;" viewBox="0 0 80 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="40" cy="24" rx="38" ry="22" fill="#6B3F1F"/>
        <path d="M40 2 C36 12 36 36 40 46" stroke="#F8F9FA" stroke-width="2.5" stroke-linecap="round"/>
    </svg>

    <svg class="bean-deco float-h" style="--r:60deg; top:56%; right:6px; width:40px; opacity:.10;" viewBox="0 0 80 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="40" cy="24" rx="38" ry="22" fill="#6B3F1F"/>
        <path d="M40 2 C36 12 36 36 40 46" stroke="#F8F9FA" stroke-width="2.5" stroke-linecap="round"/>
    </svg>

    {{-- ═══ Card ═══ --}}
    <div class="card">

        {{-- Logo --}}
        <div class="logo-area">
            <img src="{{ asset('assets/img/logo.png') }}"
                 alt="Kopi Titik"
                 class="logo-img">
        </div>

        {{-- Divider with bean --}}
        <div class="divider">
            <div class="divider-line"></div>
            <svg class="divider-bean" width="20" height="12" viewBox="0 0 80 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <ellipse cx="40" cy="24" rx="38" ry="22" fill="#111827"/>
                <path d="M40 2 C36 12 36 36 40 46" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round"/>
            </svg>
            <div class="divider-line"></div>
        </div>

        {{-- Heading --}}
        <div class="heading">
            <h2>Masuk ke akun Anda</h2>
            <p>Sistem Informasi Reservasi Kopi Titik</p>
        </div>

        {{-- Status --}}
        @if (session('status'))
            <div class="status-msg">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email --}}
            <div class="field">
                <div class="field-top">
                    <label for="email">Email</label>
                </div>
                <div class="field-wrap">
                    <input type="email"
                           id="email" name="email"
                           value="{{ old('email') }}"
                           required autofocus autocomplete="username"
                           placeholder="admin@kopititik.com"
                           class="{{ $errors->has('email') ? 'is-error' : '' }}">
                    <span class="field-icon">
                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </span>
                </div>
                @error('email')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="field">
                <div class="field-top">
                    <label for="password">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">Lupa password?</a>
                    @endif
                </div>
                <div class="field-wrap">
                    <input type="password"
                           id="password" name="password"
                           required autocomplete="current-password"
                           placeholder="••••••••"
                           class="{{ $errors->has('password') ? 'is-error' : '' }}">
                    <span class="field-icon" onclick="togglePassword()">
                        <svg id="icon-eye" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg id="icon-eye-off" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </span>
                </div>
                @error('password')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember --}}
            <div class="remember-row">
                <input type="checkbox" id="remember_me" name="remember">
                <label for="remember_me">Ingat saya</label>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-submit">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 1v3M10 1v3M14 1v3"/>
                </svg>
                Masuk
            </button>

        </form>

        {{-- Footer --}}
        <div class="footer">
            <p>© {{ date('Y') }} Kopi Titik · Reservasi & Menu</p>
        </div>

    </div>

    <script>
        function togglePassword() {
            const input  = document.getElementById('password');
            const eyeOn  = document.getElementById('icon-eye');
            const eyeOff = document.getElementById('icon-eye-off');
            if (input.type === 'password') {
                input.type = 'text';
                eyeOn.style.display  = 'none';
                eyeOff.style.display = 'block';
            } else {
                input.type = 'password';
                eyeOn.style.display  = 'block';
                eyeOff.style.display = 'none';
            }
        }
    </script>

</body>
</html>