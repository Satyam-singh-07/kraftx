@component('account.partials.shell', ['seo' => $seo])
    <h4 class="account-title">My Address</h4>
    <div class="account-my_address">
        <form method="POST" action="{{ route('account.addresses.update') }}" class="form-account-address">
            @csrf
            @method('PATCH')
            <div class="form-content">
                <fieldset class="tf-field">
                    <label for="address" class="tf-lable fw-medium">Street Address</label>
                    <textarea id="address" name="address" rows="5" placeholder="Street Address">{{ old('address', $user->address) }}</textarea>
                </fieldset>
                <div class="tf-grid-layout sm-col-2">
                    <fieldset class="tf-field">
                        <label for="phone" class="tf-lable fw-medium">Phone</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Phone">
                    </fieldset>
                    <fieldset class="tf-field">
                        <label for="email" class="tf-lable fw-medium">Email</label>
                        <input type="email" id="email" value="{{ $user->email }}" disabled>
                    </fieldset>
                </div>
            </div>
            <button type="submit" class="btn-action-submit tf-btn animate-btn">
                Update Address
            </button>
        </form>
    </div>
@endcomponent
