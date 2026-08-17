<div>
    <h4>حذف الحساب</h4>
    <p class="profile-hint">
        عند حذف حسابك سيتم حذف جميع بياناته نهائياً ولا يمكن التراجع عن ذلك. يُرجى إدخال كلمة المرور للتأكيد.
    </p>

    <form method="post" action="{{ route('profile.destroy') }}" class="profile-form confirm-delete-form" data-confirm-title="حذف الحساب" data-confirm-text="سيتم حذف حسابك نهائياً مع جميع بياناته. لا يمكن التراجع عن هذه العملية.">
        @csrf
        @method('delete')

        <div class="form-group">
            <label for="delete_password">كلمة المرور للتأكيد</label>
            <input id="delete_password" class="form-input" type="password" name="password" required autocomplete="current-password">
            @error('password', 'userDeletion')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-actions">
            <button type="submit" class="btn btn-danger">حذف الحساب نهائياً</button>
        </div>
    </form>
</div>