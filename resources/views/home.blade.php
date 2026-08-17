@extends('layouts.app')

@section('title', 'أرشيف الإنجازات')

@section('content')
    {{-- Hero section --}}
    <section class="hero">
        <div class="hero-grid-overlay"></div>
        <div class="container home-hero">
            <div class="hero-text">
                <h1>{{ $stats['department_name'] }}</h1>
                <p>حيث توثّق إنجازات فرق التقنية في مكان واحد ..<br>قصة التحول الرقمي للمصلحة على أيدي مُهندسيها.</p>
                <div class="hero-cta">
                    <a href="#departments" class="btn btn-primary btn-lg">استكشاف الأقسام</a>
                </div>
            </div>
        </div>
    </section>

    {{-- Statistics section --}}
    <section class="stats container">
        <div class="stats-grid">
            <div class="stat-card" style="--stat-accent: var(--gold-500);">
                <div class="stat-value" data-count-up="{{ $stats['total_count'] }}">0</div>
                <div class="stat-label">إجمالي الإنجازات — جميع الأقسام</div>
            </div>
            <div class="stat-card" style="--stat-accent: var(--accent);">
                <div class="stat-value" data-count-up="{{ $stats['year_count'] }}">0</div>
                <div class="stat-label">إنجازات هذا العام</div>
            </div>
            @foreach ($departments as $department)
                <div class="stat-card" style="--stat-accent: {{ $department->color }}">
                    <div class="stat-value" data-count-up="{{ $department->achievements_count }}">0</div>
                    <div class="stat-label">{{ $department->name }}</div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Departments cards section --}}
    <section class="section" id="departments">
        <div class="container">
            <h2 class="section-title">أقسام إدارة تقنية المعلومات</h2>
            <p class="section-sub">
                أربعة أقسام متكاملة تعمل معاً لدفع عجلة التحول الرقمي في مصلحة الضرائب الليبية.
            </p>

            <div class="dept-grid" data-stagger>
                @foreach ($departments as $i => $department)
                    <a href="{{ route('departments.show', $department->slug) }}" class="dept-card stagger-item" style="animation-delay: {{ $i * 0.08 }}s">
                        <div class="dept-card-body">
                            <span class="dept-card-icon" style="background: {{ $department->color }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    @if ($department->icon === 'network')
                                        <circle cx="12" cy="12" r="2.4"/><circle cx="4.5" cy="5" r="2.1"/><circle cx="19.5" cy="5" r="2.1"/><circle cx="4.5" cy="19" r="2.1"/><circle cx="19.5" cy="19" r="2.1"/><path d="M6.3 6.2l3.4 4.1M17.7 6.2l-3.4 4.1M6.3 17.8l3.4-4.1M17.7 17.8l-3.4-4.1"/>
                                    @elseif ($department->icon === 'document')
                                        <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5M9 13h6M9 17h6M9 9h2"/>
                                    @elseif ($department->icon === 'shield')
                                        <path d="M12 2l8 4v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6z"/><path d="M9 12l2 2 4-4"/>
                                    @else
                                        <path d="M8 6l-5 6 5 6M16 6l5 6-5 6M13.5 4l-3 16"/>
                                    @endif
                                </svg>
                            </span>
                            <h3>{{ $department->name }}</h3>
                            <p>{{ $department->description }}</p>
                            <span class="dept-card-count">
                                {{ $department->achievements_count }} إنجاز
                            </span>
                            <span class="dept-card-cta">عرض الإنجازات</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<style>
    /* Dark, minimal, side-by-side hero */
    .hero {
        background:
            radial-gradient(1000px 520px at 85% -10%, rgba(184, 146, 90, 0.22), transparent 60%),
            radial-gradient(900px 480px at 8% 115%, rgba(204, 55, 59, 0.18), transparent 60%),
            linear-gradient(135deg, #101828 0%, #1b2940 55%, #0f1722 100%);
        color: #fff;
        padding-block: 88px 84px;
    }

    .hero::before,
    .hero::after {
        display: none;
    }

    .hero-grid-overlay {
        opacity: 0.55;
        filter: invert(1);
    }

    .home-hero {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 26px;
        max-width: 720px;
        margin-inline: auto;
    }

    .hero-text {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .hero h1 {
        color: #fff;
        margin-bottom: 16px;
    }

    .hero p {
        color: rgba(255, 255, 255, 0.75);
        margin: 0 0 30px;
    }

    .btn-lg {
        padding: 14px 30px;
        font-size: 1rem;
    }

    /* Clean, minimal stats cards */
    .stats {
        margin-top: -30px;
    }

    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    }

    .stat-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        gap: 4px;
        padding: 30px 16px 26px;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        inset-inline: 0;
        top: 0;
        height: 4px;
        background: var(--stat-accent, var(--accent));
        opacity: 0.9;
    }

    .stat-card .stat-value {
        color: var(--navy-800);
    }

    .stat-card .stat-label {
        color: var(--text-soft);
        margin-top: 4px;
    }

    @media (max-width: 860px) {
        .home-hero {
            gap: 24px;
        }

        .hero {
            padding-block: 64px 60px;
        }
    }
</style>
@endpush