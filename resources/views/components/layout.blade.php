<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{{ str_replace('_', '-', config('app.locale', 'en')) }}"
    lang="{{ str_replace('_', '-', config('app.locale', 'en')) }}">

<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-5ZHLTDJD5Q"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-5ZHLTDJD5Q');
    </script>
    @php
        $seoData = \App\Helpers\SeoHelper::build(
            $seo ?? [
                'title' => $title ?? config('app.name', 'KraftX'),
            ],
        );
    @endphp
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="{{ config('app.name', 'KraftX') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    {!! \App\Helpers\SeoHelper::renderMetaTags($seoData) !!}

    <!-- font -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/icon/icomoon/style.css') }}">
    <!-- css -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/animate.css') }}">
    {{ $styles ?? '' }}
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/styles.css') }}">

    <!-- Favicon and Touch Icons  -->
    <link rel="shortcut icon" href="{{ asset('assets/images/logo/favicon.svg') }}">
    <link rel="apple-touch-icon-precomposed" href="{{ asset('assets/images/logo/favicon.svg') }}">
    @foreach ($seoData['preload'] ?? [] as $preload)
        <link rel="preload" href="{{ $preload['href'] ?? '' }}" as="{{ $preload['as'] ?? 'image' }}"
            @if (!empty($preload['type'])) type="{{ $preload['type'] }}" @endif
            @if (!empty($preload['fetchpriority'])) fetchpriority="{{ $preload['fetchpriority'] }}" @endif>
    @endforeach
    {{ $head ?? '' }}
    {!! \App\Helpers\SeoHelper::renderJsonLd($seoData['json_ld'] ?? []) !!}
    @yield('seo')

</head>

<body>
    <!-- Scroll Top -->
    <button id="goTop">
        <span class="border-progress"></span>
        <span class="ic-wrap">
            <span class="icon icon-CaretTopThin"></span>
        </span>
    </button>
    <!-- /Scroll Top -->

    <!-- Preload -->
    <div class="preload preload-container" id="preload">
        <div class="preload-logo">
            <div class="spinner"></div>
        </div>
    </div>
    <!-- /Preload -->

    <main id="wrapper">
        <x-topbar />
        <x-header />

        {{ $slot }}

        <x-footer />
    </main>

    <!-- Modals and other global elements from index.html -->
    <x-modals />

    <!-- Javascript -->
    <script src="{{ asset('assets/js/plugin/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/bootstrap.min.js') }}"></script>
    <script>
        // Polyfill for bootstrap-select to work with Bootstrap 5
        window.Dropdown = bootstrap.Dropdown;
        // Explicitly set Bootstrap version
        if (typeof $ !== 'undefined' && $.fn && $.fn.selectpicker) {
            $.fn.selectpicker.Constructor.BootstrapVersion = '5';
        }
    </script>
    <script src="{{ asset('assets/js/plugin/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/count-down.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/infinityslide.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/wow.min.js') }}"></script>

    <script src="{{ asset('assets/js/carousel.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('assets/js/wishlist.js') }}"></script>
    {{ $scripts ?? '' }}

    <x-login />

    @if (session('auth_modal'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var modalEl = document.getElementById(@json(session('auth_modal')));

                if (modalEl && window.bootstrap) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                }
            });
        </script>
    @endif


</body>

</html>
