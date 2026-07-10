<x-guest-layout>
    <div class="section-authentication-signin d-flex align-items-center justify-content-center my-5 my-lg-0">
        <div class="container">
            <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-2">
                <div class="col-xl-9 mx-auto">
                    <div class="card my-5 shadow-lg border-0 radius-10">
                        <div class="row g-0">
                            <!-- Left: Cover image -->
                            <div class="col-lg-6 d-none d-lg-flex bg-light align-items-center justify-content-center border-end">
                                <img src="{{ asset('backend/assets/images/login-images/register-cover.svg') }}" class="img-fluid" width="450" alt="Register Cover">
                            </div>
                            <!-- Right: Register Form -->
                            <div class="col-lg-6">
                                <div class="card-body p-sm-5">
                                    <div class="p-2">
                                        <div class="text-center mb-4">
                                            <img src="{{ asset('backend/assets/images/makoons-logo.png') }}" width="60" alt="Logo icon" />
                                            <h4 class="mt-2 font-weight-bold text-dark">Get Started</h4>
                                            <p class="text-muted font-13">Create your account to manage Makoon Panel</p>
                                        </div>

                                        <form method="POST" action="{{ route('register') }}" class="row g-3">
                                            @csrf

                                            <!-- Full Name -->
                                            <div class="col-12">
                                                <label for="name" class="form-label font-weight-bold text-secondary font-12 text-uppercase mb-1">Full Name</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-transparent"><i class="bx bx-user"></i></span>
                                                    <input type="text" name="name" id="name" class="form-control border-start-0 @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="John Doe" required autofocus autocomplete="name" />
                                                </div>
                                                @error('name')
                                                    <div class="invalid-feedback d-block font-12 mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Email Address -->
                                            <div class="col-12">
                                                <label for="email" class="form-label font-weight-bold text-secondary font-12 text-uppercase mb-1">Email Address</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-transparent"><i class="bx bx-envelope"></i></span>
                                                    <input type="email" name="email" id="email" class="form-control border-start-0 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="email@example.com" required autocomplete="username" />
                                                </div>
                                                @error('email')
                                                    <div class="invalid-feedback d-block font-12 mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Password -->
                                            <div class="col-12">
                                                <label for="password" class="form-label font-weight-bold text-secondary font-12 text-uppercase mb-1">Password</label>
                                                <div class="input-group" id="show_hide_password">
                                                    <span class="input-group-text bg-transparent"><i class="bx bx-lock"></i></span>
                                                    <input type="password" name="password" id="password" class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror" placeholder="••••••••" required autocomplete="new-password" />
                                                    <a href="javascript:;" class="input-group-text bg-transparent"><i class="bx bx-hide"></i></a>
                                                </div>
                                                @error('password')
                                                    <div class="invalid-feedback d-block font-12 mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Confirm Password -->
                                            <div class="col-12">
                                                <label for="password_confirmation" class="form-label font-weight-bold text-secondary font-12 text-uppercase mb-1">Confirm Password</label>
                                                <div class="input-group" id="show_hide_confirm_password">
                                                    <span class="input-group-text bg-transparent"><i class="bx bx-lock-alt"></i></span>
                                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-start-0 border-end-0 @error('password_confirmation') is-invalid @enderror" placeholder="••••••••" required autocomplete="new-password" />
                                                    <a href="javascript:;" class="input-group-text bg-transparent"><i class="bx bx-hide"></i></a>
                                                </div>
                                                @error('password_confirmation')
                                                    <div class="invalid-feedback d-block font-12 mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Submit Button -->
                                            <div class="col-12 mt-4">
                                                <button type="submit" class="btn btn-primary w-100 radius-30 font-weight-bold"><i class="bx bx-user-plus me-1"></i>Sign Up</button>
                                            </div>

                                            <div class="col-12 text-center mt-3">
                                                <p class="mb-0 text-secondary font-13">Already have an account? <a href="{{ route('login') }}" class="text-primary font-weight-bold text-decoration-none">Sign In</a></p>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function () {
            $("#show_hide_password a").on('click', function (event) {
                event.preventDefault();
                var input = $('#show_hide_password input');
                var icon = $('#show_hide_password i');
                if (input.attr("type") === "text") {
                    input.attr('type', 'password');
                    icon.addClass("bx-hide");
                    icon.removeClass("bx-show");
                } else if (input.attr("type") === "password") {
                    input.attr('type', 'text');
                    icon.removeClass("bx-hide");
                    icon.addClass("bx-show");
                }
            });

            $("#show_hide_confirm_password a").on('click', function (event) {
                event.preventDefault();
                var input = $('#show_hide_confirm_password input');
                var icon = $('#show_hide_confirm_password i');
                if (input.attr("type") === "text") {
                    input.attr('type', 'password');
                    icon.addClass("bx-hide");
                    icon.removeClass("bx-show");
                } else if (input.attr("type") === "password") {
                    input.attr('type', 'text');
                    icon.removeClass("bx-hide");
                    icon.addClass("bx-show");
                }
            });
        });
    </script>
    @endpush
</x-guest-layout>
