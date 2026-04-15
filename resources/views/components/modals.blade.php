<!-- Mobile Menu -->
<div class="offcanvas offcanvas-start canvas-mb" id="mobileMenu">
    <div class="canvas-header">
        <span class="icon-close-popup" data-bs-dismiss="offcanvas">
            <i class="icon icon-X2"></i>
        </span>
        <form class="form-search-nav">
            <fieldset>
                <input type="text" placeholder="What are you looking for?" required>
            </fieldset>
            <button type="submit" class="btn-action">
                <i class="icon icon-MagnifyingGlass"></i>
            </button>
        </form>
    </div>
    <div class="canvas-body">
        <div class="mb-content-top">
            <ul class="nav-ul-mb" id="wrapper-menu-navigation"></ul>
        </div>
        <div class="need-help-wrap">
            <p class="nd-title h6 fw-medium mb-16">Need Help?</p>
            <p class="lh-26 cl-text-2 mb-4">
                600 N Michigan Ave, Chicago, IL 60611, USA
            </p>
            <a href="https://www.google.com/maps?q=600+N+Michigan+Ave+Chicago,+IL+60611+USA" target="_blank"
                class="text-decoration-underline text-primary lh-26 mb-16">
                Open in Maps
            </a>
            <a href="mailto:hi.amere@gmail.com" class="cl-text-2 link mb-8">
                hi.amere@gmail.com
            </a>
            <a href="tel:3156666688" class="cl-text-2 link">
                315-666-6688
            </a>
        </div>
    </div>
    <div class="canvas-footer">
        <div class="d-flex justify-content-center border-end">
            <div class="tf-currencies">
                <select class="tf-dropdown-select style-default type-currencies">
                    <option selected data-thumbnail="{{ asset('assets/images/country/us.png') }}">(USD $)</option>
                    <option data-thumbnail="{{ asset('assets/images/country/vn.png') }}">(VND ₫)</option>
                </select>
            </div>
        </div>
        <div class="d-flex justify-content-center">
            <div class="tf-languages">
                <select class="tf-dropdown-select style-default type-languages">
                    <option>English</option>
                    <option>العربية</option>
                    <option>简体中文</option>
                    <option>اردو</option>
                </select>
            </div>
        </div>
    </div>
</div>
<!-- /Mobile Menu -->

<!-- Toolbar -->
<div class="tf-toolbar-bottom">
    <div class="toolbar-item">
        <a href="{{ route('home') }}">
            <span class="toolbar-icon">
                <i class="icon icon-storefront"></i>
            </span>
            <span class="toolbar-label">Shop</span>
        </a>
    </div>
    <div class="toolbar-item">
        <a href="#search" data-bs-toggle="modal">
            <span class="toolbar-icon">
                <i class="icon icon-MagnifyingGlass"></i>
            </span>
            <span class="toolbar-label">Search</span>
        </a>
    </div>
    <div class="toolbar-item">
        <a href="#">
            <span class="toolbar-icon">
                <i class="icon icon-User"></i>
            </span>
            <span class="toolbar-label">Account</span>
        </a>
    </div>
    <div class="toolbar-item">
        <a href="#">
            <span class="toolbar-icon">
                <i class="icon icon-HeartStraight"></i>
            </span>
            <span class="toolbar-label">Wishlist</span>
        </a>
    </div>
    <div class="toolbar-item">
        <a href="#shoppingCart" data-bs-toggle="offcanvas">
            <span class="toolbar-icon">
                <i class="icon icon-Handbag"></i>
                <span class="toolbar-count">12</span>
            </span>
            <span class="toolbar-label">Cart</span>
        </a>
    </div>
</div>
<!-- /Toolbar -->

<!-- Forgot Pass -->
<div class="modal modalCentered fade modal-log modal-log_forgot" id="modalForgot">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <span class="icon-close-popup" data-bs-dismiss="modal">
                <i class="icon-X2"></i>
            </span>
            <div class="modal-heading text-center">
                <h3 class="title-pop mb-8">Forgot Password</h3>
                <p class="desc-pop cl-text-2">We’ll send instructions to reset your password.</p>
            </div>
            <div class="modal-main">
                <form class="form-log">
                    <div class="form-content">
                        <fieldset class="tf-field">
                            <label for="forgot-user" class="tf-lable fw-medium">
                                Username or email address
                                <span class="text-primary">*</span>
                            </label>
                            <input type="text" id="forgot-user" placeholder="Username or email address*" required>
                        </fieldset>
                    </div>
                    <div class="group-action">
                        <button type="submit" class="tf-btn animate-btn w-100">
                            Get Reset Code
                        </button>
                        <p class="orther-log text-center">
                            Remember your password?
                            <a href="#sign" data-bs-toggle="modal" class="text-primary text-decoration-underline">
                                Sign In
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Forgot Pass -->

