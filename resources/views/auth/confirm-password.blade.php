<x-guest-layout>
    <div class="container py-5 d-flex justify-content-center">
        <div class="card shadow-sm p-4 w-100" style="max-width: 450px; border-radius: 12px;">
            <div class="mb-4 text-muted small">
                {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
            </div>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('Password') }}</label>
                    <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="text-danger small mt-1" />
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        {{ __('Confirm') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>