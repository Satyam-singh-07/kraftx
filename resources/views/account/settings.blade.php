@component('account.partials.shell', ['seo' => $seo])
    <h4 class="account-title">Setting</h4>
    <div class="account-my_address setting">
        <p class="mb-12 h6 fw-medium">Information</p>
        <form method="POST" action="{{ route('account.settings.update') }}" class="form-setting">
            @csrf
            @method('PATCH')
            <div class="form-content">
                <div class="tf-grid-layout sm-col-2">
                    <fieldset class="tf-field">
                        <label for="name" class="tf-lable fw-medium">Name <span class="text-primary">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" placeholder="Name" required>
                    </fieldset>
                    <fieldset class="tf-field">
                        <label for="phone" class="tf-lable fw-medium">Phone Number</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Phone Number">
                    </fieldset>
                </div>
                <fieldset class="tf-field">
                    <label for="email" class="tf-lable fw-medium">Email Address</label>
                    <input type="email" id="email" value="{{ $user->email }}" disabled>
                </fieldset>
            </div>
            <div class="btn-submit">
                <button type="submit" class="tf-btn animate-btn">
                    Save Change
                </button>
            </div>
        </form>
    </div>
@endcomponent
