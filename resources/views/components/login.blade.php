<!-- Sign In -->
<div class="modal modalCentered fade modal-log" id="sign">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <span class="icon-close-popup" data-bs-dismiss="modal">
                <i class="icon-X2"></i>
            </span>
            <div class="modal-heading text-center">
                <h3 class="title-pop mb-8">Sign In</h3>
                <p class="desc-pop cl-text-2">Sign in to access your personalized experience.</p>
            </div>
            <div class="modal-main">
                <form action="account-page.html" class="form-log">
                    <div class="form-content">
                        <fieldset class="tf-field">
                            <label for="user-name-log" class="tf-lable fw-medium">Username or email address <span
                                    class="text-primary">*</span></label>
                            <input type="text" id="user-name-log" placeholder="Username or email address*" required>
                        </fieldset>
                        <fieldset class="tf-field password-wrapper">
                            <label for="password" class="tf-lable fw-medium">
                                Password
                                <span class="text-primary">*</span>
                            </label>
                            <div class="password-wrapper w-100">
                                <span class="toggle-pass icon-EyeSlash fs-20 cl-text-3"></span>
                                <input class="password-field" type="password" id="password" placeholder="Password"
                                    required>
                            </div>
                        </fieldset>
                        <fieldset class="field-bottom">
                            <div class="checkbox-wrap">
                                <input class="tf-check style-2" type="checkbox" id="remember">
                                <label for="remember">
                                    Remember me
                                </label>
                            </div>
                            <a href="index.html#modalForgot" data-bs-toggle="modal"
                                class="link text-decoration-underline">
                                <span class="text-caption-01 fw-semibold">
                                    Forgot Your Password?
                                </span>
                            </a>
                        </fieldset>
                    </div>
                    <div class="group-action">
                        <button type="submit" class="tf-btn animate-btn w-100">
                            Login
                        </button>
                        <a href="index.html#register" data-bs-toggle="modal" class="tf-btn btn-stroke">
                            Create Account
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Sign In -->