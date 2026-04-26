<x-layout title="Track Your Order - TheKraftX">
    <!-- Page Title -->
    <section class="section-page-title text-center flat-spacing-2 bg-main-2">
        <div class="container">
            <div class="main-page-title">
                <div class="breadcrumbs">
                    <a href="{{ route('home') }}" class="text-caption-01 cl-text-3 link">Home</a>
                    <i class="icon icon-CaretRightThin cl-text-3"></i>
                    <p class="text-caption-01">Order Tracking</p>
                </div>
                <h3>Track Your Order</h3>
                <p class="text-body-1 cl-text-2">
                    Enter your details below to see the status of your purchase.
                </p>
            </div>
        </div>
    </section>
    <!-- /Page Title -->

    <!-- Order Tracking -->
    <div class="flat-spacing pt-0">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="tracking-modern-container">
                        <div class="row g-0">
                            <!-- Form Column -->
                            <div class="col-md-5">
                                <div class="tracking-form-wrap">
                                    <div class="mb-32">
                                        <h4 class="mb-8">Order Details</h4>
                                        <p class="cl-text-3">Please enter the required information from your receipt.</p>
                                    </div>
                                    <form id="track-order-form" class="form-tracking modern-form">
                                        @csrf
                                        <div class="form-content">
                                            <fieldset class="tf-field mb-20">
                                                <label class="tf-lable mb-8 fw-medium">Order ID <span class="text-danger">*</span></label>
                                                <input type="text" name="order_id" placeholder="e.g. #ORD-12345" required>
                                            </fieldset>
                                            <fieldset class="tf-field mb-24">
                                                <label class="tf-lable mb-8 fw-medium">Billing Email <span class="text-danger">*</span></label>
                                                <input type="email" name="email" placeholder="email@example.com" required>
                                            </fieldset>
                                        </div>
                                        <button type="submit" class="tf-btn animate-btn w-100 btn-dark">
                                            Track Now
                                            <i class="icon icon-ArrowUpRight ms-2"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <!-- Illustration/Info Column -->
                            <div class="col-md-7 d-none d-md-block">
                                <div class="tracking-info-visual">
                                    <div class="visual-content">
                                        <div class="icon-box mb-24">
                                            <i class="icon icon-Package fs-60 text-white"></i>
                                        </div>
                                        <h3 class="text-white mb-16">Quick Tracking</h3>
                                        <p class="text-white_80">Stay updated on your shipment's journey from our warehouse to your doorstep.</p>
                                        
                                        <div class="tracking-steps mt-40">
                                            <div class="step-item active">
                                                <div class="step-icon">1</div>
                                                <div class="step-text">Enter Order ID</div>
                                            </div>
                                            <div class="step-line"></div>
                                            <div class="step-item">
                                                <div class="step-icon">2</div>
                                                <div class="step-text">Verify Email</div>
                                            </div>
                                            <div class="step-line"></div>
                                            <div class="step-item">
                                                <div class="step-icon">3</div>
                                                <div class="step-text">Real-time Status</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Results Section (Hidden initially) -->
                        <div id="tracking-results" class="tracking-results-wrap" style="display: none;">
                            <hr class="my-40">
                            <div class="results-content text-center py-4">
                                <div class="spinner-border text-primary mb-16" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p>Checking order status...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Order Tracking -->

    <style>
        .tracking-modern-container {
            background: #fff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
            margin-top: -40px;
            position: relative;
            z-index: 10;
        }

        .tracking-form-wrap {
            padding: 48px;
        }

        .tracking-info-visual {
            background: linear-gradient(135deg, #171717 0%, #333 100%);
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px;
            position: relative;
        }

        .tracking-info-visual::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image: url("{{ asset('assets/images/section/banner-newsletter.jpg') }}");
            background-size: cover;
            background-position: center;
            opacity: 0.1;
        }

        .visual-content {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 400px;
        }

        .text-white_80 {
            color: rgba(255,255,255,0.8);
        }

        .tracking-steps {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .step-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
        }

        .step-item.active .step-icon {
            background: #fff;
            color: #171717;
            border-color: #fff;
        }

        .step-text {
            font-size: 12px;
            color: rgba(255,255,255,0.6);
            font-weight: 500;
            white-space: nowrap;
        }

        .step-line {
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.1);
            margin-bottom: 24px;
            min-width: 20px;
        }

        .modern-form .tf-field input {
            height: 54px;
            border-radius: 12px;
            border: 1px solid rgba(23, 23, 23, 0.1);
            padding: 0 20px;
            background: #f9f9f9;
            transition: all 0.25s ease;
        }

        .modern-form .tf-field input:focus {
            background: #fff;
            border-color: #171717;
            box-shadow: 0 0 0 4px rgba(23, 23, 23, 0.05);
        }

        .tracking-results-wrap {
            padding: 0 48px 48px;
        }

        @media (max-width: 767px) {
            .tracking-form-wrap {
                padding: 32px 24px;
            }
            .tracking-modern-container {
                margin-top: 0;
                border-radius: 16px;
            }
        }
    </style>

    <script>
        document.getElementById('track-order-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const resultsDiv = document.getElementById('tracking-results');
            resultsDiv.style.display = 'block';
            resultsDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Simulation for demo
            setTimeout(() => {
                resultsDiv.querySelector('.results-content').innerHTML = `
                    <div class="p-24 bg-light rounded-xl border">
                        <i class="icon icon-WarningCircle fs-40 text-warning mb-16"></i>
                        <h5 class="mb-8">Order Not Found</h5>
                        <p class="cl-text-3 mb-0">We couldn't find an order matching these details. Please check your Order ID and email address and try again.</p>
                    </div>
                `;
            }, 1500);
        });
    </script>
</x-layout>
