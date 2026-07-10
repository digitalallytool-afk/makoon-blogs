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

    <div class="authentication-reset-password d-flex align-items-center justify-content-center">
        <div class="card shadow-lg border-0 radius-10" style="max-width: 450px; width: 100%;">
            <div class="card-body p-sm-5">
                <div class="p-2">
                    <div class="text-center mb-4">
                        <img src="{{ asset('backend/assets/images/login-images/reset-password-cover.svg') }}" width="120" alt="Reset Password Cover" />
                        <h4 class="mt-4 font-weight-bold text-dark">Generate New Password</h4>
                        <p class="text-muted font-13">Create a strong password that you can remember.</p>
                    </div>

                    <form method="POST" action="{{ route('password.store') }}" class="row g-3">
                        @csrf

                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <!-- Email Address -->
                        <div class="col-12">
                            <label for="email" class="form-label font-weight-bold text-secondary font-12 text-uppercase mb-1">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent"><i class="bx bx-envelope"></i></span>
                                <input type="email" name="email" id="email" class="form-control border-start-0 @error('email') is-invalid @enderror" value="{{ old('email', $request->email) }}" placeholder="email@example.com" required autofocus autocomplete="username" />
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block font-12 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="col-12">
                            <label for="password" class="form-label font-weight-bold text-secondary font-12 text-uppercase mb-1">New Password</label>
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
                            <label for="password_confirmation" class="form-label font-weight-bold text-secondary font-12 text-uppercase mb-1">Confirm New Password</label>
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
                            <button type="submit" class="btn btn-primary w-100 radius-30 font-weight-bold"><i class="bx bx-check-double me-1"></i>Change Password</button>
                        </div>
                    </form>
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
