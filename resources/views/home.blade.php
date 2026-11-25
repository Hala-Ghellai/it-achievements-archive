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
            <div class="stat-card stat-summary" style="--stat-accent: var(--gold-500);">
                <span class="stat-summary-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 21h8M12 17v4"/><path d="M7 4h10v5a5 5 0 0 1-10 0z"/><path d="M17 5h2.5a1.5 1.5 0 0 1 1.5 1.5c0 2.2-1.8 4-4 4M7 5H4.5A1.5 1.5 0 0 0 3 6.5c0 2.2 1.8 4 4 4"/></svg>
                </span>
                <div>
                    <div class="stat-value" data-count-up="{{ $stats['total_count'] }}">0</div>
                    <div class="stat-label">إجمالي الأعمال في جميع الأقسام</div>
                </div>
            </div>
            <div class="stat-card stat-summary" style="--stat-accent: var(--accent);">
                <span class="stat-summary-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4.5" width="18" height="16.5" rx="2.5"/><path d="M3 9.5h18M8 2.5v4M16 2.5v4"/><path d="m9 15 2 2 4-4"/></svg>
                </span>
                <div>
                    <div class="stat-value" data-count-up="{{ $stats['year_count'] }}">0</div>
                    <div class="stat-label">أعمال عام {{ $year }}</div>
                </div>
            </div>
            @foreach ($departments as $department)
                <a href="{{ route('departments.show', $department->slug) }}" class="stat-card stat-dept" style="--stat-accent: {{ $department->color }}" title="{{ $department->name }}">
                    <span class="stat-dept-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            @include('partials.department-icon', ['icon' => $department->icon])
                        </svg>
                    </span>
                    <div class="stat-value" data-count-up="{{ $department->achievements_count }}">0</div>
                    <div class="stat-label">{{ $department->name }}</div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Departments cards section --}}
    <section class="section" id="departments">
        <div class="container">
            <h2 class="section-title">أقسام إدارة تقنية المعلومات</h2>
            <p class="section-sub">
                ستة أقسام متكاملة تعمل معاً لدفع عجلة التحول الرقمي في مصلحة الضرائب.
            </p>

            <div class="dept-grid" data-stagger>
                @foreach ($departments as $i => $department)
                    <a href="{{ route('departments.show', $department->slug) }}" class="dept-card stagger-item" style="animation-delay: {{ $i * 0.08 }}s">
                        <div class="dept-card-body">
                            <span class="dept-card-icon" style="background: {{ $department->color }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    @include('partials.department-icon', ['icon' => $department->icon])
                                </svg>
                            </span>
                            <h3>{{ $department->name }}</h3>
                            <p>{{ $department->description }}</p>
                            <span class="dept-card-count">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4.5" width="18" height="16.5" rx="2.5"/><path d="M3 9.5h18M8 2.5v4M16 2.5v4"/></svg>
                                {{ \App\Models\Department::countLabel($department->achievements_count) }} في {{ $year }}
                            </span>
                            <span class="dept-card-cta">
                                عرض الأعمال
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
                            </span>
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

    /* Statistics: two summary cards + one compact card per department */
    .stats {
        margin-top: -36px;
    }

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
    }

    .stat-card {
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        inset-inline: 0;
        top: 0;
        height: 3px;
        background: var(--stat-accent, var(--accent));
    }

    .stat-summary {
        grid-column: span 2;
        padding: 22px 24px;
        gap: 18px;
    }

    .stat-summary-icon,
    .stat-dept-icon {
        display: grid;
        place-items: center;
        flex-shrink: 0;
        color: var(--stat-accent);
        background: color-mix(in srgb, var(--stat-accent) 12%, #fff);
    }

    .stat-summary-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
    }

    .stat-summary-icon svg {
        width: 28px;
        height: 28px;
    }

    .stat-summary .stat-label {
        margin-top: 4px;
    }

    .stat-dept {
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        text-align: center;
        gap: 6px;
        padding: 20px 12px 18px;
    }

    .stat-dept-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        margin-bottom: 4px;
        transition: background var(--t-fast), color var(--t-fast);
    }

    .stat-dept-icon svg {
        width: 22px;
        height: 22px;
    }

    .stat-dept:hover .stat-dept-icon {
        background: var(--stat-accent);
        color: #fff;
    }

    .stat-dept .stat-value {
        font-size: 1.6rem;
    }

    .stat-dept .stat-label {
        font-size: 0.8rem;
        line-height: 1.5;
    }

    @media (min-width: 640px) and (max-width: 959px) {
        .stats-grid {
            grid-template-columns: repeat(6, 1fr);
        }

        .stat-summary {
            grid-column: span 3;
        }

        .stat-dept {
            grid-column: span 2;
        }
    }

    @media (min-width: 960px) {
        .stats-grid {
            grid-template-columns: repeat(6, 1fr);
        }

        .stat-summary {
            grid-column: span 3;
        }
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