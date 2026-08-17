@extends('layouts.guest')

@section('heading', 'تأكيد كلمة المرور')
@section('subheading', 'لأمان حسابك، يرجى تأكيد كلمة مرورك للمتابعة.')

@section('content')
    @if (session('status'))
        <div class="auth-status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.confirm') }}" class="auth-form">
        @csrf

        <div class="form-group">
            <label for="password">كلمة المرور</label>
            <input id="password" class="form-input" type="password" name="password" required autocomplete="current-password" autofocus>
            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-actions">
            <button type="submit" class="btn btn-primary btn-block">تأكيد</button>
        </div>

        <p class="auth-hint">
            <a href="{{ route('home') }}">→ العودة إلى الأرشيف</a>
        </p>
    </form>
@endsection