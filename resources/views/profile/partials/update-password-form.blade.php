<div>
    <h4>تغيير كلمة المرور</h4>
    <p class="profile-hint">احرص على استخدام كلمة مرور طويلة وعشوائية للحفاظ على أمان حسابك.</p>

    <form method="post" action="{{ route('password.update') }}" class="profile-form">
        @csrf
        @method('put')

        <div class="form-group">
            <label for="update_password_current_password">كلمة المرور الحالية</label>
            <input id="update_password_current_password" class="form-input" type="password" name="current_password" required autocomplete="current-password">
            @error('current_password', 'updatePassword')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="update_password_password">كلمة المرور الجديدة</label>
            <input id="update_password_password" class="form-input" type="password" name="password" required autocomplete="new-password">
            @error('password', 'updatePassword')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="update_password_password_confirmation">تأكيد كلمة المرور</label>
            <input id="update_password_password_confirmation" class="form-input" type="password" name="password_confirmation" required autocomplete="new-password">
            @error('password_confirmation', 'updatePassword')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-actions">
            <button type="submit" class="btn btn-primary">حفظ كلمة المرور</button>
            @if (session('status') === 'password-updated')
                <span class="profile-saved">تم الحفظ.</span>
            @endif
        </div>
    </form>
</div>