<!-- Size Guide -->
<div class="modal modalCentered fade modal-find_size" id="findSize">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-heading d-flex align-items-center justify-content-between">
                <h4 class="title-pop">Size Chart</h4>
                <span class="cs-pointer d-flex link" data-bs-dismiss="modal">
                    <i class="icon-X2 fs-24"></i>
                </span>
            </div>
            <div class="modal-main">
                <div class="tf-rte">
                    <div class="tf-table-res-df mb-20">
                        <p class="h6 fw-medium mb-16 cl-text-main">Size Guide</p>
                        <div class="overflow-auto">
                            <table class="tf-sizeguide-table">
                                <thead>
                                    <tr>
                                        <th>Size</th>
                                        <th>US</th>
                                        <th>Bust</th>
                                        <th>Waist</th>
                                        <th>Low Hip</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>XS</td>
                                        <td>2</td>
                                        <td>32</td>
                                        <td>24 - 25</td>
                                        <td>33 - 34</td>
                                    </tr>
                                    <tr>
                                        <td>S</td>
                                        <td>4</td>
                                        <td>34 - 35</td>
                                        <td>26 - 27</td>
                                        <td>35 - 26</td>
                                    </tr>
                                    <tr>
                                        <td>M</td>
                                        <td>6</td>
                                        <td>36 - 37</td>
                                        <td>28 - 29</td>
                                        <td>38 - 40</td>
                                    </tr>
                                    <tr>
                                        <td>L</td>
                                        <td>8</td>
                                        <td>38 - 29</td>
                                        <td>30 - 31</td>
                                        <td>42 - 44</td>
                                    </tr>
                                    <tr>
                                        <td>XL</td>
                                        <td>10</td>
                                        <td>40 - 41</td>
                                        <td>32 - 33</td>
                                        <td>45 - 47</td>
                                    </tr>
                                    <tr>
                                        <td>XXL</td>
                                        <td>12</td>
                                        <td>42 - 43</td>
                                        <td>34 - 35</td>
                                        <td>48 - 50</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tf-page-size-chart-content">
                        <div>
                            <p class="h6 fw-medium mb-16 cl-text-main">Measuring Tips</p>
                            <div class="title fw-medium">Bust</div>
                            <p class="mb-12">Measure around the fullest part of your bust.</p>
                            <div class="title fw-medium">Waist</div>
                            <p class="mb-12">Measure around the narrowest part of your torso.</p>
                            <div class="title fw-medium">Low Hip</div>
                            <p class="mb-12">With your feet together measure around the fullest part of your
                                hips/rear.
                            </p>
                        </div>
                        <div>
                            <img loading="lazy" width="270" height="270" src="{{ asset('assets/images/section/size-chart.jpg') }}"
                                alt="Image">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Size Guide -->

<!-- Share -->
<div class="modal modalCentered fade modal-share" id="share">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-heading d-flex align-items-center justify-content-between">
                <h4 class="title-pop">Share</h4>
                <span class="cs-pointer d-flex link" data-bs-dismiss="modal">
                    <i class="icon-X2 fs-24"></i>
                </span>
            </div>
            <div class="modal-main">
                <ul class="tf-social-icon-2 hv-dark mb-20">
                    <li>
                        <a href="https://www.facebook.com/">
                            <i class="icon icon-FacebookLogo"></i>
                        </a>
                    </li>
                    <li>
                        <a href="https://x.com/">
                            <i class="icon icon-XLogo"></i>
                        </a>
                    </li>
                    <li>
                        <a href="https://www.instagram.com/">
                            <i class="icon icon-InstagramLogo"></i>
                        </a>
                    </li>
                    <li>
                        <a href="https://www.tiktok.com/">
                            <i class="icon icon-TiktokLogo"></i>
                        </a>
                    </li>
                </ul>
                <div class="wrap-code btn-coppy-text">
                    <p class="coppyText cl-text-2" id="coppyText">http://themesflat.com</p>
                    <div class="btn-action-copy tf-btn">Copy</div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Share -->

