<!-- Register -->
<div class="modal modalCentered fade modal-log" id="register">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <span class="icon-close-popup" data-bs-dismiss="modal">
                <i class="icon-X2"></i>
            </span>
            <div class="modal-heading text-center">
                <h3 class="title-pop mb-8">Create Account</h3>
                <p class="desc-pop cl-text-2">Be part of our growing family of new customers!</p>
            </div>
            <div class="modal-main">
                <form action="account-page.html" class="form-log">
                    <div class="form-content">
                        <fieldset class="tf-field">
                            <label for="user-name" class="tf-lable fw-medium">Username or email address <span
                                    class="text-primary">*</span></label>
                            <input type="text" id="user-name" placeholder="Username or email address*" required>
                        </fieldset>
                        <fieldset class="tf-field password-wrapper">
                            <label for="register-password" class="tf-lable fw-medium">
                                Password
                                <span class="text-primary">*</span>
                            </label>
                            <div class="password-wrapper w-100">
                                <span class="toggle-pass icon-EyeSlash fs-20 cl-text-3"></span>
                                <input class="password-field" type="password" id="register-password"
                                    placeholder="Password" required>
                            </div>
                        </fieldset>
                        <fieldset class="tf-field password-wrapper">
                            <label for="register-password-confirm" class="tf-lable fw-medium">
                                Confirm Password
                                <span class="text-primary">*</span>
                            </label>
                            <div class="password-wrapper w-100">
                                <span class="toggle-pass icon-EyeSlash fs-20 cl-text-3"></span>
                                <input class="password-field" type="password" id="register-password-confirm"
                                    placeholder="Confirm Password" required>
                            </div>
                        </fieldset>
                    </div>
                    <div class="group-action">
                        <button type="submit" class="action-create-account tf-btn animate-btn w-100">
                            Create Account
                        </button>
                        <a href="index.html#sign" data-bs-toggle="modal" class="tf-btn btn-stroke">
                            Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Register -->