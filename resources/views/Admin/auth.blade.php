<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }} Admin — Giriş</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>

    <style type="text/css"
        data-vite-dev-id="/Applications/XAMPP/xamppfiles/htdocs/zecmap/apps/admin/src/pages/LoginPage.css">
        .zlogin {
            position: relative;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
            overflow: hidden;
            background: #050505;
        }

        /* ── Arka plan ──────────────────────────────────────────────────────────── */
        .zlogin__bg {
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .zlogin__orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.5;
            will-change: transform;
        }

        .zlogin__orb--1 {
            width: 520px;
            height: 520px;
            background: radial-gradient(circle, rgba(255, 193, 7, 0.45) 0%, transparent 70%);
            top: -10%;
            left: -8%;
            animation: zlogin-orb1 18s ease-in-out infinite;
        }

        .zlogin__orb--2 {
            width: 480px;
            height: 480px;
            background: radial-gradient(circle, rgba(255, 152, 0, 0.4) 0%, transparent 70%);
            bottom: -12%;
            right: -10%;
            animation: zlogin-orb2 22s ease-in-out infinite;
        }

        .zlogin__orb--3 {
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, rgba(255, 213, 79, 0.32) 0%, transparent 70%);
            top: 35%;
            right: 28%;
            animation: zlogin-orb3 26s ease-in-out infinite;
        }

        @keyframes zlogin-orb1 {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(40vw, 30vh) scale(1.1);
            }
        }

        @keyframes zlogin-orb2 {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(-30vw, -25vh) scale(1.15);
            }
        }

        @keyframes zlogin-orb3 {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(-20vw, 25vh) scale(1.08);
            }

            66% {
                transform: translate(15vw, -15vh) scale(0.92);
            }
        }

        .zlogin__grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(to right, rgba(255, 193, 7, 0.04) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 193, 7, 0.04) 1px, transparent 1px);
            background-size: 60px 60px;
            -webkit-mask-image: radial-gradient(ellipse at center, black 30%, transparent 80%);
            mask-image: radial-gradient(ellipse at center, black 30%, transparent 80%);
        }

        .zlogin__noise {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle at 1px 1px, rgba(255, 193, 7, 0.18) 1px, transparent 0);
            background-size: 24px 24px;
            -webkit-mask-image: radial-gradient(ellipse at center, black 0%, transparent 60%);
            mask-image: radial-gradient(ellipse at center, black 0%, transparent 60%);
            opacity: 0.3;
        }

        /* ── Kart ───────────────────────────────────────────────────────────────── */
        .zlogin__card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
            padding: 2.5rem 2.25rem 2rem;
            background: linear-gradient(160deg, rgba(20, 21, 41, 0.85) 0%, rgba(11, 12, 20, 0.92) 100%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 1.5rem;
            backdrop-filter: blur(28px) saturate(180%);
            -webkit-backdrop-filter: blur(28px) saturate(180%);
            box-shadow:
                0 30px 80px -20px rgba(0, 0, 0, 0.7),
                0 0 0 1px rgba(255, 193, 7, 0.05),
                inset 0 1px 0 0 rgba(255, 255, 255, 0.06);
            animation: zlogin-card-in 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) backwards;
        }

        @keyframes zlogin-card-in {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.97);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .zlogin__card--shake {
            animation: zlogin-shake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97);
        }

        @keyframes zlogin-shake {

            10%,
            90% {
                transform: translateX(-2px);
            }

            20%,
            80% {
                transform: translateX(4px);
            }

            30%,
            50%,
            70% {
                transform: translateX(-7px);
            }

            40%,
            60% {
                transform: translateX(7px);
            }
        }

        /* ── Logo ───────────────────────────────────────────────────────────────── */
        .zlogin__logo {
            position: relative;
            width: 72px;
            height: 72px;
            margin: 0 auto 1.25rem;
        }

        .zlogin__logo-ring {
            position: absolute;
            inset: -6px;
            border-radius: 50%;
            background: conic-gradient(from 0deg, transparent, #FFC107, transparent, #FFC107, transparent);
            animation: zlogin-spin 4s linear infinite;
            opacity: 0.65;
            filter: blur(2px);
        }

        .zlogin__logo-inner {
            position: relative;
            width: 100%;
            height: 100%;
            border-radius: 1.25rem;
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.14), rgba(255, 152, 0, 0.07));
            border: 1px solid rgba(255, 193, 7, 0.3);
            display: grid;
            place-items: center;
            box-shadow:
                inset 0 0 30px rgba(255, 193, 7, 0.15),
                0 0 24px rgba(255, 193, 7, 0.18);
        }

        .zlogin__logo-inner span {
            font-size: 2rem;
            font-weight: 900;
            background: linear-gradient(135deg, #FFD93D, #FFC107, #F59E0B);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -0.02em;
        }

        @keyframes zlogin-spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ── Başlık ─────────────────────────────────────────────────────────────── */
        .zlogin__head {
            text-align: center;
            margin-bottom: 1.75rem;
        }

        .zlogin__title {
            margin: 0 0 0.4rem;
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #f5f5f7;
            text-transform: uppercase;
        }

        .zlogin__title-accent {
            background: linear-gradient(135deg, #FFD93D, #F59E0B);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .zlogin__subtitle {
            margin: 0;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: rgba(229, 231, 235, 0.45);
        }

        /* ── Form ───────────────────────────────────────────────────────────────── */
        .zlogin__form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .zlogin__field {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .zlogin__label {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.78rem;
            font-weight: 600;
            color: rgba(229, 231, 235, 0.7);
            letter-spacing: 0.02em;
        }

        .zlogin__label svg {
            color: #FFC107;
            opacity: 0.75;
        }

        .zlogin__input {
            width: 100%;
            padding: 0.85rem 0.95rem;
            font: inherit;
            font-size: 0.95rem;
            color: #f5f5f7;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.09);
            border-radius: 0.75rem;
            outline: none;
            transition: all 0.2s ease;
        }

        .zlogin__input::placeholder {
            color: rgba(229, 231, 235, 0.3);
        }

        .zlogin__input:hover:not(:disabled) {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.14);
        }

        .zlogin__input:focus {
            background: rgba(255, 193, 7, 0.04);
            border-color: rgba(255, 193, 7, 0.45);
            box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.1);
        }

        .zlogin__input:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .zlogin__pw-wrap {
            position: relative;
        }

        .zlogin__pw-toggle {
            position: absolute;
            top: 50%;
            right: 0.6rem;
            transform: translateY(-50%);
            display: grid;
            place-items: center;
            width: 32px;
            height: 32px;
            background: transparent;
            border: none;
            color: rgba(229, 231, 235, 0.5);
            cursor: pointer;
            border-radius: 0.5rem;
            transition: all 0.15s ease;
        }

        .zlogin__pw-toggle:hover {
            color: #FFC107;
            background: rgba(255, 193, 7, 0.08);
        }

        /* ── Hata ───────────────────────────────────────────────────────────────── */
        .zlogin__error {
            display: flex;
            align-items: flex-start;
            gap: 0.65rem;
            padding: 0.75rem 0.85rem;
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.25);
            border-radius: 0.65rem;
            animation: zlogin-error-in 0.3s ease;
        }

        .zlogin__error span {
            display: grid;
            place-items: center;
            width: 22px;
            height: 22px;
            flex-shrink: 0;
            border-radius: 50%;
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            font-size: 0.8rem;
            font-weight: 900;
        }

        .zlogin__error p {
            margin: 0;
            font-size: 0.85rem;
            color: #fca5a5;
            line-height: 1.4;
        }

        @keyframes zlogin-error-in {
            from {
                opacity: 0;
                transform: translateY(-4px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ── Submit ─────────────────────────────────────────────────────────────── */
        .zlogin__submit {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.95rem 1rem;
            margin-top: 0.5rem;
            font: inherit;
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: 0.01em;
            color: #1a1a1a;
            background: linear-gradient(135deg, #FFD93D 0%, #FFC107 50%, #F59E0B 100%);
            border: none;
            border-radius: 0.75rem;
            cursor: pointer;
            overflow: hidden;
            transition: all 0.2s ease;
            box-shadow:
                0 8px 20px -6px rgba(255, 193, 7, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.4);
        }

        .zlogin__submit::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, transparent 0%, rgba(255, 255, 255, 0.18) 50%, transparent 100%);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }

        .zlogin__submit:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow:
                0 12px 28px -6px rgba(255, 193, 7, 0.6),
                inset 0 1px 0 rgba(255, 255, 255, 0.4);
        }

        .zlogin__submit:hover:not(:disabled)::before {
            transform: translateX(100%);
        }

        .zlogin__submit:active:not(:disabled) {
            transform: translateY(0);
        }

        .zlogin__submit:disabled {
            opacity: 0.55;
            cursor: not-allowed;
        }

        /* ── Foot ───────────────────────────────────────────────────────────────── */
        .zlogin__foot {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            color: rgba(229, 231, 235, 0.4);
        }

        .zlogin__dot {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #4ade80;
            box-shadow: 0 0 8px rgba(74, 222, 128, 0.6);
            animation: zlogin-pulse 2s ease-in-out infinite;
        }

        @keyframes zlogin-pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }
        }

        .zlogin__page-foot {
            position: relative;
            z-index: 1;
            margin-top: 2rem;
            font-size: 0.7rem;
            color: rgba(229, 231, 235, 0.25);
            letter-spacing: 0.04em;
        }

        /* ── Yükleniyor ─────────────────────────────────────────────────────────── */
        .zlogin__loading {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            color: rgba(229, 231, 235, 0.6);
            font-size: 0.9rem;
        }

        .zlogin__loading svg {
            color: #FFC107;
        }

        /* ── Spin ───────────────────────────────────────────────────────────────── */
        .spin {
            animation: zlogin-spin-svg 0.85s linear infinite;
        }

        @keyframes zlogin-spin-svg {
            to {
                transform: rotate(360deg);
            }
        }

        /* ── Reduced motion ─────────────────────────────────────────────────────── */
        @media (prefers-reduced-motion: reduce) {

            .zlogin__orb,
            .zlogin__logo-ring,
            .zlogin__dot,
            .spin {
                animation: none !important;
            }
        }
    </style>