<!-- Ask -->
<div class="modal modalCentered fade modal-log modal-ask" id="ask">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <span class="icon-close-popup" data-bs-dismiss="modal">
                <i class="icon-X2"></i>
            </span>
            <div class="modal-heading text-center">
                <h3 class="title-pop mb-8">Ask A Question</h3>
                <p class="desc-pop cl-text-2">Have a question? Ask us today!</p>
            </div>
            <div class="modal-main">
                <form class="form-log mb-20">
                    <div class="form-content">
                        <fieldset class="tf-field">
                            <label for="name-ask" class="tf-lable fw-medium">
                                Your Name<span class="text-primary">*</span>
                            </label>
                            <input type="text" id="name-ask" placeholder="Your Name*" required>
                        </fieldset>
                        <fieldset class="tf-field">
                            <label for="email-ask" class="tf-lable fw-medium">
                                Your Email<span class="text-primary">*</span>
                            </label>
                            <input type="email" id="email-ask" placeholder="Your Email*" required>
                        </fieldset>
                        <fieldset class="tf-field">
                            <label for="phone-ask" class="tf-lable fw-medium">
                                Your phone
                            </label>
                            <input type="number" id="phone-ask" placeholder="Your phone" required>
                        </fieldset>
                        <fieldset class="tf-field">
                            <label for="message-ask" class="tf-lable fw-medium">
                                Your Message
                                <span class="text-primary">*</span>
                            </label>
                            <textarea id="message-ask" placeholder="Your Message*" required></textarea>
                        </fieldset>
                    </div>
                    <div class="group-action">
                        <button type="submit" class="tf-btn animate-btn w-100">
                            Subcribe
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Ask -->

<!-- Compare -->
<div class="offcanvas offcanvas-bottom canvas-compare" id="compare">
    <div class="canvas-wrapper">
        <div class="canvas-body">
            <div class="container">
                <div class="tf-compare-list main-list-clear wrap-empty_text">
                    <div class="tf-compare-head">
                        <h4 class="title letter-space-0">Compare products</h4>
                    </div>
                    <div class="tf-compare-offcanvas list-empty">
                        <p class="box-text_empty cl-text-2">Your Compare is curently empty</p>
                    </div>
                    <div class="tf-compare-buttons justify-content-center">
                        <a href="#" class="tf-btn animate-btn">Compare </a>
                        <div class="tf-btn btn-white btn-stroke clear-list-empty tf-compare-button-clear-all">
                            Clear All
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Compare -->

<!-- Quick Add -->
<div class="modal modalCentered fade modal-quickadd" id="quickAdd">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="d-flex align-items-center justify-content-between mb-20">
                <h5>Quick Add</h5>
                <span class="d-flex cs-pointer link" data-bs-dismiss="modal">
                    <i class="icon icon-X2 fs-24"></i>
                </span>
            </div>
            <div class="tf-product-quick_add tf-quick-prd_variant">
                <div class="product-mini-view">
                    <a href="{{ route('product.detail') }}" class="prd-image">
                        <img class="img-product" width="80" height="107"
                            src="{{ asset('assets/images/product/single/detail-1.jpg') }}" alt="Image Product">
                    </a>
                    <div class="prd-content">
                        <a href="{{ route('product.detail') }}"
                            class="prd-name fw-medium link-underline link text-capitalize">
                            linen slim-fit shirt
                        </a>
                        <div class="price-wrap">
                            <span class="price-new text-primary fw-semibold price-on-sale">$79.99</span>
                        </div>
                    </div>
                </div>
                <div class="quick-variant-picker picker_size">
                    <div class="variant-picker_label mb-12">
                        <div>
                            Size:
                            <span class="variant__value text-capitalize fw-medium">L</span>
                        </div>
                    </div>
                    <div class="variant-picker_values">
                        <span class="size_btn" data-quick-size="S" data-quick-price="39.99">S</span>
                        <span class="size_btn" data-quick-size="M" data-quick-price="59.99">M</span>
                        <span class="size_btn active" data-quick-size="L" data-quick-price="79.99">L</span>
                        <span class="size_btn" data-quick-size="XL" data-quick-price="89.99">XL</span>
                    </div>
                </div>
                <div class="product-total-quantity">
                    <div class="group-action">
                        <div class="wg-quantity">
                            <button class="btn-quantity btn-decrease"><i class="icon icon-minus"></i></button>
                            <input class="quantity-product" type="text" name="number" value="1">
                            <button class="btn-quantity btn-increase"><i class="icon icon-plus"></i></button>
                        </div>
                        <a href="#shoppingCart" data-bs-toggle="offcanvas"
                            class="btn-action-price tf-btn type-xl animate-btn w-100">
                            Add to Cart - $79.99
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Quick Add -->

<!-- Quick View -->
<div class="offcanvas offcanvas-end canvas-quickview" id="quickView">
    <div class="mini-quick-image">
        <div class="wrap-quick wrapper-scroll-quickview">
            <div class="image item-scroll-quickview" data-scroll-quickview="Green">
                <img loading="lazy" width="340" height="444" src="{{ asset('assets/images/product/single/detail-1.jpg') }}"
                    alt="Image">
            </div>
        </div>
    </div>
    <div class="wrap-canvas">
        <div class="canvas-header ps-md-0">
            <h5 class="title-pop">Quick View</h5>
            <span class="icon-close-popup" data-bs-dismiss="offcanvas">
                <i class="icon icon-X2"></i>
            </span>
        </div>
        <div class="canvas-body ps-md-0">
            <div class="tf-product-quick_view tf-quick-prd_variant">
                <div class="tf-product-info-heading">
                    <p class="product-infor-cate text-caption-01 mb-4">Clothing</p>
                    <h3 class="product-infor-name mb-12">Lyocell wrap top</h3>
                    <div class="product-infor-price mb-12">
                        <h4 class="price-on-sale">$59.99</h4>
                    </div>
                </div>
                <div class="tf-product-total-quantity">
                    <div class="group-action">
                        <a href="#shoppingCart" data-bs-toggle="offcanvas"
                            class="btn-action-price tf-btn type-xl animate-btn w-100">
                            Add to Cart - $59.99
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Quick View -->

