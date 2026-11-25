@extends('layouts.guest')

@section('heading', 'إعادة تعيين كلمة المرور')
@section('subheading', 'أدخل بريدك الإلكتروني وسنرسل لك رابط إعادة التعيين.')

@section('content')
    @if (session('status'))
        <div class="auth-status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="auth-form">
        @csrf

        <div class="form-group">
            <label for="email">البريد الإلكتروني</label>
            <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" dir="ltr">
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-actions">
            <button type="submit" class="btn btn-primary btn-block">إرسال رابط إعادة التعيين</button>
        </div>

        <p class="auth-back">
            <a href="{{ route('login') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                العودة إلى تسجيل الدخول
            </a>
        </p>
    </form>
@endsection