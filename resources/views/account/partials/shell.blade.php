<x-layout :seo="$seo" title="My Account">
    <div class="btn-sidebar-mb d-lg-none left">
        <button data-bs-toggle="offcanvas" data-bs-target="#accountSidebar">
            <i class="icon icon-sidebar"></i>
        </button>
    </div>

    <section class="section-page-title">
        <div class="container">
            <div class="main-page-title text-center">
                <div class="breadcrumbs justify-content-center">
                    <a href="{{ route('home') }}" class="text-caption-01 cl-text-3 link">Home</a>
                    <i class="icon icon-CaretRightThin cl-text-3"></i>
                    <p class="text-caption-01">My Account</p>
                </div>
                <h3>My Account</h3>
                <p class="text-body-1 cl-text-2">
                    Manage your profile, track orders, and update your details.
                </p>
            </div>
        </div>
    </section>

    <section class="flat-spacing">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success mb-24">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger mb-24">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-4 col-xl-3 d-none d-lg-block">
                    @include('account.partials.nav')
                </div>
                <div class="col-lg-8 ms-auto">
                    <div class="my-account-content">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="offcanvas offcanvas-start" id="accountSidebar" tabindex="-1">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">My Account</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            @include('account.partials.nav')
        </div>
    </div>
</x-layout>
