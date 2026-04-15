<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">

<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Amerce - Multipurpose eCommerce HTML Template' }}</title>
    <meta name="author" content="themesflat.com">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="description"
        content="Themesflat Amerce - A modern and elegant Multipurpose eCommerce HTML Template, perfect for online stores selling rings, necklaces, watches, and other accessories. SEO-optimized, fast-loading, and fully customizable.">



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
    {{ $head ?? '' }}

    <!-- SEO Tags Output via Helper -->



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
    {{ $scripts ?? '' }}

    <x-login />
    <x-register />



</body>

</html>