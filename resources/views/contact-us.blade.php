<x-layout :seo="$seo" title="KraftX - Contact Us">
    <x-slot name="styles">
        <style>
            .contact-hero-modern {
                padding: 72px 0 42px;
                background:
                    radial-gradient(100% 130% at 0% 0%, rgba(181, 139, 33, 0.2) 0%, rgba(255, 255, 255, 0) 60%),
                    radial-gradient(120% 130% at 100% 0%, rgba(23, 23, 23, 0.08) 0%, rgba(255, 255, 255, 0) 70%);
            }
            .contact-modern-grid {
                display: grid;
                grid-template-columns: 1fr 1.1fr;
                gap: 24px;
            }
            .contact-modern-card {
                border: 1px solid #ececec;
                border-radius: 20px;
                background: #fff;
                padding: 24px;
                box-shadow: 0 14px 40px rgba(17, 17, 17, 0.06);
            }
            .contact-info-list {
                margin: 0;
                padding: 0;
                list-style: none;
                display: grid;
                gap: 14px;
            }
            .contact-info-list a {
                color: #171717;
                text-decoration: none;
            }
            .contact-pill {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                line-height: 1;
                border-radius: 999px;
                border: 1px solid #e8e8e8;
                background: #fafafa;
                padding: 7px 12px;
            }
            .contact-map-modern {
                border: 0;
                width: 100%;
                min-height: 300px;
                border-radius: 16px;
            }
            @media (max-width: 991px) {
                .contact-modern-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </x-slot>

    @php
        $supportPhone = config('seo.support_phone');
        $supportEmail = config('seo.support_email');
        $supportAddress = config('seo.address');
    @endphp
    <section class="contact-hero-modern">
        <div class="container">
            <div class="main-page-title text-center">
                <div class="breadcrumbs justify-content-center d-flex align-items-center">
                    <a href="{{ route('home') }}" class="text-caption-01 cl-text-3 link">Home</a>
                    <i class="icon icon-CaretRightThin cl-text-3"></i>
                    <p class="text-caption-01 mb-0">Contact Us</p>
                </div>
                <h3 class="mt-12 mb-10">Let’s Talk</h3>
                <p class="cl-text-2 mb-0">Need help with orders, shipping, or product support? Our team is ready.</p>
            </div>
        </div>
    </section>

    <section class="flat-spacing pt-0">
        <div class="container">
            <div class="contact-modern-grid">
                <div class="contact-modern-card">
                    <div class="d-flex align-items-center justify-content-between mb-16">
                        <h5 class="mb-0">Contact Details</h5>
                        <span class="contact-pill">Support 10 AM - 7 PM</span>
                    </div>
                    <ul class="contact-info-list">
                        <li>
                            <p class="text-caption-01 cl-text-2 mb-4">Phone</p>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $supportPhone) }}" class="h6 fw-medium">{{ $supportPhone }}</a>
                        </li>
                        <li>
                            <p class="text-caption-01 cl-text-2 mb-4">Email</p>
                            <a href="mailto:{{ $supportEmail }}" class="h6 fw-medium">{{ $supportEmail }}</a>
                        </li>
                        <li>
                            <p class="text-caption-01 cl-text-2 mb-4">Address</p>
                            <a href="https://www.google.com/maps?q={{ urlencode($supportAddress) }}" target="_blank" rel="noopener noreferrer">
                                {{ $supportAddress }}
                            </a>
                        </li>
                    </ul>
                    <div class="mt-20">
                        <iframe class="contact-map-modern" loading="lazy"
                            src="https://www.google.com/maps?q={{ urlencode($supportAddress) }}&output=embed"
                            referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                    </div>
                </div>

                <div class="contact-modern-card">
                    <h5 class="mb-6">Send a Message</h5>
                    <p class="cl-text-2 mb-16">We usually respond within 24 hours.</p>

                    @if(session('success'))
                        <div class="alert alert-success mb-16">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger mb-16">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form class="form-log" action="{{ route('contact.us.store') }}" method="POST">
                        @csrf
                        <div class="form-content">
                            <fieldset class="tf-field">
                                <label for="contact_name" class="tf-lable fw-medium">Your Name <span class="text-primary">*</span></label>
                                <input id="contact_name" name="name" type="text" value="{{ old('name') }}" placeholder="Enter your full name" required>
                            </fieldset>
                            <fieldset class="tf-field">
                                <label for="contact_email" class="tf-lable fw-medium">Email Address <span class="text-primary">*</span></label>
                                <input id="contact_email" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com" required>
                            </fieldset>
                            <fieldset class="tf-field">
                                <label for="contact_phone" class="tf-lable fw-medium">Phone Number</label>
                                <input id="contact_phone" name="phone" type="text" value="{{ old('phone') }}" placeholder="+91 XXXXXXXXXX">
                            </fieldset>
                            <fieldset class="tf-field d-flex flex-column">
                                <label for="contact_message" class="tf-lable fw-medium">Message <span class="text-primary">*</span></label>
                                <textarea id="contact_message" name="message" rows="6" placeholder="How can we help you?" required>{{ old('message') }}</textarea>
                            </fieldset>
                        </div>
                        <div class="group-action mt-12">
                            <button type="submit" class="tf-btn animate-btn">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-layout>
