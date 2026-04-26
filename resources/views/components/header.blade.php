<!-- Header -->
<header class="tf-header header-s2 scr-box-shadow">
    <div class="container-full">
        <div class="header-inner">
            <div class="box-open-menu-mobile d-xl-none">
                <a href="#mobileMenu" data-bs-toggle="offcanvas" class="btn-open-menu">
                    <i class="icon icon-List"></i>
                </a>
            </div>
            <div class="header-left d-none d-xl-block">
                <nav class="box-navigation">
                    <ul class="box-nav-menu">
                        <li class="menu-item position-relative">
                            <a href="{{ route('home') }}" class="item-link">
                                <span class="text cus-text">Home</span>
                            </a>
                            <!-- ... (Home Submenu) ... -->
                        </li>
                        <li class="menu-item">
                            <a href="#" class="item-link">
                                <span class="text cus-text">God Idols</span>

                            </a>
                            <!-- ... (Shop Submenu) ... -->
                        </li>
                        <li class="menu-item">
                            <a href="{{ route('collection.show', ['slug' => 'car-dashboard-idols']) }}" class="item-link">
                                <span class="text cus-text">Car Dashboard Idols</span>
                            </a>
                            <!-- ... (Product Submenu) ... -->
                        </li>
                        <li class="menu-item position-relative">
                            <a href="{{ route('blog.index') }}" class="item-link">
                                <span class="text cus-text">Blog</span>

                            </a>
                        </li>

                    </ul>
                </nav>
            </div>
            <div class="header-center ">
                <a href="{{ route('home') }}" class="logo-site">
                    <img loading="lazy" width="150" height="30" src="{{ asset('assets/images/logo/logo.png') }}"
                        alt="Image">
                </a>
            </div>
            <div class="header-right">

                <div class="br-line type-vertical d-none d-xxl-flex"></div>
                <ul class="nav-icon-list">
                    <li class="d-none d-sm-block">
                        <a href="#search" data-bs-toggle="modal" class="nav-icon-item link">
                            <i class="icon icon-MagnifyingGlass"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#sign" data-bs-toggle="modal" class="nav-icon-item link">
                            <i class="icon icon-User"></i>
                        </a>
                    </li>
                    <li class="d-none d-sm-block">
                        <a href="#" class="nav-icon-item link">
                            <i class="icon icon-HeartStraight"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#shoppingCart" data-bs-toggle="offcanvas" class="nav-icon-item link shop-cart">
                            <i class="icon icon-Handbag"></i>
                            <span class="count cart-count">0</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
<!-- /Header -->