<!-- Shopping Cart -->
<div class="offcanvas offcanvas-end popup-shopping-cart" id="shoppingCart">
    <div class="canvas-wrapper">
        <div class="popup-header">
            <div class="d-flex align-items-center justify-content-between mb-12">
                <h5 class="title">Shopping Cart</h5>
                <span class="icon-X2 icon-close-popup" data-bs-dismiss="offcanvas"></span>
            </div>
        </div>
        <div class="wrap">
            <div class="tf-mini-cart-wrap list-file-delete wrap-empty_text text-center">
                <div class="shop-empty_top py-5">
                    <span class="icon"><i class="icon-Handbag fs-60"></i></span>
                    <h4 class="text-emp mt-3">Your cart is empty</h4>
                </div>
                <div class="shop-empty_bot">
                    <a href="{{ route('home') }}" class="tf-btn animate-btn">Start Shopping</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Shopping Cart -->

<!-- Search -->
<div class="modal modalCentered fade modal-search" id="search">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="d-flex align-items-center justify-content-between gap-10">
                <h3>Search</h3>
                <span class="icon-close-popup flex-shrink-0" data-bs-dismiss="modal">
                    <i class="icon-X2"></i>
                </span>
            </div>
            <form action="#" class="form-search-nav style-2">
                <fieldset>
                    <input type="text" placeholder="Searching..." required>
                </fieldset>
                <button type="submit" class="btn-action">
                    <i class="icon icon-MagnifyingGlass"></i>
                </button>
            </form>
        </div>
    </div>
</div>
<!-- /Search -->

<!-- Register -->
<div class="modal modalCentered fade modal-log" id="register">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <span class="icon-close-popup" data-bs-dismiss="modal">
                <i class="icon-X2"></i>
            </span>
            <div class="modal-heading text-center">
                <h3 class="title-pop mb-8">Create Account</h3>
            </div>
            <div class="modal-main">
                <form action="#" class="form-log">
                    <div class="form-content">
                        <fieldset class="tf-field">
                            <label class="tf-lable fw-medium">Email address *</label>
                            <input type="email" placeholder="Email address" required>
                        </fieldset>
                        <fieldset class="tf-field password-wrapper">
                            <label class="tf-lable fw-medium">Password *</label>
                            <input class="password-field" type="password" placeholder="Password" required>
                        </fieldset>
                    </div>
                    <div class="group-action">
                        <button type="submit" class="tf-btn animate-btn w-100">Create Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Register -->

<!-- Sign In -->
<div class="modal modalCentered fade modal-log" id="sign">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <span class="icon-close-popup" data-bs-dismiss="modal">
                <i class="icon-X2"></i>
            </span>
            <div class="modal-heading text-center">
                <h3 class="title-pop mb-8">Sign In</h3>
            </div>
            <div class="modal-main">
                <form action="#" class="form-log">
                    <div class="form-content">
                        <fieldset class="tf-field">
                            <label class="tf-lable fw-medium">Email address *</label>
                            <input type="email" placeholder="Email address" required>
                        </fieldset>
                        <fieldset class="tf-field password-wrapper">
                            <label class="tf-lable fw-medium">Password *</label>
                            <input class="password-field" type="password" placeholder="Password" required>
                        </fieldset>
                    </div>
                    <div class="group-action">
                        <button type="submit" class="tf-btn animate-btn w-100">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Sign In -->

<!-- Newsletter -->
<div class="modal modalCentered fade modal-newsletter auto-popup" id="newsletter" data-bs-config='{"backdrop":true}'>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="image-left">
                <img loading="lazy" width="360" height="360" src="{{ asset('assets/images/section/banner-newsletter.jpg') }}"
                    alt="Image">
            </div>
            <div class="content-right">
                <span class="icon-close-popup" data-bs-dismiss="modal">
                    <i class="icon-X2"></i>
                </span>
                <p class="h6 mb-8">Subscribe & Enjoy</p>
                <p class="h1 fw-medium mb-8 text-primary">10% OFF</p>
                <form class="form-newsletter mb-12">
                    <fieldset>
                        <input type="email" placeholder="Your email address" required>
                    </fieldset>
                    <button type="submit" class="btn-action tf-btn small animate-btn">Subscribe</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Newsletter -->
