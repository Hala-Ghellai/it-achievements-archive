@extends('layouts.app')

@section('title', 'حسابي')

@section('content')
    <section class="section">
        <div class="container detail-wrap" style="max-width: 720px;">
            <div class="detail-header">
                <a href="{{ url()->previous() ?: route('home') }}" class="back-link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    العودة
                </a>
                <h1 class="detail-title">حسابي</h1>
                <p style="color: var(--muted); font-size: 0.9rem;">إدارة بيانات حسابك وكلمة المرور.</p>
            </div>

            @if (session('status') === 'profile-updated')
                <div class="status-msg">تم حفظ التعديلات بنجاح.</div>
            @endif

            <div class="detail-card">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="detail-card">
                @include('profile.partials.update-password-form')
            </div>

            <div class="detail-card danger-card">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </section>

    @include('partials.confirm-delete-modal')
@endsection

@push('scripts')
<style>
    .detail-card h4 {
        margin: 0 0 4px;
        font-size: 1.05rem;
    }

    .profile-sub,
    .profile-hint {
        color: var(--muted);
        font-size: 0.9rem;
        line-height: 1.7;
        margin-bottom: 20px;
    }

    .profile-form {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .profile-form .auth-actions {
        margin-top: 6px;
    }

    .status-msg {
        background: #e9f7ef;
        border: 1px solid #bfe6d0;
        color: var(--success);
        border-radius: var(--radius-sm);
        padding: 12px 16px;
        font-weight: 700;
        font-size: 0.9rem;
        margin-bottom: 18px;
    }

    .danger-card {
        border-color: #f0c4c6;
    }

    .danger-card h4 {
        color: var(--danger);
    }

    .btn-danger {
        background: var(--danger);
        border-color: var(--danger);
        color: #fff;
    }

    .btn-danger:hover {
        background: #b02e32;
        border-color: #b02e32;
    }

    .profile-saved {
        color: var(--success);
        font-size: 0.85rem;
        font-weight: 700;
    }
</style>
@endpush