<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=2">

    <title>@yield('heading', 'تسجيل الدخول') · {{ config('site.department_name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Noto+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">

    @stack('head')
    @vite(['resources/css/app.css'])
</head>
<body class="auth-body">
    @php
        $authDepartments = \App\Models\Department::orderBy('sort_order')->get();
    @endphp

    <div class="auth-shell">
        {{-- Brand panel (hidden on small screens) --}}
        <aside class="auth-aside">
            <div class="hero-grid-overlay"></div>

            <div class="auth-aside-inner">
                <a href="{{ route('home') }}" class="auth-aside-logo">
                    <img src="{{ asset('images/mashlaha-logo.jpg') }}" alt="شعار {{ config('site.organization_name') }}">
                </a>

                <div class="auth-aside-org">{{ config('site.organization_name') }}</div>
                <h2 class="auth-aside-title">{{ config('site.department_name') }}</h2>
                <p class="auth-aside-text">
                    أرشيف موحّد يوثّق إنجازات أقسام الإدارة ومسيرة التحول الرقمي في المصلحة.
                </p>

                @if ($authDepartments->isNotEmpty())
                    <ul class="auth-depts">
                        @foreach ($authDepartments as $department)
                            <li style="--dept-color: {{ $department->color }}">
                                <span class="auth-dept-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        @include('partials.department-icon', ['icon' => $department->icon])
                                    </svg>
                                </span>
                                <span>{{ $department->name }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="auth-aside-foot">© {{ now()->year }} {{ config('site.organization_name') }}</div>
        </aside>

        {{-- Form panel --}}
        <main class="auth-main">
            <div class="auth-card">
                <div class="auth-head">
                    <a href="{{ route('home') }}" class="auth-logo" aria-label="العودة إلى الصفحة الرئيسية">
                        <img src="{{ asset('images/mashlaha-logo.jpg') }}" alt="شعار {{ config('site.organization_name') }}">
                    </a>
                    <h1>@yield('heading')</h1>
                    <p class="auth-sub">@yield('subheading')</p>
                </div>

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
