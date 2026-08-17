@extends('layouts.app')

@section('title', $department->name)

@section('content')
    <section class="dept-hero">
        <div class="hero-grid-overlay"></div>
        <div class="container">
            <span class="dept-hero-eyebrow">
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
                {{ config('site.department_name') }} · {{ config('site.organization_name') }}
            </span>
            <h1>{{ $department->name }}</h1>
            <p>{{ $department->description }}</p>
            <div class="dept-hero-stats">
                <div class="dept-hero-stat">
                    <div class="num">{{ $department->achievementsCountInYear($year) }}</div>
                    <div class="lbl">إنجازات هذا العام</div>
                </div>
                <div class="dept-hero-stat">
                    <div class="num">{{ $department->achievementsCount() }}</div>
                    <div class="lbl">إجمالي الإنجازات</div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="achieve-toolbar">
                <h2 class="section-title" style="margin-bottom: 0; font-size: 1.4rem;">أرشيف الإنجازات</h2>
                @auth
                    @if (\Illuminate\Support\Facades\Route::has('achievements.create') && \Illuminate\Support\Facades\Gate::allows('create-achievement-in', $department))
                        <a href="{{ route('achievements.create', ['department' => $department->slug]) }}" class="btn btn-primary">+ إضافة إنجاز</a>
                    @endif
                @endauth
            </div>

            @if ($achievements->isEmpty())
                <div class="empty-state">
                    <div class="big-icon">🗂</div>
                    <p>لا توجد إنجازات مسجلة لهذا القسم بعد.</p>
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
                                        <span class="img-count">🖼 {{ $achievement->images_count }}</span>
                                    @else
                                        <div class="achieve-thumb-placeholder">
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
                                    <a href="{{ route('achievements.edit', $achievement->id) }}" class="achieve-act-btn" title="تعديل الإنجاز">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                        تعديل
                                    </a>
                                    <form method="POST" action="{{ route('achievements.destroy', $achievement->id) }}" class="achieve-del-form confirm-delete-form" data-confirm-title="{{ $achievement->title }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="achieve-act-btn danger" title="حذف الإنجاز">
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
