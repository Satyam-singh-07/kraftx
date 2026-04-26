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
       
    </div>
    
</div>
<!-- /Mobile Menu -->

<!-- Toolbar -->
<div class="tf-toolbar tf-toolbar-bottom">
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
        <a href="#sign" data-bs-toggle="modal">
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
    <div class="toolbar-item is-cart">
        <a href="#shoppingCart" data-bs-toggle="offcanvas">
            <span class="toolbar-icon">
                <i class="icon icon-Handbag"></i>
                <span class="toolbar-count cart-count">0</span>
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
            <div id="quick-add-content" class="tf-product-quick_add tf-quick-prd_variant">
                <!-- Content will be loaded dynamically -->
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quickAddModal = document.getElementById('quickAdd');
        const quickAddContent = document.getElementById('quick-add-content');

        // Handle Quick Add buttons
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-bs-target="#quickAdd"]');
            if (!btn) return;

            const productId = btn.dataset.productId;
            if (!productId) return;

            // Show loading
            quickAddContent.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            `;

            fetch(`/product/${productId}/quick-add`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderQuickAdd(data.product);
                    }
                })
                .catch(error => console.error('Error:', error));
        });

        function renderQuickAdd(product) {
            const hasVariants = product.variants.length > 0;
            const sizes = product.sizes;
            const colors = product.colors;

            let html = `
                <div class="product-mini-view mb-24">
                    <a href="/product/${product.slug}" class="prd-image">
                        <img class="img-product" width="80" height="107" src="${product.image}" alt="${product.name}">
                    </a>
                    <div class="prd-content">
                        <a href="/product/${product.slug}" class="prd-name fw-medium link-underline link text-capitalize">
                            ${product.name}
                        </a>
                        <div class="price-wrap mt-4">
                            <span class="price-new text-primary fw-semibold">${product.sale_price ? '₹'+product.sale_price : '₹'+product.price}</span>
                            ${product.sale_price ? `<span class="price-old ms-2 text-muted text-decoration-line-through">₹${product.price}</span>` : ''}
                        </div>
                    </div>
                </div>
            `;

            if (colors.length > 0) {
                html += `
                    <div class="quick-variant-picker picker_color mb-20">
                        <div class="variant-picker_label mb-12">Color: <span class="variant__value fw-medium" id="selected-color-val">Select Color</span></div>
                        <div class="variant-picker_values d-flex gap-8 flex-wrap">
                            ${colors.map(color => `<span class="color_btn" data-value="${color}" title="${color}">${color}</span>`).join('')}
                        </div>
                    </div>
                `;
            }

            if (sizes.length > 0) {
                html += `
                    <div class="quick-variant-picker picker_size mb-24">
                        <div class="variant-picker_label mb-12">Size: <span class="variant__value fw-medium" id="selected-size-val">Select Size</span></div>
                        <div class="variant-picker_values d-flex gap-8 flex-wrap">
                            ${sizes.map(size => `<span class="size_btn" data-value="${size}">${size}</span>`).join('')}
                        </div>
                    </div>
                `;
            }

            html += `
                <div class="product-total-quantity">
                    <div class="group-action">
                        <div class="wg-quantity mb-16">
                            <button class="btn-quantity btn-decrease-qa"><i class="icon icon-minus"></i></button>
                            <input class="quantity-product-qa" type="text" name="number" value="1" readonly>
                            <button class="btn-quantity btn-increase-qa"><i class="icon icon-plus"></i></button>
                        </div>
                        <div class="d-flex gap-10">
                            <button type="button" id="confirm-quick-add" class="btn-action-price tf-btn type-xl animate-btn flex-grow-1" data-product-id="${product.id}">
                                Add to Cart
                            </button>
                         
                        </div>
                    </div>
                </div>
            `;

            quickAddContent.innerHTML = html;

            // Internal logic for variant selection
            const colorBtns = quickAddContent.querySelectorAll('.color_btn');
            const sizeBtns = quickAddContent.querySelectorAll('.size_btn');
            const qtyInput = quickAddContent.querySelector('.quantity-product-qa');
            const confirmBtn = document.getElementById('confirm-quick-add');
            const buyNowBtn = document.getElementById('buy-now-quick-add');
            
            let selectedColor = null;
            let selectedSize = null;

            colorBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    colorBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    selectedColor = btn.dataset.value;
                    document.getElementById('selected-color-val').textContent = selectedColor;
                });
            });

            sizeBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    sizeBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    selectedSize = btn.dataset.value;
                    document.getElementById('selected-size-val').textContent = selectedSize;
                });
            });

            quickAddContent.querySelector('.btn-increase-qa').addEventListener('click', () => {
                qtyInput.value = parseInt(qtyInput.value) + 1;
            });

            quickAddContent.querySelector('.btn-decrease-qa').addEventListener('click', () => {
                if (parseInt(qtyInput.value) > 1) {
                    qtyInput.value = parseInt(qtyInput.value) - 1;
                }
            });

            function addToCart(isBuyNow = false) {
                if (colors.length > 0 && !selectedColor) {
                    alert('Please select a color');
                    return;
                }
                if (sizes.length > 0 && !selectedSize) {
                    alert('Please select a size');
                    return;
                }

                const targetBtn = isBuyNow ? buyNowBtn : confirmBtn;
                const originalText = targetBtn.textContent;
                
                targetBtn.disabled = true;
                targetBtn.textContent = isBuyNow ? 'Processing...' : 'Adding...';

                fetch('{{ route('cart.add') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: product.id,
                        quantity: qtyInput.value,
                        color: selectedColor,
                        size: selectedSize
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(quickAddModal).hide();
                        if (isBuyNow) {
                            window.location.href = '/checkout';
                        } else {
                            if (window.refreshCartDrawer) window.refreshCartDrawer();
                            const cartOffcanvas = document.getElementById('shoppingCart');
                            if (cartOffcanvas) {
                                bootstrap.Offcanvas.getOrCreateInstance(cartOffcanvas).show();
                            }
                        }
                    } else {
                        alert(data.message || 'Error adding to cart');
                        targetBtn.disabled = false;
                        targetBtn.textContent = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    targetBtn.disabled = false;
                    targetBtn.textContent = originalText;
                });
            }

            confirmBtn.addEventListener('click', () => addToCart(false));
            buyNowBtn.addEventListener('click', () => addToCart(true));
        }
    });
</script>

<style>
    .color_btn, .size_btn {
        padding: 6px 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s;
    }
    .color_btn.active, .size_btn.active {
        border-color: #171717;
        background: #171717;
        color: #fff;
    }
    .wg-quantity {
        display: flex;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 8px;
        width: fit-content;
    }
    .wg-quantity .btn-quantity {
        padding: 8px 12px;
        background: none;
        border: none;
    }
    .wg-quantity input {
        width: 40px;
        text-align: center;
        border: none;
        background: none;
    }
</style>
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
                        <h4 class="price-on-sale">₹59.99</h4>
                    </div>
                </div>
                <div class="tf-product-total-quantity">
                    <div class="group-action">
                        <a href="#shoppingCart" data-bs-toggle="offcanvas"
                            class="btn-action-price tf-btn type-xl animate-btn w-100">
                            Add to Cart - ₹59.99
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Quick View -->

<style>
    .popup-shopping-cart {
        width: min(920px, 100vw) !important;
        max-width: min(920px, 100vw) !important;
        padding: 0;
        border: 0;
        background: #f7f3ee !important; /* Force background */
        color: #171717;
        display: flex;
        flex-direction: row;
        overflow: hidden;
        pointer-events: auto !important; /* Fix for desktop closure */
    }

    .popup-shopping-cart .btn-reset {
        border: 0;
        background: transparent;
        padding: 0;
        color: inherit;
    }

    .cart-drawer-recommendations {
        width: 270px;
        flex: 0 0 270px;
        padding: 26px 20px 22px;
        background: #fbf8f4;
        border-right: 1px solid rgba(23, 23, 23, 0.08);
        display: flex;
        flex-direction: column;
        gap: 18px;
        pointer-events: auto !important;
    }

    .cart-drawer-main {
        flex: 1 1 auto;
        min-width: 0;
        padding: 26px 22px 22px;
        display: flex;
        flex-direction: column;
        gap: 18px;
        background: #fffdfb;
        pointer-events: auto !important;
    }

    .cart-drawer-panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .cart-drawer-panel-head h5,
    .cart-drawer-main-header h5 {
        margin: 0;
        font-size: 32px;
        line-height: 1;
        font-weight: 500;
        letter-spacing: -0.03em;
    }

    .cart-drawer-close {
        width: 40px;
        height: 40px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        transition: background-color 0.25s ease, transform 0.25s ease;
    }

    .cart-drawer-close:hover {
        background: rgba(23, 23, 23, 0.06);
        transform: rotate(90deg);
    }

    .cart-recommendations-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
        overflow: auto;
        min-height: 0;
        padding-right: 6px;
    }

    .cart-recommendation-card {
        display: flex;
        flex-direction: column;
        gap: 12px;
        text-decoration: none;
        color: inherit;
    }

    .cart-recommendation-media {
        aspect-ratio: 0.82;
        border-radius: 18px;
        overflow: hidden;
        background: #f1ece5;
    }

    .cart-recommendation-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .cart-recommendation-name {
        margin: 0;
        font-size: 18px;
        line-height: 1.3;
        font-weight: 500;
    }

    .cart-recommendation-price {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        font-size: 17px;
    }

    .cart-recommendation-price .sale {
        color: #d0523a;
        font-weight: 600;
    }

    .cart-recommendation-price .compare {
        color: rgba(23, 23, 23, 0.45);
        text-decoration: line-through;
    }

    .cart-drawer-main-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .cart-threshold {
        padding: 18px 20px;
        border-radius: 18px;
        background: #f7f4ef;
        display: grid;
        gap: 12px;
    }

    .tf-threshold-text {
        margin: 0;
        font-size: 17px;
        line-height: 1.4;
    }

    .tf-threshold-text .accent {
        color: #d0523a;
        font-weight: 600;
    }

    .tf-threshold-bar {
        position: relative;
        width: 100%;
        height: 6px;
        border-radius: 999px;
        background: #ddd7d0;
        overflow: visible;
    }

    .tf-threshold-bar-progress {
        position: relative;
        display: block;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #7ab663 0%, #98c77a 100%);
        transition: width 0.35s ease;
    }

    .tf-threshold-bar-progress::after {
        content: "";
        position: absolute;
        top: 50%;
        right: -1px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid #8fbe74;
        background: #fffdfb;
        transform: translate(50%, -50%);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .tf-mini-cart-wrap {
        flex: 1 1 auto;
        min-height: 0;
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .tf-mini-cart-main {
        flex: 1 1 auto;
        min-height: 0;
        position: relative;
    }

    .tf-mini-cart-sroll {
        position: absolute;
        inset: 0;
        overflow: auto;
        padding-right: 8px;
    }

    .tf-mini-cart-sroll::-webkit-scrollbar,
    .cart-recommendations-list::-webkit-scrollbar {
        width: 6px;
    }

    .tf-mini-cart-sroll::-webkit-scrollbar-thumb,
    .cart-recommendations-list::-webkit-scrollbar-thumb {
        background: rgba(23, 23, 23, 0.14);
        border-radius: 999px;
    }

    .tf-mini-cart-items {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .tf-mini-cart-item {
        display: grid;
        grid-template-columns: 104px minmax(0, 1fr);
        gap: 16px;
        padding: 14px 0 18px;
        border-bottom: 1px solid rgba(23, 23, 23, 0.08);
    }

    .tf-mini-cart-items .tf-mini-cart-item:first-child {
        padding-top: 0;
    }

    .tf-mini-cart-image {
        border-radius: 16px;
        overflow: hidden;
        background: #f3eee7;
        aspect-ratio: 1 / 1.08;
    }

    .tf-mini-cart-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .tf-mini-cart-info {
        min-width: 0;
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 10px 16px;
        align-items: start;
    }

    .cart-item-copy {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .cart-item-copy .title {
        color: #171717;
        font-size: 19px;
        line-height: 1.3;
        font-weight: 500;
        text-decoration: none;
    }

    .cart-item-meta {
        display: flex;
        flex-direction: column;
        gap: 4px;
        color: rgba(23, 23, 23, 0.5);
        font-size: 15px;
        line-height: 1.35;
    }

    .cart-item-meta strong {
        color: #171717;
        font-weight: 500;
    }

    .cart-item-controls {
        grid-column: 1 / -1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .cart-item-summary {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 10px;
        text-align: right;
    }

    .remove-cart-item {
        color: #cf5942;
        text-decoration: underline;
        font-size: 15px;
        line-height: 1.2;
        cursor: pointer;
    }

    .cart-item-line-total {
        font-size: 17px;
        font-weight: 700;
        white-space: nowrap;
    }

    .wg-quantity.small {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        padding: 8px 14px;
        border: 1px solid rgba(23, 23, 23, 0.1);
        border-radius: 999px;
        background: #faf7f2;
    }

    .wg-quantity.small input {
        width: 18px;
        padding: 0;
        border: 0;
        background: transparent;
        text-align: center;
        font-weight: 600;
        color: #171717;
    }

    .wg-quantity.small .btn-quantity {
        font-size: 20px;
        line-height: 1;
        color: #171717;
        user-select: none;
    }

    #cart-empty-state {
        margin: auto 0;
        padding: 56px 24px;
        border-radius: 24px;
        background: #f7f4ef;
    }

    .cart-quick-actions {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }

    .cart-quick-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 14px;
        border-radius: 12px;
        border: 1px solid rgba(23, 23, 23, 0.08);
        background: #fff;
        font-size: 16px;
        color: #171717;
    }

    .tf-mini-cart-bottom {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .tf-mini-cart-total-content {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 8px;
    }

    .tf-mini-cart-total-content span:first-child {
        font-size: 18px;
        font-weight: 600;
    }

    #cart-subtotal-display {
        font-size: 22px;
        font-weight: 700;
    }

    .cart-policy-check {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
        font-size: 15px;
        color: rgba(23, 23, 23, 0.78);
    }

    .cart-policy-check input {
        width: 18px;
        height: 18px;
        accent-color: #171717;
        flex-shrink: 0;
    }

    .cart-policy-check a {
        color: inherit;
        text-decoration: underline;
    }

    .tf-mini-cart-view-checkout {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .cart-cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 58px;
        padding: 14px 22px;
        border-radius: 999px;
        text-decoration: none;
        font-size: 18px;
        font-weight: 600;
        transition: transform 0.25s ease, background-color 0.25s ease, color 0.25s ease;
    }

    .cart-cta:hover {
        transform: translateY(-1px);
    }

    .cart-cta-outline {
        border: 1px solid #171717;
        color: #171717;
        background: transparent;
    }

    .cart-cta-solid {
        border: 1px solid #171717;
        color: #fff;
        background: #171717;
    }

    .cart-continue-link {
        text-align: center;
        color: #171717;
        font-size: 16px;
        font-weight: 500;
        text-decoration: none;
    }

    @media (max-width: 767px) {
        .popup-shopping-cart {
            width: min(100vw, 420px) !important;
            max-width: min(100vw, 420px) !important;
        }

        .cart-drawer-recommendations {
            display: none;
        }

        .cart-drawer-main {
            padding: 20px 16px 18px;
        }

        .cart-drawer-main-header h5 {
            font-size: 28px;
        }

        .cart-threshold {
            padding: 16px;
        }

        .tf-mini-cart-item {
            grid-template-columns: 84px minmax(0, 1fr);
            gap: 12px;
        }

        .tf-mini-cart-info {
            grid-template-columns: 1fr;
        }

        .cart-item-summary {
            align-items: flex-start;
            text-align: left;
        }

        .cart-item-controls {
            align-items: flex-start;
            flex-direction: column;
        }

        .cart-quick-actions,
        .tf-mini-cart-view-checkout {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Shopping Cart -->
<div class="offcanvas offcanvas-end popup-shopping-cart" id="shoppingCart" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="cart-drawer-recommendations" onclick="event.stopPropagation()">
        <div class="cart-drawer-panel-head">
            <h5 class="title">You may also like</h5>
            <button type="button" class="btn-reset cart-drawer-close" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="icon-X2"></i>
            </button>
        </div>
        <div class="cart-recommendations-list" id="cart-recommendations-list">
            <!-- Recommended items injected by JS -->
        </div>
    </div>
    <div class="cart-drawer-main" onclick="event.stopPropagation()">
        <div class="cart-drawer-main-header">
            <h5 class="title">Shopping Cart</h5>
            <button type="button" class="btn-reset cart-drawer-close" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="icon-X2"></i>
            </button>
        </div>
        <div class="cart-threshold">
            <p class="tf-threshold-text" id="shipping-threshold-text">Buy <span class="accent" id="shipping-remaining">₹1,500</span> more to get free shipping</p>
            <div class="tf-threshold-bar">
                <div id="shipping-progress-bar" class="tf-threshold-bar-progress" style="width: 0%;"></div>
            </div>
        </div>
        <div class="tf-mini-cart-wrap list-file-delete wrap-empty_text">
            <div id="cart-empty-state" class="shop-empty_top py-5 text-center d-none">
                <span class="icon"><i class="icon-Handbag fs-60"></i></span>
                <h4 class="text-emp mt-3">Your cart is empty</h4>
                <p class="mt-2 mb-0 cl-text-2">Add a few pieces and this drawer will fill up fast.</p>
                <div class="shop-empty_bot mt-4">
                    <a href="{{ route('home') }}" class="cart-cta cart-cta-solid">Start Shopping</a>
                </div>
            </div>

            <div class="tf-mini-cart-main">
                <div class="tf-mini-cart-sroll">
                    <div class="tf-mini-cart-items" id="cart-items-list">
                        <!-- Items injected by JS -->
                    </div>
                </div>
            </div>
            <div id="cart-footer" class="tf-mini-cart-bottom">
                <div class="tf-mini-cart-total">
                    <div class="tf-mini-cart-total-content">
                        <span class="fw-6">Subtotal</span>
                        <span class="fw-6" id="cart-subtotal-display">₹0</span>
                    </div>
                    
<hr>                    

                </div>
                                                                                                                        
                <div class="tf-mini-cart-view-checkout">
                    <a href="#" class="cart-cta cart-cta-solid">Check Out</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cartDrawer = document.getElementById('shoppingCart');
        const itemsList = document.getElementById('cart-items-list');
        const recommendationsList = document.getElementById('cart-recommendations-list');
        const emptyState = document.getElementById('cart-empty-state');
        const footer = document.getElementById('cart-footer');
        const subtotalDisplay = document.getElementById('cart-subtotal-display');
        const toolbarCounts = document.querySelectorAll('.toolbar-count, .cart-count');
        const shippingRemaining = document.getElementById('shipping-remaining');
        const shippingProgress = document.getElementById('shipping-progress-bar');
        const thresholdText = document.getElementById('shipping-threshold-text');

        const FREE_SHIPPING_THRESHOLD = 1500;

        function formatCurrency(amount) {
            return '₹' + Math.round(amount).toLocaleString('en-IN');
        }

        let isFetching = false;
        window.refreshCartDrawer = function() {
            if (isFetching) return;
            isFetching = true;

            // Fetch Cart Items
            fetch('{{ route('cart.fetch') }}')
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        toolbarCounts.forEach(el => el.textContent = data.cart_count);
                        
                        if (data.items.length === 0) {
                            itemsList.innerHTML = '';
                            emptyState.classList.remove('d-none');
                            footer.classList.add('d-none');
                            updateShippingProgress(0);
                        } else {
                            emptyState.classList.add('d-none');
                            footer.classList.remove('d-none');
                            subtotalDisplay.textContent = formatCurrency(data.total);
                            updateShippingProgress(data.total);
                            
                            itemsList.innerHTML = data.items.map(item => {
                                if (!item.product) return '';
                                const imagePath = item.product.images && item.product.images[0] 
                                    ? '/storage/' + item.product.images[0].image_path 
                                    : '/assets/images/product/product-placeholder.jpg';
                                
                                return `
                                    <div class="tf-mini-cart-item file-delete" data-id="${item.id}">
                                        <div class="tf-mini-cart-image">
                                            <a href="/product/${item.product.slug}">
                                                <img src="${imagePath}" alt="${item.product.name}">
                                            </a>
                                        </div>
                                        <div class="tf-mini-cart-info">
                                            <div class="cart-item-copy">
                                                <a class="title link" href="/product/${item.product.slug}">${item.product.name}</a>
                                                <div class="cart-item-meta">
                                                    <span>Color: <strong>${item.variant?.color ?? 'Standard'}</strong></span>
                                                    <span>Size: <strong>${item.variant?.size ?? 'One Size'}</strong></span>
                                                </div>
                                            </div>
                                            <div class="cart-item-summary">
                                                <div class="remove-cart-item" data-id="${item.id}">Remove</div>
                                                <div class="cart-item-line-total">${item.quantity} x ${formatCurrency(item.price)}</div>
                                            </div>
                                            <div class="cart-item-controls">
                                                <div class="wg-quantity small">
                                                    <span class="btn-quantity minus-btn-cart cs-pointer" data-id="${item.id}">-</span>
                                                    <input type="text" name="number" value="${item.quantity}" readonly>
                                                    <span class="btn-quantity plus-btn-cart cs-pointer" data-id="${item.id}">+</span>
                                                </div>
                                                <div class="price fw-6">${formatCurrency(item.price * item.quantity)}</div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }).join('');
                        }
                    }
                })
                .catch(error => console.error('Error fetching cart:', error))
                .finally(() => { isFetching = false; });

            // Fetch Recommendations
            fetch('{{ route('cart.recommendations') }}')
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        recommendationsList.innerHTML = data.products.map(product => `
                            <a class="cart-recommendation-card" href="/product/${product.slug}">
                                <div class="cart-recommendation-media">
                                        <img src="${product.images && product.images[0] ? '/storage/' + product.images[0].image_path : '/assets/images/product/product-placeholder.jpg'}" alt="${product.name}">
                                </div>
                                <div>
                                    <p class="cart-recommendation-name">${product.name}</p>
                                    <div class="cart-recommendation-price">
                                        <span class="sale">${formatCurrency(product.sale_price ?? product.price)}</span>
                                        ${product.sale_price ? `<span class="compare">${formatCurrency(product.price)}</span>` : ''}
                                    </div>
                                </div>
                            </a>
                        `).join('');
                    }
                })
                .catch(error => console.error('Error fetching recommendations:', error));
        };

        function updateShippingProgress(total) {
            const remaining = Math.max(0, FREE_SHIPPING_THRESHOLD - total);
            const percent = Math.min(100, (total / FREE_SHIPPING_THRESHOLD) * 100);
            
            if (remaining > 0) {
                thresholdText.innerHTML = `Buy <span class="accent">${formatCurrency(remaining)}</span> more to get free shipping`;
                shippingProgress.style.width = percent + '%';
            } else {
                thresholdText.innerHTML = `Free shipping unlocked for this order`;
                shippingProgress.style.width = '100%';
            }
        }

        // Event delegation for cart actions
        itemsList.addEventListener('click', function(e) {
            const target = e.target.closest('.btn-quantity, .remove-cart-item');
            if (!target) return;

            const itemId = target.dataset.id;
            
            if (target.classList.contains('plus-btn-cart')) {
                updateQuantity(itemId, 1);
            } else if (target.classList.contains('minus-btn-cart')) {
                updateQuantity(itemId, -1);
            } else if (target.classList.contains('remove-cart-item')) {
                removeCartItem(itemId);
            }
        });

        function updateQuantity(itemId, change) {
            if (isFetching) return;
            
            const itemEl = document.querySelector(`.tf-mini-cart-item[data-id="${itemId}"]`);
            if (!itemEl) return;
            const input = itemEl.querySelector('input');
            const newQty = parseInt(input.value) + change;
            if (newQty < 1) return;

            isFetching = true;
            fetch('{{ route('cart.update') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ item_id: itemId, quantity: newQty })
            })
            .then(response => {
                if (!response.ok) throw new Error('Update failed');
                return response.json();
            })
            .then(() => {
                isFetching = false;
                refreshCartDrawer();
            })
            .catch(error => {
                console.error('Error updating quantity:', error);
                isFetching = false;
            });
        }

        function removeCartItem(itemId) {
            if (isFetching) return;
            
            isFetching = true;
            fetch('{{ route('cart.remove') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ item_id: itemId })
            })
            .then(response => {
                if (!response.ok) throw new Error('Remove failed');
                return response.json();
            })
            .then(() => {
                isFetching = false;
                refreshCartDrawer();
            })
            .catch(error => {
                console.error('Error removing item:', error);
                isFetching = false;
            });
        }

        // Initial fetch when drawer opens
        cartDrawer.addEventListener('show.bs.offcanvas', refreshCartDrawer);

        // Global listener for Quick Add buttons on product cards
        document.addEventListener('click', function(e) {
            const quickAddBtn = e.target.closest('.btn-quick-add');
            if (!quickAddBtn) return;

            const productId = quickAddBtn.dataset.productId;
            if (!productId) return;

            quickAddBtn.disabled = true;
            quickAddBtn.textContent = 'Adding...';

            fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    refreshCartDrawer();
                    const bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(cartDrawer);
                    bsOffcanvas.show();
                } else {
                    alert(data.message || 'Error adding to cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            })
            .finally(() => {
                quickAddBtn.disabled = false;
                quickAddBtn.textContent = 'Quick Add';
            });
        });

        // Initial sync of counts and items on page load
        refreshCartDrawer();
    });
