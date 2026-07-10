<x-guest-layout>
    <div class="container py-5 d-flex justify-content-center">
        <div class="card shadow-sm p-4 w-100" style="max-width: 500px; border-radius: 12px;">
            <div class="mb-4 text-muted small">
                {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="alert alert-success small mb-4">
                    {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mt-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary px-3 py-2 text-sm">
                        {{ __('Resend Verification Email') }}
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link text-decoration-none text-muted small px-0 text-sm">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>