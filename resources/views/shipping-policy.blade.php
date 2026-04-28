<x-layout :seo="$seo" title="KraftX - Shipping & Cancellation Policy">


  <style>


    ul, li {
    margin-bottom: 0;
    padding-left: 0;
    list-style: disc !important;
}
        </style>

    <section class="section-page-title text-center flat-spacing-2 pb-0">
        <div class="container">
            <div class="main-page-title">
                <div class="breadcrumbs">
                    <a href="{{ route('home') }}" class="text-caption-01 cl-text-3 link">Home</a>
                    <i class="icon icon-CaretRightThin cl-text-3"></i>
                    <p class="text-caption-01">Shipping &amp; Cancellation Policy</p>
                </div>
                <h3>Shipping &amp; Cancellation Policy</h3>
            </div>
        </div>
    </section>

    <section class="section-term-user flat-spacing">
        <div class="container">
            <div class="content">
                <div class="term-item">
                    <h5 class="term-title">KraftX - Shipping &amp; Cancellation Policy</h5>
                </div>

                <div class="term-item">
                    <h5 class="term-title">Shipping Policy</h5>
                    <p class="term-text cl-text-2">At KraftX, we aim to deliver your orders quickly and safely.</p>
                </div>

                <div class="term-item">
                    <h5 class="term-title">Order Processing</h5>
                    <ul class="term-text cl-text-2 ps-3">
                        <li>Orders are usually processed within 1-2 business days</li>
                        <li>During high-demand periods, processing may take a few additional days</li>
                    </ul>
                </div>

                <div class="term-item">
                    <h5 class="term-title">Shipping &amp; Delivery</h5>
                    <ul class="term-text cl-text-2 ps-3">
                        <li>Once your order is shipped, you will receive a confirmation email with tracking details</li>
                        <li>You can track your shipment using the provided link</li>
                    </ul>
                    <p class="term-text cl-text-2 mt-3">We offer:</p>
                    <ul class="term-text cl-text-2 ps-3">
                        <li>Standard Shipping</li>
                        <li>Express Shipping</li>
                    </ul>
                    <p class="term-text cl-text-2 mt-3">Shipping charges and delivery timelines are calculated at checkout based on your location and selected shipping method.</p>
                </div>

                <div class="term-item">
                    <h5 class="term-title">Important Notes</h5>
                    <ul class="term-text cl-text-2 ps-3">
                        <li>Please ensure your shipping address is accurate and complete</li>
                        <li>KraftX is not responsible for orders delivered to incorrect addresses provided by the customer</li>
                    </ul>
                    <p class="term-text cl-text-2 mt-3">Delivery timelines are estimates and may vary due to:</p>
                    <ul class="term-text cl-text-2 ps-3">
                        <li>Weather conditions</li>
                        <li>Courier delays</li>
                        <li>Unexpected logistics issues</li>
                    </ul>
                </div>

                <div class="term-item">
                    <h5 class="term-title">International Shipping</h5>
                    <ul class="term-text cl-text-2 ps-3">
                        <li>We offer international shipping to selected locations</li>
                        <li>Shipping costs and delivery time vary depending on the destination</li>
                    </ul>
                </div>

                <div class="term-item">
                    <h5 class="term-title">Cancellation Policy</h5>
                    <ul class="term-text cl-text-2 ps-3">
                        <li>Orders can be cancelled within 24 hours of placing the order</li>
                        <li>To cancel, contact us at:</li>
                    </ul>
                    <p class="term-text cl-text-2 mb-1">Email: <a href="mailto:{{ config('seo.support_email') }}" class="link fw-medium">{{ config('seo.support_email') }}</a></p>
                    <p class="term-text cl-text-2">Once the order is processed or shipped, cancellation is not possible</p>
                </div>

                <div class="term-item">
                    <h5 class="term-title">Non-Cancellable Orders</h5>
                    <ul class="term-text cl-text-2 ps-3">
                        <li>Custom or personalized products cannot be cancelled once production has started</li>
                        <li>This includes custom-made items, sculptures, or made-to-order designs</li>
                    </ul>
                </div>

                <div class="term-item">
                    <h5 class="term-title">Need Help?</h5>
                    <p class="term-text cl-text-2">For any queries related to shipping or cancellation, feel free to contact our support team.</p>
                </div>
            </div>
        </div>
    </section>
</x-layout>
