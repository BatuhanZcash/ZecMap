@php
    $adminNavItems = [
        ['label' => 'Genel Bakış', 'path' => 'admin/dashboard'],
        ['label' => 'İşletmeler', 'path' => 'admin/isletmeler'],
        ['label' => 'Kategoriler', 'path' => 'admin/kategoriler'],
        ['label' => 'Moderasyon', 'path' => 'admin/moderasyon'],
        ['label' => 'Doğrulama', 'path' => 'admin/dogrulama'],
        ['label' => 'Güncelleme İstekleri', 'path' => 'admin/guncelleme-istekleri'],
        ['label' => 'Ülke Aracı', 'path' => 'admin/ulke-araci'],
        ['label' => 'Yorumlar', 'path' => 'admin/yorumlar'],
        ['label' => 'Bağış Başvuruları', 'path' => 'admin/charity'],
        ['label' => 'Bağışlar', 'path' => 'admin/donations'],
        ['label' => 'Blog', 'path' => 'admin/blog'],
        ['label' => 'Denetim', 'path' => 'admin/audit'],
        ['label' => 'Kullanıcılar', 'path' => 'admin/users'],
        ['label' => 'Ayarlar', 'path' => 'admin/settings'],
        ['label' => 'Destekleyenler', 'path' => 'admin/supporters'],
        ['label' => 'Accept ZEC', 'path' => 'admin/accept-zec'],
        ['label' => 'Topluluklar', 'path' => 'admin/topluluklar'],
    ];
@endphp

<header class="admin-top">
    <a class="brand group flex items-center gap-2.5" aria-label="ZecMap Admin" href="{{ url('/admin/dashboard') }}">
        <span class="relative inline-flex items-center justify-center shrink-0">
            <svg width="28" height="32" viewBox="0 0 30 34" fill="none" xmlns="http://www.w3.org/2000/svg"
                class="transition-transform duration-300 group-hover:-translate-y-0.5">
                <defs>
                    <linearGradient id="adminPinGrad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#FFD54F"></stop>
                        <stop offset="100%" stop-color="#F3B724"></stop>
                    </linearGradient>
                </defs>
                <path d="M15 1C7.82 1 2 6.82 2 14c0 9 13 19 13 19s13-10 13-19C28 6.82 22.18 1 15 1Z"
                    fill="url(#adminPinGrad)" stroke="#000" stroke-opacity="0.18" stroke-width="1"></path>
                <path d="M10 9h10l-7.5 9H20" stroke="#0a0a0a" stroke-width="2.4" stroke-linecap="round"
                    stroke-linejoin="round" fill="none"></path>
            </svg>
            <span
                class="absolute inset-0 -z-10 rounded-full blur-xl bg-primary/40 opacity-60 group-hover:opacity-90 transition-opacity"></span>
        </span>

    </a>

    <nav class="admin-nav">
        @foreach ($adminNavItems as $item)
            @php
                $isActive = request()->is($item['path']);
            @endphp
            <a class="nav-link{{ $isActive ? ' active' : '' }}" href="{{ url('/' . $item['path']) }}"
                @if ($isActive) aria-current="page" @endif>{{ $item['label'] }}</a>
        @endforeach
    </nav>

    <form method="POST" action="{{ route('admin.logout') }}" style="display: inline;">
        @csrf
        <button type="submit" class="btn ghost">Çıkış Yap</button>
    </form>
</header>
