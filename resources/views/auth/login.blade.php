@extends('layouts.guest')

@section('heading', 'تسجيل دخول الموظفين')
@section('subheading', 'أدخل اسم المستخدم وكلمة المرور لإدارة الأرشيف.')

@section('content')
    @if (session('status'))
        <div class="auth-status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <ul class="field-errors">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf

        <div class="form-group">
            <label for="username">اسم المستخدم</label>
            <input id="username" class="form-input" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username" placeholder="مثال: admin" dir="ltr">
            @error('username')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">كلمة المرور</label>
            <input id="password" class="form-input" type="password" name="password" required autocomplete="current-password" placeholder="أدخل كلمة المرور">
            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <label class="form-check">
            <input type="checkbox" name="remember" value="1">
            <span>تذكرني</span>
        </label>

        <div class="auth-actions">
            <button type="submit" class="btn btn-primary btn-block">تسجيل الدخول</button>
        </div>

        <p class="auth-hint">
            <a href="{{ route('home') }}">→ العودة إلى الأرشيف</a>
        </p>
    </form>
@endsection