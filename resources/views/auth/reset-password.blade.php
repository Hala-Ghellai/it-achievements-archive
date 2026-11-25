@extends('layouts.guest')

@section('heading', 'تعيين كلمة مرور جديدة')
@section('subheading', 'اختر كلمة مرور قوية لحسابك الموظف.')

@section('content')
    @if ($errors->any())
        <ul class="field-errors">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('password.store') }}" class="auth-form">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="form-group">
            <label for="email">البريد الإلكتروني</label>
            <input id="email" class="form-input" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" dir="ltr">
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">كلمة المرور</label>
            <input id="password" class="form-input" type="password" name="password" required autocomplete="new-password">
            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">تأكيد كلمة المرور</label>
            <input id="password_confirmation" class="form-input" type="password" name="password_confirmation" required autocomplete="new-password">
            @error('password_confirmation')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-actions">
            <button type="submit" class="btn btn-primary btn-block">إعادة تعيين كلمة المرور</button>
        </div>

        <p class="auth-back">
            <a href="{{ route('login') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                العودة إلى تسجيل الدخول
            </a>
        </p>
    </form>
@endsection