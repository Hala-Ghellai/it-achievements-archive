@extends('layouts.app')

@section('title', $achievement->title)

@section('content')
    <section class="section">
        <div class="container detail-wrap">
            <div class="detail-header">
                <a href="{{ route('departments.show', $achievement->department->slug) }}" class="back-link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    العودة إلى {{ $achievement->department->name }}
                </a>
                <h1 class="detail-title">{{ $achievement->title }}</h1>
                <div class="detail-meta">
                    <span class="badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                        {{ $achievement->achievement_at->translatedFormat('j F Y، H:i') }}
                    </span>
                    <a href="{{ route('departments.show', $achievement->department->slug) }}" class="badge badge-dept" style="--dept-color: {{ $achievement->department->color }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            @include('partials.department-icon', ['icon' => $achievement->department->icon])
                        </svg>
                        {{ $achievement->department->name }}
                    </a>
                </div>
            </div>

            <div class="detail-card">
                <h3>عن هذا الإنجاز</h3>
                <div class="detail-desc">{{ $achievement->description }}</div>
            </div>

            @php
                $images = $achievement->media->where('type', 'image');
                $videos = $achievement->media->where('type', 'video');
                $pdfs = $achievement->media->where('type', 'pdf');
            @endphp

            @if ($images->isNotEmpty())
                <div class="detail-card">
                    <h3>المعرض</h3>
                    <div class="gallery-grid">
                        @foreach ($images as $image)
                            <a href="{{ asset('storage/' . $image->url) }}" target="_blank" rel="noopener" class="gallery-item">
                                <img loading="lazy" src="{{ asset('storage/' . $image->url) }}" alt="{{ $achievement->title }}">
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($videos->isNotEmpty())
                <div class="detail-card">
                    <h3>فيديوهات</h3>
                    @foreach ($videos as $video)
                        <div class="video-frame" style="margin-bottom: 12px;">
                            <video controls preload="metadata" src="{{ asset('storage/' . $video->url) }}"></video>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($pdfs->isNotEmpty())
                <div class="detail-card">
                    <h3>ملفات PDF</h3>
                    <div class="pdf-list">
                        @foreach ($pdfs as $file)
                            <a href="{{ asset('storage/' . $file->url) }}" target="_blank" rel="noopener" class="pdf-item">
                                <span class="pdf-icon">PDF</span>
                                {{ $file->original_name ?: $file->url }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($achievement->employees->isNotEmpty())
                <div class="detail-card">
                    <h3>الفريق المساهم</h3>
                    <div class="employee-chips">
                        @foreach ($achievement->employees as $employee)
                            <span class="employee-chip">{{ $employee->full_name }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