</head>

<body cz-shortcut-listen="true">
    <div id="root">
        <div class="zlogin">
            <div class="zlogin__bg" aria-hidden="true">
                <div class="zlogin__orb zlogin__orb--1">
                </div>
                <div class="zlogin__orb zlogin__orb--2">
                </div>
                <div class="zlogin__orb zlogin__orb--3">
                </div>
                <div class="zlogin__grid">

                </div>
                <div class="zlogin__noise">
                </div>
            </div>
            <div @class(['zlogin__card', 'zlogin__card--shake' => $errors->any()])>
                <div class="zlogin__logo">
                    <svg width="64" height="74" viewBox="0 0 30 34" fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        style="filter: drop-shadow(rgba(243, 183, 36, 0.45) 0px 8px 24px);">
                        <defs>
                            <linearGradient id="loginPinGrad" x1="0" y1="0" x2="0"
                                y2="1">
                                <stop offset="0%" stop-color="#FFD54F"></stop>
                                <stop offset="100%" stop-color="#F3B724"></stop>
                            </linearGradient>
                        </defs>
                        <path d="M15 1C7.82 1 2 6.82 2 14c0 9 13 19 13 19s13-10 13-19C28 6.82 22.18 1 15 1Z"
                            fill="url(#loginPinGrad)" stroke="#000" stroke-opacity="0.18" stroke-width="1"></path>
                        <path d="M10 9h10l-7.5 9H20" stroke="#0a0a0a" stroke-width="2.4" stroke-linecap="round"
                            stroke-linejoin="round" fill="none"></path>
                    </svg>
                </div>
                <div class="zlogin__head">
                    <h1 class="zlogin__title">
                        <span class="zlogin__title-accent">zec</span>map <span
                            style="font-size: 0.55em; vertical-align: middle; margin-left: 8px; padding: 3px 8px; border: 1px solid rgba(243, 183, 36, 0.3); border-radius: 4px; color: rgb(243, 183, 36); background: rgba(243, 183, 36, 0.08); letter-spacing: 0.2em; font-weight: 700;">ADMIN</span>
                    </h1>
                    <p class="zlogin__subtitle">Yönetim Paneli</p>
                </div>
                <form class="zlogin__form" method="POST" action="{{ route('admin.login.store') }}" autocomplete="on">
                    @csrf
                    @if ($errors->any())
                        <div class="zlogin__error" role="alert">
                            <span>!</span>
                            <p>{{ $errors->first() }}</p>
                        </div>
                    @endif
                    <div class="zlogin__field">
                        <label for="email" class="zlogin__label">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="2" y="4" width="20" height="16" rx="2">

                                </rect>
                                <path d="m22 7-10 5L2 7">

                                </path>
                            </svg>
                            <span>E-posta</span>
                        </label>
                        <input id="email" name="email" autocomplete="email" required
                            placeholder="admin@gmail.com" class="zlogin__input" type="email"
                            value="{{ old('email') }}">
                    </div>
                    <div class="zlogin__field">
                        <label for="password" class="zlogin__label">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="3" y="11" width="18" height="11" rx="2">

                                </rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4">

                                </path>
                            </svg>
                            <span>Şifre</span>
                        </label>
                        <div class="zlogin__pw-wrap">
                            <input id="password" name="password" autocomplete="current-password" required
                                placeholder="••••••••" class="zlogin__input" type="password">
                            <button type="button" class="zlogin__pw-toggle" aria-label="Şifreyi göster" tabindex="-1">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" aria-hidden="true">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z">

                                    </path>
                                    <circle cx="12" cy="12" r="3">

                                    </circle>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="zlogin__submit">
                        <span>Giriş Yap</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14">

                            </path>
                            <path d="m12 5 7 7-7 7">

                            </path>
                        </svg>
                    </button>
                </form>
                <div class="zlogin__foot">
                    <span class="zlogin__dot">
                    </span>
                    <span>Yalnızca yetkili admin
                        hesapları</span>
                </div>
            </div>
            <footer class="zlogin__page-foot">© {{ date('Y') }} ZecMap · Admin Console</footer>
        </div>
    </div>
    <script src="{{ asset('assets/js/admin-auth.js') }}" defer></script>
</body>

</html>
