<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – {{ \App\Models\Setting::getValue('nama_gym') ?? 'GymPro' }}</title>
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

        /* subtle grid bg */
        body::before {
            content: '';
            position: fixed; inset: 0;
            background-image:
                linear-gradient(rgba(16,185,129,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(16,185,129,0.04) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        /* glow blob */
        body::after {
            content: '';
            position: fixed;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(16,185,129,0.12) 0%, transparent 70%);
            top: -100px; left: 50%; transform: translateX(-50%);
            pointer-events: none;
        }

        .card {
            background: #fff;
            border-radius: 20px;
            width: 100%; max-width: 400px;
            padding: 36px 32px;
            position: relative; z-index: 1;
            box-shadow: 0 24px 60px rgba(0,0,0,0.3);
        }

        .logo-wrap {
            display: flex; align-items: center; justify-content: center;
            gap: 10px; margin-bottom: 28px;
        }
        .logo-icon {
            width: 38px; height: 38px; border-radius: 11px;
            background: linear-gradient(135deg, #10b981, #059669);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 14px rgba(16,185,129,0.35);
        }
        .logo-icon i { color: #fff; font-size: 15px; }
        .logo-name { font-size: 17px; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; }

        h1 {
            font-size: 20px; font-weight: 800; color: #0f172a;
            text-align: center; margin-bottom: 4px; letter-spacing: -0.02em;
        }
        .subtitle { font-size: 12.5px; color: #94a3b8; text-align: center; margin-bottom: 28px; }

        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block; font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.08em;
            color: #64748b; margin-bottom: 6px;
        }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
            color: #94a3b8; font-size: 12px; pointer-events: none;
        }
        .form-input {
            width: 100%; border: 1.5px solid #e2e8f0; border-radius: 10px;
            padding: 10px 13px 10px 36px;
            font-size: 13px; color: #0f172a; background: #f8fafc;
            outline: none; transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
            font-family: inherit;
        }
        .form-input:focus {
            border-color: #10b981; background: #fff;
            box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
        }
        .form-input.error { border-color: #f87171; }
        .form-error { font-size: 11px; color: #ef4444; margin-top: 5px; }

        .toggle-pw {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            color: #94a3b8; cursor: pointer; font-size: 12px;
            transition: color 0.15s; background: none; border: none; padding: 0;
        }
        .toggle-pw:hover { color: #475569; }

        .remember-row {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 20px;
        }
        .remember-label {
            display: flex; align-items: center; gap: 7px;
            font-size: 12px; color: #64748b; cursor: pointer; font-weight: 500;
        }
        .remember-check {
            width: 15px; height: 15px; border-radius: 4px;
            border: 1.5px solid #cbd5e1; accent-color: #10b981; cursor: pointer;
        }
        .forgot-link {
            font-size: 12px; color: #10b981; font-weight: 600;
            text-decoration: none; transition: color 0.15s;
        }
        .forgot-link:hover { color: #059669; }

        .btn-submit {
            width: 100%; background: #10b981; color: #fff;
            font-size: 13.5px; font-weight: 700; padding: 12px;
            border-radius: 11px; border: none; cursor: pointer;
            transition: background 0.15s, transform 0.1s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            font-family: inherit; letter-spacing: -0.01em;
        }
        .btn-submit:hover { background: #059669; }
        .btn-submit:active { transform: scale(0.98); }

        .divider {
            display: flex; align-items: center; gap: 10px;
            margin: 20px 0; font-size: 11px; color: #cbd5e1;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; height: 1px; background: #f1f5f9;
        }

        .alert-error {
            background: #fef2f2; border: 1px solid #fecaca;
            border-radius: 10px; padding: 10px 14px;
            font-size: 12px; color: #dc2626; margin-bottom: 20px;
            display: flex; align-items: flex-start; gap: 8px;
        }
        .alert-status {
            background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.2);
            border-radius: 10px; padding: 10px 14px;
            font-size: 12px; color: #059669; margin-bottom: 20px;
        }

        .footer-link {
            text-align: center; font-size: 12px; color: #94a3b8; margin-top: 20px;
        }
        .footer-link a { color: #10b981; font-weight: 600; text-decoration: none; }
        .footer-link a:hover { color: #059669; }
    </style>
</head>
<body>

<div class="card">

    {{-- Logo --}}
    <div class="logo-wrap">
        <div class="logo-icon"><i class="fa-solid fa-dumbbell"></i></div>
        <span class="logo-name">{{ \App\Models\Setting::getValue('nama_gym') ?? 'GymPro' }}</span>
    </div>

    <h1>Selamat Datang</h1>
    <p class="subtitle">Masuk ke panel admin gym Anda</p>

    {{-- Session status --}}
    @if (session('status'))
        <div class="alert-status">
            <i class="fa-solid fa-circle-check mr-1.5"></i>{{ session('status') }}
        </div>
    @endif

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

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <div class="input-wrap">
                <i class="fa-regular fa-envelope input-icon"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                    class="form-input {{ $errors->get('email') ? 'error' : '' }}"
                    placeholder="admin@email.com" required autofocus autocomplete="username">
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
                    placeholder="••••••••" required autocomplete="current-password">
                <button type="button" class="toggle-pw" onclick="togglePw('password', this)" tabindex="-1">
                    <i class="fa-regular fa-eye"></i>
                </button>
            </div>
            @foreach ($errors->get('password') as $msg)
                <div class="form-error">{{ $msg }}</div>
            @endforeach
        </div>

        {{-- Remember + Forgot --}}
        <div class="remember-row">
            <label class="remember-label">
                <input type="checkbox" name="remember" id="remember_me" class="remember-check">
                Ingat saya
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
            @endif
        </div>

        <button type="submit" class="btn-submit">
            <i class="fa-solid fa-right-to-bracket text-[13px]"></i> Masuk
        </button>
    </form>
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
</script>
</body>
</html>