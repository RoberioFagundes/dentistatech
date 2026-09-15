<section>
    <header class="mb-4">
        <h5 class="fw-bold text-dark mb-1">
            {{ __('Update Password') }}
        </h5>
        <p class="text-muted small mb-0">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label fw-semibold">
                {{ __('Current Password') }}
            </label>
            <input type="password" id="update_password_current_password" name="current_password" 
                   class="form-control" autocomplete="current-password">
            @error('current_password')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label fw-semibold">
                {{ __('New Password') }}
            </label>
            <input type="password" id="update_password_password" name="password" 
                   class="form-control" autocomplete="new-password">
            @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label fw-semibold">
                {{ __('Confirm Password') }}
            </label>
            <input type="password" id="update_password_password_confirmation" name="password_confirmation" 
                   class="form-control" autocomplete="new-password">
            @error('password_confirmation')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex align-items-center gap-3 mt-4">
            <button type="submit" class="btn btn-primary">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'password-updated')
                <span class="text-success small">
                    {{ __('Saved.') }}
                </span>
                <script>
                    setTimeout(() => document.currentScript.parentElement.remove(), 2000);
                </script>
            @endif
        </div>
    </form>
</section>