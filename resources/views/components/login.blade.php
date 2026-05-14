<!-- Email Auth -->
<div class="modal modalCentered fade modal-log" id="sign">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <span class="icon-close-popup" data-bs-dismiss="modal">
                <i class="icon-X2"></i>
            </span>
            <div class="modal-heading text-center">
                <h3 class="title-pop mb-8">Continue with Email</h3>
                <p class="desc-pop cl-text-2">Enter your email and verify the OTP we send.</p>
            </div>
            <div class="modal-main">
                @if (session('auth_modal') === 'sign' && session('auth_notice'))
                    <div class="alert alert-warning mb-3">{{ session('auth_notice') }}</div>
                @endif
                @if (session('auth_modal') === 'sign' && session('success'))
                    <div class="alert alert-success mb-3">{{ session('success') }}</div>
                @endif
                @if (! session('otp_email'))
                <form action="{{ route('auth.otp.send') }}" method="POST" class="form-log otp-auth-form">
                    @csrf
                    <div class="form-content">
                        <fieldset class="tf-field">
                            <label for="login-email" class="tf-lable fw-medium">Email address <span
                                    class="text-primary">*</span></label>
                            <input type="email" id="login-email" name="email" value="{{ old('email', session('otp_email')) }}" placeholder="Email address*" required>
                            @error('email')
                                @if (session('auth_modal') === 'sign')
                                    <small class="text-danger">{{ $message }}</small>
                                @endif
                            @enderror
                        </fieldset>
                    </div>
                    <div class="group-action">
                        <button type="submit" class="tf-btn animate-btn w-100" data-loading-text="Sending...">
                            Send OTP
                        </button>
                    </div>
                </form>
                @endif
                @if (session('otp_email'))
                    <form action="{{ route('auth.otp.verify') }}" method="POST" class="form-log mt-4 otp-auth-form">
                        @csrf
                        <input type="hidden" name="email" value="{{ session('otp_email') }}">
                        <div class="form-content">
                            <fieldset class="tf-field">
                                <label for="login-otp" class="tf-lable fw-medium">6 digit OTP <span
                                        class="text-primary">*</span></label>
                                <input type="text" id="login-otp" name="otp" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" placeholder="Enter OTP*" required>
                                @error('otp')
                                    @if (session('auth_modal') === 'sign')
                                        <small class="text-danger">{{ $message }}</small>
                                    @endif
                                @enderror
                            </fieldset>
                        </div>
                        <div class="group-action">
                            <button type="submit" class="tf-btn animate-btn w-100" data-loading-text="Verifying...">
                                Verify & Continue
                            </button>
                            <button type="submit" form="resend-otp-form" class="link text-primary text-decoration-underline bg-transparent border-0 p-0 mt-3" data-loading-text="Sending...">
                                Resend OTP
                            </button>
                        </div>
                    </form>
                    <form id="resend-otp-form" action="{{ route('auth.otp.send') }}" method="POST" class="d-none otp-auth-form">
                        @csrf
                        <input type="hidden" name="email" value="{{ session('otp_email') }}">
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.otp-auth-form').forEach(function(form) {
            form.addEventListener('submit', function(event) {
                var submitter = event.submitter || form.querySelector('[type="submit"]');
                if (!submitter || submitter.disabled) {
                    return;
                }

                submitter.dataset.originalText = submitter.innerHTML;
                submitter.disabled = true;
                submitter.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>' + (submitter.dataset.loadingText || 'Please wait...');
            });
        });
    });
</script>
<!-- /Email Auth -->
