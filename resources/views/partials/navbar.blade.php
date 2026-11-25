@php
    $navDepartments = \App\Models\Department::orderBy('sort_order')->get();
@endphp

<header class="navbar" id="navbar">
    <div class="container navbar-inner">
        <a href="{{ route('home') }}" class="navbar-brand">
            <span class="logo-mark">
                <img src="{{ asset('images/mashlaha-logo.jpg') }}" alt="شعار {{ config('site.organization_name') }}">
            </span>
            <span class="brand-text">
                <span class="brand-title">{{ config('site.department_name') }}</span>
                <span class="brand-sub">{{ config('site.organization_name') }}</span>
            </span>
        </a>

        <nav class="navbar-links" aria-label="التنقل الرئيسي">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">الرئيسية</a>

            <div class="nav-dropdown" data-nav-dropdown-root>
                <button type="button" class="nav-dropdown-btn {{ request()->routeIs('departments.show') ? 'active' : '' }}" data-nav-dropdown aria-expanded="false">
                    الأقسام
                    <svg class="caret" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div class="nav-dropdown-panel">
                    @foreach ($navDepartments as $department)
                        <a href="{{ route('departments.show', $department->slug) }}" class="{{ request()->routeIs('departments.show') && request()->route('department') === $department->slug ? 'active' : '' }}">
                            {{ $department->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            @auth
                <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">التقارير</a>
            @endauth
        </nav>

        <div class="navbar-actions">
            @auth
                <div class="nav-user nav-dropdown" data-nav-dropdown-root>
                    <button type="button" class="nav-user-btn" data-nav-dropdown aria-expanded="false">
                        <span class="avatar">{{ strtoupper(mb_substr(auth()->user()->full_name, 0, 1)) }}</span>
                        <span class="nav-user-name">{{ auth()->user()->full_name }}</span>
                        <svg class="caret" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="nav-dropdown-panel nav-user-menu">
                        <div class="nav-user-head">
                            <span class="avatar head-avatar">{{ strtoupper(mb_substr(auth()->user()->full_name, 0, 1)) }}</span>
                            <div class="nav-user-head-text">
                                <span class="nav-user-head-name">{{ auth()->user()->full_name }}</span>
                                <span class="nav-user-head-role">{{ auth()->user()->isAdmin() ? 'مدير النظام' : (auth()->user()->department?->name ?? 'عضو') }}</span>
                            </div>
                        </div>
                        @if (\Illuminate\Support\Facades\Route::has('profile.edit'))
                            <a href="{{ route('profile.edit') }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                حسابي
                            </a>
                        @endif
                        @if (\Illuminate\Support\Facades\Route::has('logout'))
                            <span class="nav-sep" aria-hidden="true"></span>
                            <a href="#" class="nav-dropdown-logout" onclick="event.preventDefault(); this.closest('.nav-user-menu').querySelector('.nav-dropdown-logout-form').submit();">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
                                تسجيل الخروج
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="nav-dropdown-logout-form" hidden>
                                @csrf
                            </form>
                        @endif
                    </div>
                </div>
            @else
                @if (\Illuminate\Support\Facades\Route::has('login'))
                    <a href="{{ route('login') }}" class="btn-login" title="تسجيل الدخول">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="M10 17l5-5-5-5M15 12H3"/></svg>
                        <span class="btn-login-label">تسجيل الدخول</span>
                    </a>
                @endif
            @endauth
            <button class="nav-toggle" id="nav-toggle" aria-label="فتح القائمة" aria-expanded="false">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h18M3 12h18M3 18h18" />
                </svg>
            </button>
        </div>
    </div>

    <div class="mobile-menu" id="mobile-menu">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">الرئيسية</a>
        @foreach ($navDepartments as $department)
            <a href="{{ route('departments.show', $department->slug) }}" class="{{ request()->routeIs('departments.show') && request()->route('department') === $department->slug ? 'active' : '' }}">{{ $department->name }}</a>
        @endforeach
        @auth
            <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">التقارير</a>
        @endauth
    </div>
</header>

@push('scripts')
<style>
    .logo-mark img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 8px;
    }

    /* Refined brand block */
    .navbar-brand {
        gap: 12px;
    }

    .logo-mark {
        width: 44px;
        height: 44px;
        padding: 5px;
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid var(--border);
        box-shadow: 0 2px 10px rgba(20, 20, 20, 0.07);
        margin-block: -3px;
    }

    .brand-text {
        justify-content: center;
        gap: 2px;
    }

    .brand-title {
        font-size: 0.98rem;
        letter-spacing: 0.02em;
    }

    .brand-sub {
        font-size: 0.74rem;
        font-weight: 600;
        color: var(--muted);
    }

    .nav-dropdown {
        position: relative;
    }

    .nav-dropdown-btn,
    .nav-user-btn {
        position: relative;
        display: flex;
        align-items: center;
        gap: 7px;
        background: none;
        border: none;
        padding: 8px 14px;
        border-radius: var(--radius-full);
        font-family: inherit;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--navy-800);
        cursor: pointer;
        transition: background var(--t-fast), color var(--t-fast);
    }

    .nav-dropdown-btn:hover,
    .nav-dropdown-btn.active {
        color: var(--accent);
        background: rgba(204, 55, 59, 0.08);
    }

    .nav-user-btn {
        padding: 5px 10px 5px 6px;
    }

    .nav-dropdown-btn .caret,
    .nav-user-btn .caret {
        width: 14px;
        height: 14px;
        color: var(--muted);
        transition: transform var(--t-fast);
    }

    .nav-dropdown.open .caret {
        transform: rotate(180deg);
    }

    .nav-dropdown-panel {
        position: absolute;
        top: calc(100% + 8px);
        inset-inline-start: 0;
        min-width: 210px;
        padding: 8px;
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-lg);
        z-index: 110;
        opacity: 0;
        visibility: hidden;
        transform: translateY(8px);
        transition: opacity var(--t-fast), transform var(--t-fast), visibility var(--t-fast);
    }

    .nav-dropdown.open .nav-dropdown-panel {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .nav-dropdown-panel a {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 10px 12px;
        border-radius: var(--radius-sm);
        color: var(--navy-800);
        font-size: 0.88rem;
        font-weight: 600;
        white-space: nowrap;
        transition: background var(--t-fast), color var(--t-fast);
    }

    .nav-dropdown-panel a:hover,
    .nav-dropdown-panel a.active {
        color: var(--accent);
        background: rgba(204, 55, 59, 0.08);
    }

    .nav-dropdown-panel .nav-dropdown-logout {
        color: var(--danger);
    }

    .nav-dropdown-panel .nav-dropdown-logout:hover {
        color: var(--danger);
        background: #fbecec;
    }

    .nav-user-menu {
        inset-inline-start: auto;
        inset-inline-end: 0;
        min-width: 200px;
    }

    /* Compact, modern navbar */
    .navbar-inner {
        gap: 14px;
        padding-block: 10px;
    }

    .navbar-links {
        margin-inline-start: 40px;
        gap: 20px;
    }

    .navbar-actions {
        margin-inline-start: auto;
        gap: 8px;
    }

    .navbar-links a {
        font-size: 0.88rem;
        padding: 7px 12px;
    }

    .nav-dropdown-btn::after {
        content: '';
        position: absolute;
        inset-inline: 50%;
        bottom: 2px;
        height: 2px;
        transform: translateX(50%);
        border-radius: var(--radius-full);
        background: var(--accent);
        opacity: 0;
        transition: opacity var(--t-fast);
    }

    .nav-dropdown-btn.active::after {
        opacity: 1;
    }

    .navbar::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent 0%, var(--gold-500) 35%, var(--accent) 75%, transparent 100%);
        opacity: 0.75;
    }

    .btn-login {
        padding: 8px 18px;
    }

    /* Uniform heights + micro-lift so every control sits on the same line */
    .navbar-links a,
    .nav-dropdown-btn,
    .nav-user-btn {
        display: inline-flex;
        align-items: center;
        min-height: 42px;
    }

    .nav-dropdown-btn,
    .nav-user-btn {
        transition: background var(--t-fast), color var(--t-fast), transform var(--t-fast);
    }

    .nav-dropdown-btn:hover,
    .nav-user-btn:hover {
        transform: translateY(-1px);
    }

    .nav-user-btn {
        min-height: 44px;
        padding: 6px 12px 6px 7px;
    }

    /* Avatar ring in the navbar */
    .nav-user .avatar {
        border: 2px solid #fff;
        box-shadow: 0 2px 8px rgba(20, 20, 20, 0.12);
    }

    /* Profile header inside the user menu */
    .nav-user-head {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px 12px;
        margin-bottom: 6px;
        background: var(--bg-alt);
        border-radius: var(--radius-sm);
        border-bottom: 1px solid var(--border);
    }

    .nav-user-head .avatar {
        width: 38px;
        height: 38px;
        border: none;
        box-shadow: none;
    }

    .nav-user-head-text {
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .nav-user-head-name {
        font-weight: 800;
        font-size: 0.9rem;
        color: var(--navy-800);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .nav-user-head-role {
        font-size: 0.74rem;
        font-weight: 600;
        color: var(--gold-600);
    }

    .nav-sep {
        height: 1px;
        background: var(--border);
        display: block;
        margin: 6px 4px;
    }
</style>
<script>
    (function () {
        'use strict';

        var roots = Array.prototype.slice.call(document.querySelectorAll('[data-nav-dropdown-root]'));

        function closeAll(except) {
            roots.forEach(function (root) {
                if (root !== except) {
                    root.classList.remove('open');
                    var btn = root.querySelector('[data-nav-dropdown]');
                    if (btn) btn.setAttribute('aria-expanded', 'false');
                }
            });
        }

        roots.forEach(function (root) {
            var btn = root.querySelector('[data-nav-dropdown]');
            if (!btn) return;

            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                var willOpen = !root.classList.contains('open');
                closeAll(root);
                root.classList.toggle('open', willOpen);
                btn.setAttribute('aria-expanded', String(willOpen));
            });
        });

        document.addEventListener('click', function () { closeAll(null); });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeAll(null);
        });
    })();
</script>
@endpush