</script>
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
            <form action="{{ route('search.results') }}" method="GET" class="form-search-nav style-2" id="search-form">
                <fieldset>
                    <input type="text" name="q" id="search-input" placeholder="Searching..." required autocomplete="off">
                </fieldset>
                <button type="submit" class="btn-action">
                    <i class="icon icon-MagnifyingGlass"></i>
                </button>
            </form>
            
            <div id="search-modal-content" class="mt-20">
                <!-- Recent Searches -->
                <div id="recent-searches-container" class="search-section mb-24 d-none">
                    <div class="d-flex align-items-center justify-content-between mb-12">
                        <h6 class="mb-0">Recent Searches</h6>
                        <button type="button" id="clear-recent-searches" class="text-caption-01 text-decoration-underline border-0 bg-transparent p-0">Clear All</button>
                    </div>
                    <div id="recent-searches-list" class="d-flex flex-wrap gap-8">
                        <!-- Keywords injected by JS -->
                    </div>
                </div>

                <!-- Suggestions/Trending -->
                <div id="search-suggestions-container" class="search-section">
                    <h6 class="mb-16" id="suggestions-title">Trending Now</h6>
                    <div id="suggestions-list" class="d-flex flex-column gap-16">
                        <!-- Products injected by JS -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-input');
        const searchForm = document.getElementById('search-form');
        const suggestionsList = document.getElementById('suggestions-list');
        const suggestionsTitle = document.getElementById('suggestions-title');
        const suggestionsContainer = document.getElementById('search-suggestions-container');
        const recentSearchesContainer = document.getElementById('recent-searches-container');
        const recentSearchesList = document.getElementById('recent-searches-list');
        const clearRecentBtn = document.getElementById('clear-recent-searches');

        const STORAGE_KEY = 'recent_searches';
        let debounceTimer;

        // Initialize Recent Searches
        updateRecentSearchesUI();

        // Handle Input for Suggestions
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value.trim();

            if (query.length < 2) {
                suggestionsContainer.classList.add('d-none');
                recentSearchesContainer.classList.toggle('d-none', JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]').length === 0);
                return;
            }

            debounceTimer = setTimeout(() => {
                fetchSuggestions(query);
            }, 300);
        });

        // Save Search on Form Submit
        searchForm.addEventListener('submit', function(e) {
            const query = searchInput.value.trim();
            if (query) {
                saveRecentSearch(query);
            }
        });

        // Clear Recent Searches
        clearRecentBtn.addEventListener('click', function() {
            localStorage.removeItem(STORAGE_KEY);
            updateRecentSearchesUI();
        });

        function saveRecentSearch(query) {
            let searches = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
            searches = searches.filter(s => s.toLowerCase() !== query.toLowerCase());
            searches.unshift(query);
            searches = searches.slice(0, 5); // Keep last 5
            localStorage.setItem(STORAGE_KEY, JSON.stringify(searches));
        }

        function updateRecentSearchesUI() {
            const searches = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
            if (searches.length > 0 && !searchInput.value.trim()) {
                recentSearchesContainer.classList.remove('d-none');
                recentSearchesList.innerHTML = searches.map(query => `
                    <a href="/search?q=${encodeURIComponent(query)}" class="search-tag">${query}</a>
                `).join('');
            } else {
                recentSearchesContainer.classList.add('d-none');
            }
        }

        function fetchSuggestions(query = '') {
            fetch(`/api/search/suggestions?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.type === 'results') {
                        suggestionsContainer.classList.remove('d-none');
                        recentSearchesContainer.classList.add('d-none');
                        suggestionsTitle.textContent = 'Search Suggestions';
                        renderSuggestions(data.products);
                    } else {
                        suggestionsContainer.classList.add('d-none');
                    }
                })
                .catch(error => console.error('Error fetching suggestions:', error));
        }

        function renderSuggestions(products) {
            if (products.length === 0) {
                suggestionsList.innerHTML = '<p class="text-caption-01 cl-text-2">No products found</p>';
                return;
            }

            suggestionsList.innerHTML = products.map(product => `
                <a href="${product.url}" class="search-item d-flex align-items-center gap-12">
                    <div class="search-item-img flex-shrink-0">
                        <img src="${product.image}" alt="${product.name}" width="60" height="80" style="object-fit: cover; border-radius: 8px;">
                    </div>
                    <div class="search-item-info">
                        <h6 class="mb-4 text-capitalize">${product.name}</h6>
                        <div class="d-flex align-items-center gap-8">
                            <span class="fw-semibold">${product.price}</span>
                            ${product.old_price ? `<span class="text-muted text-decoration-line-through text-caption-02">${product.old_price}</span>` : ''}
                        </div>
                    </div>
                </a>
            `).join('');
        }

        // Initialize state on modal open
        document.getElementById('search').addEventListener('shown.bs.modal', function () {
            searchInput.value = '';
            suggestionsContainer.classList.add('d-none');
            updateRecentSearchesUI();
        });
    });
</script>

<style>
    .search-tag {
        display: inline-block;
        padding: 6px 14px;
        background: #f7f4ef;
        border-radius: 99px;
        font-size: 13px;
        color: #111;
        transition: all 0.2s;
    }
    .search-tag:hover {
        background: #111;
        color: #fff;
    }
    .search-item {
        transition: all 0.2s;
        text-decoration: none;
        color: inherit;
    }
    .search-item:hover h6 {
        color: #b58b21;
    }
</style>
<!-- /Search -->
