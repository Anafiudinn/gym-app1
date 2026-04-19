<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register – {{ \App\Models\Setting::getValue('nama_gym') ?? 'GymPro' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('logo-gym.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; }

        body {
            min-height: 100vh;
            background: #0a0f1e;
            display: flex; align-items: center; justify-content: center;
            padding: 20px;
            position: relative; overflow: hidden;
        }
        body::before {
            content: '';
            position: fixed; inset: 0;
            background-image:
                linear-gradient(rgba(16,185,129,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(16,185,129,0.04) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(16,185,129,0.12) 0%, transparent 70%);
            top: -100px; left: 50%; transform: translateX(-50%);
            pointer-events: none;
        }

        .card {
            background: #fff; border-radius: 20px;
            width: 100%; max-width: 420px;
            padding: 36px 32px;
            position: relative; z-index: 1;
            box-shadow: 0 24px 60px rgba(0,0,0,0.3);
        }

        .logo-wrap {
            display: flex; align-items: center; justify-content: center;
            gap: 10px; margin-bottom: 24px;
        }
        .logo-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, #10b981, #059669);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 14px rgba(16,185,129,0.3);
        }
        .logo-icon i { color: #fff; font-size: 14px; }
        .logo-name { font-size: 16px; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; }

        h1 { font-size: 19px; font-weight: 800; color: #0f172a; text-align: center; margin-bottom: 4px; letter-spacing: -0.02em; }
        .subtitle { font-size: 12.5px; color: #94a3b8; text-align: center; margin-bottom: 24px; }

        .form-group { margin-bottom: 14px; }
        .form-label {
            display: block; font-size: 10.5px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.08em;
            color: #64748b; margin-bottom: 5px;
        }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            color: #94a3b8; font-size: 11.5px; pointer-events: none;
        }
        .form-input {
            width: 100%; border: 1.5px solid #e2e8f0; border-radius: 10px;
            padding: 9px 13px 9px 34px;
            font-size: 13px; color: #0f172a; background: #f8fafc;
            outline: none; transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
            font-family: inherit;
        }
        .form-input:focus {
            border-color: #10b981; background: #fff;
            box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
        }
        .form-input.error { border-color: #f87171; }
        .form-error { font-size: 11px; color: #ef4444; margin-top: 4px; }

        .toggle-pw {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            color: #94a3b8; cursor: pointer; font-size: 12px;
            transition: color 0.15s; background: none; border: none; padding: 0;
        }
        .toggle-pw:hover { color: #475569; }

        /* Password strength bar */
        .pw-strength { margin-top: 6px; }
        .pw-bar-track { height: 3px; background: #f1f5f9; border-radius: 99px; overflow: hidden; }
        .pw-bar-fill { height: 100%; border-radius: 99px; transition: width 0.3s, background 0.3s; width: 0; }
        .pw-hint { font-size: 10px; color: #94a3b8; margin-top: 3px; }

        .btn-submit {
            width: 100%; background: #10b981; color: #fff;
            font-size: 13.5px; font-weight: 700; padding: 12px;
            border-radius: 11px; border: none; cursor: pointer;
            transition: background 0.15s, transform 0.1s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            font-family: inherit; margin-top: 20px; letter-spacing: -0.01em;
        }
        .btn-submit:hover { background: #059669; }
        .btn-submit:active { transform: scale(0.98); }

        .footer-link {
            text-align: center; font-size: 12px; color: #94a3b8; margin-top: 18px;
        }
        .footer-link a { color: #10b981; font-weight: 600; text-decoration: none; }
        .footer-link a:hover { color: #059669; }

        .alert-error {
            background: #fef2f2; border: 1px solid #fecaca;
            border-radius: 10px; padding: 10px 14px;
            font-size: 12px; color: #dc2626; margin-bottom: 18px;
            display: flex; align-items: flex-start; gap: 8px;
        }
    </style>
</head>
<body>

<div class="card">

    {{-- Logo --}}
    <div class="logo-wrap">
        <div class="logo-icon"><i class="fa-solid fa-dumbbell"></i></div>
        <span class="logo-name">{{ \App\Models\Setting::getValue('nama_gym') ?? 'GymPro' }}</span>
    </div>

    <h1>Buat Akun Baru</h1>
    <p class="subtitle">Daftarkan akun admin gym Anda</p>

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="alert-error">
            <i class="fa-solid fa-circle-exclamation mt-0.5 flex-shrink-0"></i>
            <div>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Name --}}
        <div class="form-group">
            <label class="form-label" for="name">Nama Lengkap</label>
            <div class="input-wrap">
                <i class="fa-regular fa-user input-icon"></i>
                <input id="name" type="text" name="name" value="{{ old('name') }}"
                    class="form-input {{ $errors->get('name') ? 'error' : '' }}"
                    placeholder="John Doe" required autofocus autocomplete="name">
            </div>
            @foreach ($errors->get('name') as $msg)
                <div class="form-error">{{ $msg }}</div>
            @endforeach
        </div>

        {{-- Email --}}
        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <div class="input-wrap">
                <i class="fa-regular fa-envelope input-icon"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                    class="form-input {{ $errors->get('email') ? 'error' : '' }}"
                    placeholder="admin@email.com" required autocomplete="username">
            </div>
            @foreach ($errors->get('email') as $msg)
                <div class="form-error">{{ $msg }}</div>
            @endforeach
        </div>

        {{-- Password --}}
        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <div class="input-wrap">
                <i class="fa-solid fa-lock input-icon"></i>
                <input id="password" type="password" name="password"
                    class="form-input {{ $errors->get('password') ? 'error' : '' }}"
                    placeholder="Min. 8 karakter" required autocomplete="new-password"
                    oninput="checkStrength(this.value)">
                <button type="button" class="toggle-pw" onclick="togglePw('password', this)" tabindex="-1">
                    <i class="fa-regular fa-eye"></i>
                </button>
            </div>
            {{-- Strength bar --}}
            <div class="pw-strength">
                <div class="pw-bar-track">
                    <div class="pw-bar-fill" id="pw-bar"></div>
                </div>
                <div class="pw-hint" id="pw-hint">Masukkan password</div>
            </div>
            @foreach ($errors->get('password') as $msg)
                <div class="form-error">{{ $msg }}</div>
            @endforeach
        </div>

        {{-- Confirm Password --}}
        <div class="form-group">
            <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
            <div class="input-wrap">
                <i class="fa-solid fa-lock input-icon"></i>
                <input id="password_confirmation" type="password" name="password_confirmation"
                    class="form-input {{ $errors->get('password_confirmation') ? 'error' : '' }}"
                    placeholder="Ulangi password" required autocomplete="new-password"
                    oninput="checkMatch(this.value)">
                <button type="button" class="toggle-pw" onclick="togglePw('password_confirmation', this)" tabindex="-1">
                    <i class="fa-regular fa-eye"></i>
                </button>
            </div>
            <div class="form-error" id="match-error" style="display:none;">Password tidak cocok</div>
            @foreach ($errors->get('password_confirmation') as $msg)
                <div class="form-error">{{ $msg }}</div>
            @endforeach
        </div>

        <button type="submit" class="btn-submit">
            <i class="fa-solid fa-user-plus text-[12px]"></i> Daftarkan Akun
        </button>
    </form>

    <div class="footer-link">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
    </div>
</div>

<script>
    function togglePw(inputId, btn) {
        var input = document.getElementById(inputId);
        var icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fa-regular fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fa-regular fa-eye';
        }
    }

    function checkStrength(val) {
        var bar  = document.getElementById('pw-bar');
        var hint = document.getElementById('pw-hint');
        var score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        var configs = [
            { w: '0%',   bg: '#e2e8f0', text: 'Masukkan password' },
            { w: '25%',  bg: '#ef4444', text: 'Lemah' },
            { w: '50%',  bg: '#f97316', text: 'Cukup' },
            { w: '75%',  bg: '#eab308', text: 'Kuat' },
            { w: '100%', bg: '#10b981', text: 'Sangat kuat' },
        ];
        var c = val.length === 0 ? configs[0] : configs[score] || configs[1];
        bar.style.width      = c.w;
        bar.style.background = c.bg;
        hint.textContent     = c.text;
        hint.style.color     = val.length === 0 ? '#94a3b8' : c.bg;
    }

    function checkMatch(confirmVal) {
        var pwVal = document.getElementById('password').value;
        var err   = document.getElementById('match-error');
        err.style.display = (confirmVal && confirmVal !== pwVal) ? 'block' : 'none';
    }
</script>
</body>
</html>