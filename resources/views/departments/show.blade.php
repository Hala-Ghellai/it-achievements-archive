@extends('layouts.app')

@section('title', $department->name)

@section('content')
    <section class="dept-hero">
        <div class="hero-grid-overlay"></div>
        <div class="container">
            <nav class="dept-breadcrumb" aria-label="مسار التنقل">
                <a href="{{ route('home') }}">الرئيسية</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('home') }}#departments">الأقسام</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">{{ $department->name }}</span>
            </nav>
            <div class="dept-hero-head">
                <span class="dept-hero-icon" style="--dept-color: {{ $department->color }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        @include('partials.department-icon', ['icon' => $department->icon])
                    </svg>
                </span>
                <h1>{{ $department->name }}</h1>
            </div>
            <p>{{ $department->description }}</p>
            <div class="dept-hero-stats">
                <div class="dept-hero-stat">
                    <div class="num">{{ $department->achievementsCountInYear($year) }}</div>
                    <div class="lbl">أعمال عام {{ $year }}</div>
                </div>
                <div class="dept-hero-stat">
                    <div class="num">{{ $department->achievementsCount() }}</div>
                    <div class="lbl">إجمالي الأعمال</div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="achieve-toolbar">
                <h2 class="section-title" style="margin-bottom: 0; font-size: 1.4rem;">أعمال القسم</h2>
                @auth
                    @if (\Illuminate\Support\Facades\Route::has('achievements.create') && \Illuminate\Support\Facades\Gate::allows('create-achievement-in', $department))
                        <a href="{{ route('achievements.create', ['department' => $department->slug]) }}" class="btn btn-primary">+ إضافة عمل</a>
                    @endif
                @endauth
            </div>

            @if ($achievements->isEmpty())
                <div class="empty-state">
                    <span class="empty-state-icon" style="--dept-color: {{ $department->color }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            @include('partials.department-icon', ['icon' => $department->icon])
                        </svg>
                    </span>
                    <h3>لا توجد أعمال مسجلة بعد</h3>
                    <p>سيتم عرض أعمال {{ $department->name }} هنا فور إضافتها.</p>
                </div>
            @else
                <div class="achieve-grid">
                    @foreach ($achievements as $achievement)
                        @php
                            $canManage = auth()->check() && Gate::allows('manage-achievement', $achievement);
                        @endphp
                        <div class="achieve-card" style="animation-delay: {{ $loop->index * 0.06 }}s">
                            <a href="{{ route('achievements.show', $achievement->id) }}" class="achieve-card-main">
                                <div class="achieve-thumb">
                                    @if ($achievement->thumbnail)
                                        <img loading="lazy" src="{{ asset('storage/' . $achievement->thumbnail) }}" alt="{{ $achievement->title }}">
                                        <span class="img-count">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2.5"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.1-3.1a2 2 0 0 0-2.8 0L6 21"/></svg>
                                            {{ $achievement->images_count }}
                                        </span>
                                    @else
                                        <div class="achieve-thumb-placeholder" style="--dept-color: {{ $department->color }}">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                                @include('partials.department-icon', ['icon' => $department->icon])
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="achieve-body">
                                    <h3>{{ $achievement->title }}</h3>
                                    <p class="excerpt">{{ $achievement->description }}</p>
                                    <div class="achieve-meta">
                                        <span class="date">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                            {{ $achievement->achievement_at->translatedFormat('j F Y') }}
                                        </span>
                                        <span class="achieve-more">التفاصيل</span>
                                    </div>
                                </div>
                            </a>

                            @if ($canManage)
                                <div class="achieve-actions">
                                    <a href="{{ route('achievements.edit', $achievement->id) }}" class="achieve-act-btn" title="تعديل العمل">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                        تعديل
                                    </a>
                                    <form method="POST" action="{{ route('achievements.destroy', $achievement->id) }}" class="achieve-del-form confirm-delete-form" data-confirm-title="{{ $achievement->title }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="achieve-act-btn danger" title="حذف العمل">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M10 11v6M14 11v6"/></svg>
                                            حذف
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @include('partials.confirm-delete-modal')
@endsection
