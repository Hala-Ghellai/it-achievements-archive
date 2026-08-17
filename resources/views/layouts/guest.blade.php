<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('heading', 'تسجيل الدخول') · {{ config('site.department_name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Noto+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">

    @stack('head')
    @vite(['resources/css/app.css'])
</head>
<body>
    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-head">
                <a href="{{ route('home') }}" class="auth-brand">
                    <span class="logo-mark">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 2l8 4v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6z" />
                            <path d="M9 12l2 2 4-4" />
                        </svg>
                    </span>
                    <span>{{ config('site.department_name') }}</span>
                </a>
                <h1>@yield('heading')</h1>
                <p class="auth-sub">@yield('subheading')</p>
            </div>

            @yield('content')
        </div>
    </div>
</body>
</html>