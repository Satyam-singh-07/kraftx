<x-layout :seo="$seo" title="Account">
    <section class="section-page-title text-center flat-spacing-2 pb-0">
        <div class="container">
            <div class="main-page-title">
                <div class="breadcrumbs justify-content-center">
                    <a href="{{ route('home') }}" class="text-caption-01 cl-text-3 link">Home</a>
                    <i class="icon icon-CaretRightThin cl-text-3"></i>
                    <p class="text-caption-01">Account</p>
                </div>
                <h1>Account</h1>
                <p class="text-body-1 cl-text-2">Manage your KraftX profile and orders.</p>
            </div>
        </div>
    </section>

    <section class="flat-spacing pt-40">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="border rounded-20 p-40">
                        <h2 class="h4 mb-16">{{ auth()->user()->name }}</h2>
                        <p class="text-body-2 cl-text-2 mb-24">{{ auth()->user()->email }}</p>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="tf-btn animate-btn">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout>
