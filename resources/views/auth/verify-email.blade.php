@extends('layouts.guest')

@section('heading', 'تحقق من بريدك الإلكتروني')
@section('subheading', 'شكراً لتسجيلك.')

@section('content')
    <p class="auth-hint">
        قبل المتابعة، يرجى التحقق من بريدك الإلكتروني بالضغط على الرابط الذي أرسلناه إليك.
        إذا لم تستلم البريد، يسعدنا إرسال رابط آخر.
    </p>

    @if (session('status') === 'verification-link-sent')
        <div class="auth-status">
            تم إرسال رابط تحقق جديد إلى البريد الإلكتروني الذي أدخلته أثناء التسجيل.
        </div>
    @endif

    <div class="auth-actions">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary btn-block">إعادة إرسال رابط التحقق</button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="auth-inline">
            @csrf
            <button type="submit" class="btn btn-outline btn-block">تسجيل الخروج</button>
        </form>
    </div>
@endsection