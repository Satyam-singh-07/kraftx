📄 KRAFTX E-Commerce Website – Developer README
🧭 Project Overview

Brand Name: KRAFTX
Business Type: E-commerce (Religious Idols + Handicrafts + Spiritual Gifts)
Primary Market: India (Phase 1), Global (Phase 2)

🎯 Website Goal

Build a conversion-focused e-commerce website that:

Generates direct sales
Collects customer data
Supports marketing & retargeting
Builds a premium spiritual lifestyle brand

This is NOT just a product website — it must be built as a brand + sales funnel.

👥 Target Customers

Primary buyers:

Women age 25–45
Married couples / homeowners
Gift buyers (weddings, housewarming, festivals)
Spiritual & home decor audience

Key buying motivations:

Faith & positivity
Premium gifting
Home temple decoration
Festivals
🛍️ Product Categories (Initial)

Developer must build scalable category system.

Core Categories
God Idols
Shiv Ji
Ganpati Ji
Ram Ji
Bajrangbali
Lakshmi Ganesh
Radha Krishna
Saraswati
Home Temple Decor
Diyas
Incense holders
Pooja accessories
Gift Collections
Housewarming gifts
Wedding gifts
Festival gift boxes
Future Category (Phase 2)
Handicrafts
Home decor items
🧱 TECH STACK (Recommended)

Developer can choose but must support:

Shopify / WooCommerce / Next.js + Headless CMS
Razorpay + COD integration
Meta Pixel + Google Analytics integration
Fast mobile performance

⚠️ Mobile-first design is mandatory (80% traffic mobile).

🎯 CORE WEBSITE GOAL: CONVERSION

Every page must push users toward Add to Cart → Checkout.

🏠 REQUIRED WEBSITE PAGES

1. Homepage (MOST IMPORTANT PAGE)

Sections required:

Hero banner
Tagline: “Modern Spiritual Decor for Your Home”
Featured products
Festival banner support
Shop by Category
Best Sellers
Gift Section Highlight
Why Choose KRAFTX
Customer Reviews
Instagram Feed Integration
Email/WhatsApp Signup 2) Category Pages

Must include:

Filters (price, deity, size, material)
Sorting (price, popularity, newest)
Quick add-to-cart option 3) Product Page (HIGH CONVERSION PAGE)

Each product page must include:

Product Media
High-quality images
Short product video support
Conversion Elements
Product title
Price
Discount badge
COD available badge
Stock urgency message:
“Only few left in stock”
Delivery estimate
Trust Builders
Customer reviews
FAQ accordion
Return & replacement policy
Upsell Section
“Frequently bought together”
“You may also like” 4) Gift Section Page

Special page dedicated to:

Wedding gifts
Housewarming gifts
Festival gifts
Corporate gifting

This page is critical for conversions.

5. About Us Page

Must include:

Brand story
Mission
Handcrafted positioning
Emotional connection with spirituality 6) Contact Page

Include:

WhatsApp button
Email form
Social links
🛒 CHECKOUT REQUIREMENTS

Checkout must be:

Simple
Fast
Mobile optimized
Payment options:
Razorpay
UPI
Cards
Net banking
Cash on Delivery (MANDATORY)
📦 SHIPPING & OFFERS LOGIC

Implement:

Free shipping above ₹999
COD available badge
Delivery estimate on product page
📈 MARKETING & TRACKING (CRITICAL)

Developer MUST integrate:

Meta Pixel

For:

Retargeting ads
Conversion tracking
Google Analytics 4

For:

Traffic tracking
Conversion tracking
Google Search Console
💬 LEAD CAPTURE SYSTEM

Must implement:

Popup system:

Capture:

Email
Phone number

Offer:
“Get ₹100 OFF on first order”

This is critical for remarketing.

📱 WHATSAPP INTEGRATION

Floating WhatsApp button:

“Chat with us”
“Order assistance”
⭐ REVIEW SYSTEM

Customers must be able to:

Leave reviews
Upload photos

Reviews increase trust and conversions.

🎁 UPSELL & CROSS-SELL FEATURES

Must include:

Bundle product support
“Buy together & save” section
Related products carousel
⚡ PERFORMANCE REQUIREMENTS

Page load speed:

Under 3 seconds
Optimized images
Lazy loading
🔍 SEO REQUIREMENTS

Must support:

SEO friendly URLs
Meta title/description editing
Schema markup
Blog section (future marketing)
🧠 FUTURE FEATURES (Phase 2)

Prepare architecture for:

International shipping
Multi-currency
Corporate bulk orders
Subscription gifting
Mobile app integration
🎯 SUCCESS METRICS (KPIs)

Developer should optimize for:

Conversion rate: 2–3%
Add to cart rate: 6–8%
Page load speed < 3s
Mobile usability score high
❤️ FINAL NOTE FOR DEVELOPER

This website must be built as:

A brand experience
A sales funnel
A marketing engine

Not just a simple product catalogue.

Focus on:
👉 Trust
👉 Speed

## Image Optimization Commands

The project converts existing uploaded images to WebP through Laravel Artisan commands. Run these commands from the project root:

```bash
php artisan images:optimize-products
php artisan images:optimize-collections
php artisan images:optimize-banners
```

### What Each Command Does

`php artisan images:optimize-products`

- Reads existing product image records from the database.
- Creates WebP derivatives for each product image: `thumb.webp`, `medium.webp`, and `zoom.webp`.
- Keeps the product image record connected to the optimized image set.
- Processes images in batches of 100 to reduce memory usage.
- Prints how many images were optimized and skipped.

`php artisan images:optimize-collections`

- Reads existing collection images.
- Creates a 420px WebP thumbnail for each collection image.
- Updates the collection image path to the optimized file.
- Processes records in batches of 100 and prints optimized/skipped totals.

`php artisan images:optimize-banners`

- Reads existing banner images.
- Creates desktop and mobile WebP versions using the banner optimizer.
- Updates the banner paths to the optimized files.
- Processes records in batches of 100 and prints optimized/skipped totals.

### Recommended Server Sequence

Back up the database and storage files before converting existing production images. Then run only the command for the image type you need:

```bash
cd /var/www/kraftx
php artisan images:optimize-products
php artisan images:optimize-collections
php artisan images:optimize-banners
php artisan optimize:clear
```

The commands are safe to run again: images that are already optimized are skipped by the optimizer. They do not upload new images and they do not delete the original source files unless the optimizer's cleanup rules explicitly allow it.
👉 Mobile UX
👉 Conversion optimization
