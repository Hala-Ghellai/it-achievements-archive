@extends('layouts.guest')

@section('heading', 'تسجيل دخول الموظفين')
@section('subheading', 'أدخل اسم المستخدم وكلمة المرور لإدارة أرشيف الإنجازات.')

@section('content')
    @if (session('status'))
        <div class="auth-status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="auth-alert" role="alert">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8v4.5M12 16h.01"/></svg>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf

        <div class="form-group">
            <label for="username">اسم المستخدم</label>
            <div class="input-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                <input id="username" class="form-input @error('username') is-invalid @enderror" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username" placeholder="اسم المستخدم">
            </div>
        </div>

        <div class="form-group">
            <label for="password">كلمة المرور</label>
            <div class="input-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="10.5" width="16" height="10.5" rx="2.5"/><path d="M8 10.5V7.5a4 4 0 0 1 8 0v3"/></svg>
                <input id="password" class="form-input @error('password') is-invalid @enderror" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                <button type="button" class="input-toggle" data-toggle-password="password" aria-label="إظهار كلمة المرور" title="إظهار كلمة المرور">
                    <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg class="eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3l18 18"/><path d="M10.6 5.1A9.8 9.8 0 0 1 12 5c6.4 0 10 7 10 7a17.6 17.6 0 0 1-3.2 4.2M6.6 6.6C3.7 8.4 2 12 2 12s3.6 7 10 7a9.6 9.6 0 0 0 5.4-1.6"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                </button>
            </div>
        </div>

        <label class="form-check">
            <input type="checkbox" name="remember" value="1">
            <span>تذكرني على هذا الجهاز</span>
        </label>

        <div class="auth-actions">
            <button type="submit" class="btn btn-primary btn-block">
                تسجيل الدخول
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="width:18px;height:18px"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
            </button>
        </div>

        <p class="auth-back">
            <a href="{{ route('home') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                العودة إلى الأرشيف
            </a>
        </p>
    </form>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById(btn.getAttribute('data-toggle-password'));
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.classList.toggle('is-visible', show);
            btn.setAttribute('aria-label', show ? 'إخفاء كلمة المرور' : 'إظهار كلمة المرور');
            btn.setAttribute('title', show ? 'إخفاء كلمة المرور' : 'إظهار كلمة المرور');
        });
    });
</script>
@endpush
