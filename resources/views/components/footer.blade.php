<!-- Footer -->
<footer class="tf-footer footer-s5 type-2 bg-dark">
    <div class="position-relative">
        <div class="br-line fake-class top-0 bg-white_10"></div>
        <div class="br-line fake-class bottom-0 bg-white_10 d-none d-sm-flex"></div>
        <div class="container-full">
            <div class="footer-inner flat-spacing">
                <div class="col-left">
                    <div class="footer-col-block type-white footer-wrap-start">
                        <p class="footer-heading footer-heading-mobile text-white">OUR STORE</p>
                        <div class="tf-collapse-content">
                            <p class="cl-text-3 mb-4">
                                Support Center:
                            </p>
                            <a href="tel:9990010933" class="text-white link h4 fw-medium mb-12">
                                (+91) 9990010933
                            </a>
                            <a href="https://www.google.com/maps?q=600+N+Michigan+Ave+Chicago,+IL+60611+USA"
                                target="_blank" class="cl-text-3 link mb-4">
                                Gaur City Center, Gaur City West, Greater Noida, UP, India, 201308
                            </a>
                            <a href="mailto:thekraftx@gmail.com" class="cl-text-3 link">
                                thekraftx@gmail.com
                            </a>

                            <div class="footer-social-modern-wrap ">
                <div class="footer-social ">
                    <ul class="social-modern-list">
                        <li>
                            <a href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                                <i class="icon icon-FacebookLogo"></i>
                            </a>
                        </li>
                        <li>
                            <a href="https://x.com/" target="_blank" rel="noopener noreferrer" aria-label="X">
                                <i class="icon icon-XLogo"></i>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                                <i class="icon icon-InstagramLogo"></i>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.tiktok.com/" target="_blank" rel="noopener noreferrer" aria-label="TikTok">
                                <i class="icon icon-TiktokLogo"></i>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.snapchat.com/" target="_blank" rel="noopener noreferrer" aria-label="Snapchat">
                                <i class="icon icon-SnapchatLogo"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
                            
                        </div>
                        
                        
                    </div>
                     
                </div>
                <div class="br-line type-vertical"></div>
                <div class="col-center">
                    <div class="footer-link-list">
                        <div class="footer-col-block type-white footer-wrap-2">
                            <p class="footer-heading footer-heading-mobile text-white">QUICK LINKS</p>
                            <div class="tf-collapse-content">
                                <ul class="footer-menu-list">
                                    <li><a href="about.html" class="cl-text-3 link">Return & Refund Policy</a></li>
                                    <li><a href="our-store.html" class="cl-text-3 link">Shipping & Cancellation Policy</a></li>
                                     <li><a href="{{ route('privacy.policy') }}" class="cl-text-3 link">Privacy Policy</a>
                                         <li><a href="{{ route('terms.conditions') }}" class="cl-text-3 link">Terms &
                                            Conditions</a></li>
                                    </li>
                                     <li>
                                        <a href="{{ route('track.order') }}" class="cl-text-3 link">Track Your Order</a>
                                    </li>
                                    <li><a href="{{ route('contact.us') }}" class="cl-text-3 link">Contact us</a></li>
                                 

                                   

                                  
                                </ul>
                            </div>
                        </div>
                       
                      

                         <div class="footer-col-block type-white ">
                            <p class="footer-heading  text-white">MOBILE APPS</p>
                            <div class="">
                                <ul class="footer-menu-list" style="display: flex; flex-direction: row; gap: 8px;">
                                    <li><img loading="lazy" width="150" height="24" src="{{ asset('assets/images/google-play.svg') }}" alt="Google Play"></li>
                                    <li><img loading="lazy" width="150" height="24" src="{{ asset('assets/images/download-on-the-app-store.svg') }}" alt="App Store"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="br-line type-vertical"></div>
                <div class="col-right">
                    <div class="footer-col-block type-white footer-wrap-end">
                        <p class="footer-heading footer-heading-mobile text-white">NEWSLETTER</p>
                        <div class="tf-collapse-content">
                            <p class="footer-desc cl-text-3 mb-16">
                                Subscribe for store updates and discounts.
                            </p>
                            <form id="newsletter-form" class="form-sub mb-16">
                                @csrf
                                <fieldset>
                                    <input type="email" name="email" placeholder="Enter your e-mail" required>
                                </fieldset>
                                <button type="submit" class="btn-action">
                                    <i class="icon icon-ArrowUpRight"></i>
                                </button>
                            </form>
                            <div id="newsletter-message" class="mt-2" style="display: none;"></div>

                            <script>
                                document.getElementById('newsletter-form').addEventListener('submit', function(e) {
                                    e.preventDefault();
                                    const form = this;
                                    const messageDiv = document.getElementById('newsletter-message');
                                    const submitBtn = form.querySelector('button[type="submit"]');
                                    const emailInput = form.querySelector('input[name="email"]');

                                    submitBtn.disabled = true;
                                    messageDiv.style.display = 'none';
                                    messageDiv.className = 'mt-2';

                                    fetch('{{ route('newsletter.store') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            email: emailInput.value
                                        })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        messageDiv.style.display = 'block';
                                        if (data.errors) {
                                            messageDiv.innerText = data.errors.email[0];
                                            messageDiv.classList.add('text-danger');
                                        } else if (data.message) {
                                            messageDiv.innerText = data.message;
                                            messageDiv.classList.add('text-success');
                                            emailInput.value = '';
                                        }
                                    })
                                    .catch(error => {
                                        messageDiv.style.display = 'block';
                                        messageDiv.innerText = 'Something went wrong. Please try again.';
                                        messageDiv.classList.add('text-danger');
                                    })
                                    .finally(() => {
                                        submitBtn.disabled = false;
                                    });
                                });
                            </script>
                            <p class="text-remember cl-text-3">
                                By clicking subcribe, you agree to the
                                <a href="{{ route('terms.conditions') }}" class="text-white link link-underline">
                                    Terms of Service
                                </a>
                                and
                                <a href="{{ route('privacy.policy') }}" class="text-white link link-underline">
                                    Privacy Policy
                                </a>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    <div class="footer-hero-text">
        <div class="container-full">
            <span class="text-white fw-semibold">
                KRAFTX STORE
            </span>
        </div>
    </div>
    
    <div class="footer-bottom position-relative modern-footer-bottom">
        <div class="br-line fake-class top-0 bg-white_10"></div>
        <div class="container-full">
           
            <div class="inner-bottom modern-bottom-shell">
                <div class="bottom-meta">
                    <p class="text-nocopy cl-text-3 mb-0">©2026 KraftX. All Rights Reserved.</p>
                    <p class="text-caption-01 cl-text-3 mb-0">Secure checkout and protected payments</p>
                </div>

               

                <div class="bottom-payments">
                    <span class="text-caption-01 cl-text-3">We accept</span>
                    <ul class="tf-list payment-list modern-payment-list">
                        <li><img loading="lazy" width="38" height="24" src="{{ asset('assets/images/payment/upi.svg') }}" alt="UPI"></li>
                        <li><img loading="lazy" width="38" height="24" src="{{ asset('assets/images/payment/visa.svg') }}" alt="Visa"></li>
                        <li><img loading="lazy" width="38" height="24" src="{{ asset('assets/images/payment/master-card.svg') }}" alt="Mastercard"></li>
                        <li><img loading="lazy" width="38" height="24" src="{{ asset('assets/images/payment/amex.svg') }}" alt="Amex"></li>
                        <li><img loading="lazy" width="38" height="24" src="{{ asset('assets/images/payment/paypal.svg') }}" alt="PayPal"></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <style>
        .modern-footer-bottom {
            padding: 18px 0 22px;
        }

        .footer-social-modern-wrap {
padding-top: 15px;

        }

        .footer-social-modern {
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 16px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.07) 0%, rgba(255, 255, 255, 0.02) 100%);
        }

        .footer-social-modern .social-title {
            font-size: 14px;
            line-height: 20px;
            letter-spacing: 0.02em;
            margin: 0;
        }

        .social-modern-list {
            padding-top: 15px;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .social-modern-list a {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.22);
            background: rgba(255, 255, 255, 0.05);
            transition: all .2s ease;
        }

        .social-modern-list a:hover {
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, 0.45);
            background: rgba(255, 255, 255, 0.16);
        }

        .modern-bottom-shell {
            display: grid;
            grid-template-columns: 1.2fr auto auto;
            gap: 16px;
            align-items: center;
            padding: 16px 20px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 16px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.06) 0%, rgba(255, 255, 255, 0.02) 100%);
            backdrop-filter: blur(4px);
        }

        .bottom-meta {
            display: grid;
            gap: 2px;
        }

        .bottom-links {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            padding: 0;
        }

        .bottom-links .link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 34px;
            padding: 0 14px;
            border-radius: 999px;
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(255, 255, 255, 0.03);
            transition: all .2s ease;
        }

        .bottom-links .link:hover {
            border-color: rgba(255, 255, 255, 0.38);
            background: rgba(255, 255, 255, 0.12);
        }

        .bottom-payments {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modern-payment-list {
            display: flex;
            align-items: center;
            gap: 6px;
            margin: 0;
            padding: 0;
        }

        .modern-payment-list li {
            width: 40px;
            height: 28px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.92);
        }

        @media (max-width: 1199px) {
            .modern-bottom-shell {
                grid-template-columns: 1fr;
                justify-items: center;
                text-align: center;
            }

            .footer-social-modern {
                flex-direction: column;
                align-items: flex-start;
            }

            .social-modern-list {
                padding-top: 15px;
                width: 100%;
                justify-content: flex-start;
                flex-wrap: wrap;
            }
        }
    </style>
</footer>
<!-- /Footer -->
