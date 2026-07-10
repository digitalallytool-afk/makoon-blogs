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
        <div class="card shadow-lg border-0 radius-10" style="max-width: 450px; width: 100%;">
            <div class="card-body p-sm-5">
                <div class="p-2">
                    <div class="text-center mb-4">
                        <img src="{{ asset('backend/assets/images/login-images/forgot-password-cover.svg') }}" width="120" alt="Forgot Password Cover" />
                        <h4 class="mt-4 font-weight-bold text-dark">Forgot Password?</h4>
                        <p class="text-muted font-13">Enter your registered email address to reset your password.</p>
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success border-0 bg-light-success text-success alert-dismissible fade show font-13" role="alert">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}" class="row g-3">
                        @csrf

                        <!-- Email Address -->
                        <div class="col-12">
                            <label for="email" class="form-label font-weight-bold text-secondary font-12 text-uppercase mb-1">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent"><i class="bx bx-envelope"></i></span>
                                <input type="email" name="email" id="email" class="form-control border-start-0 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="email@example.com" required autofocus autocomplete="username" />
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block font-12 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary w-100 radius-30 font-weight-bold"><i class="bx bx-mail-send me-1"></i>Send Link</button>
                        </div>

                        <div class="col-12 text-center mt-3">
                            <a href="{{ route('login') }}" class="btn btn-light w-100 radius-30 font-weight-bold"><i class="bx bx-arrow-back me-1"></i>Back to Login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
