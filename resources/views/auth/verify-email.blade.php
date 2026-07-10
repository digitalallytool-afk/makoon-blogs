<x-guest-layout>
    <style>
        body {
            background-image: url("{{ asset('backend/assets/images/login-images/bg-forgot-password.jpg') }}") !important;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
    </style>

    <div class="authentication-forgot d-flex align-items-center justify-content-center">
        <div class="card shadow-lg border-0 radius-10" style="max-width: 480px; width: 100%;">
            <div class="card-body p-sm-5">
                <div class="p-2">
                    <div class="text-center mb-4">
                        <img src="{{ asset('backend/assets/images/login-images/forgot-password-cover.svg') }}" width="100" alt="Verify Email Cover" />
                        <h4 class="mt-4 font-weight-bold text-dark">Verify Email</h4>
                        <p class="text-secondary font-13 leading-relaxed mt-2">
                            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
                        </p>
                    </div>

                    @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-success border-0 bg-light-success text-success alert-dismissible fade show font-13 mb-4" role="alert">
                            A new verification link has been sent to the email address you provided during registration.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="d-flex flex-column gap-2 mt-4">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100 radius-30 font-weight-bold">
                                <i class="bx bx-mail-send me-1"></i>Resend Verification Email
                            </button>
                        </form>

                        <form method="POST" action="{{ route('logout') }}" class="w-100 text-center">
                            @csrf
                            <button type="submit" class="btn btn-link text-decoration-none text-secondary font-13 font-weight-bold mt-2">
                                <i class="bx bx-log-out me-1"></i>Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
