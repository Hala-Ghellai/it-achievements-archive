<div>
    <h4>معلومات الحساب</h4>
    <p class="profile-hint">
        غيّر اسم المستخدم الذي تسجّل به الدخول إلى الموقع. تذكّر أن تسجّل الدخول باسـم المستخدم الجديد في المرات القادمة.
    </p>

    <form method="post" action="{{ route('profile.update') }}" class="profile-form">
        @csrf
        @method('patch')

        <div class="form-group">
            <label for="username">اسم المستخدم</label>
            <input id="username" class="form-input" type="text" name="username" value="{{ old('username', $user->username) }}" required autofocus maxlength="255" autocomplete="username">
            @error('username')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-actions">
            <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
            @if (session('status') === 'profile-updated')
                <span class="profile-saved">تم الحفظ.</span>
            @endif
        </div>
    </form>
</div>