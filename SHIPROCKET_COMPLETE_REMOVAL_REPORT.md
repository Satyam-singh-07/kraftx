# Shiprocket Complete Removal Report

Generated on: 2026-05-15

## Summary

Shiprocket runtime integration has been removed and replaced with an internal local checkout flow using cart data, local order creation, order items, stock decrement, and COD/manual pending payment.

No Delhivery integration was added.

## Local Checkout Added

- Added `app/Http/Controllers/Public/CheckoutController.php`.
- Replaced the placeholder checkout page with `resources/views/checkout.blade.php`.
- Added `resources/views/checkout-success.blade.php`.
- Added routes:
  - `GET /checkout`
  - `POST /checkout`
  - `GET /checkout/success/{order}`

Checkout behavior:

- Reads the active session/customer cart.
- Supports guest checkout.
- Validates delivery/contact details.
- Creates a local `orders` row.
- Creates local `order_items`.
- Decrements product or variant stock.
- Marks cart as `converted` and clears cart items.
- Uses COD/manual pending payment:
  - `payment_method = COD`
  - `payment_status = pending`
  - `status = processing`

## Files Deleted

- `app/Http/Controllers/Public/ShiprocketCheckoutController.php`
- `app/Http/Controllers/Api/ShiprocketCatalogController.php`
- `app/Http/Controllers/Api/ShiprocketWebhookController.php`
- `app/Services/ShiprocketService.php`
- `app/Observers/ProductObserver.php`
- `app/Observers/ProductVariantObserver.php`
- `app/Observers/ProductImageObserver.php`
- `app/Observers/CollectionObserver.php`
- `SR Checkout Integration Guide for Custom Websites.docx`
- `SR Checkout Integration Guide for Custom Websites.pdf`

## Files Modified

- `.env`
- `.env.example`
- `app/Models/Order.php`
- `app/Providers/AppServiceProvider.php`
- `app/Services/ProductService.php`
- `config/services.php`
- `routes/api.php`
- `routes/console.php`
- `routes/web.php`
- `resources/views/checkout.blade.php`
- `resources/views/components/layout.blade.php`
- `resources/views/components/modals.blade.php`
- `resources/views/public/products/show.blade.php`
- `resources/views/admin/orders/index.blade.php`
- `resources/views/admin/orders/show.blade.php`
- `resources/views/admin/products/create.blade.php`
- `resources/views/admin/products/edit.blade.php`

## Routes Removed

- `POST /api/shiprocket/checkout/token`
- `POST /api/shiprocket/checkout/one-click`
- `GET /checkout/success` provider redirect route
- `GET /api/shiprocket/products`
- `GET /api/shiprocket/products-by-collection`
- `GET /api/shiprocket/collections`
- `POST /api/shiprocket/webhook/order`
- `POST /api/checkout/order-webhook`
- `POST /api/delivery/status-callback`

## Runtime Dependencies Removed

- Removed external checkout CSS/JS from `resources/views/components/layout.blade.php`.
- Removed `window.SRCheckout`.
- Removed `HeadlessCheckout`.
- Removed checkout token generation.
- Removed webhook handling.
- Removed tracking callback handling.
- Removed catalog sync endpoints.
- Removed product/collection/image/variant observer sync.
- Removed `ShiprocketService` injection and explicit sync calls from `ProductService`.
- Removed Shiprocket config from `config/services.php`.
- Removed Shiprocket env keys from `.env` and `.env.example`.

## UI Cleanup

- Product "Buy It Now" now adds the product to the local cart and redirects to local checkout.
- Removed product one-click checkout button.
- Cart drawer checkout now links to local checkout.
- Admin order list no longer displays provider-specific order ID.
- Admin order detail uses provider-neutral checkout wording.
- Admin product create/edit shipping section is now provider-neutral.

## Schema Changes

No database migration was added to drop provider columns.

Reason: existing historical orders may still contain provider IDs, payloads, AWB values, tracking URLs, or status data. Dropping those columns now could destroy support/audit history. Runtime code no longer reads or writes the provider-specific fields.

## Remaining Deprecated Fields

The following columns remain in existing migrations/database schema only and should be treated as deprecated:

- `orders.shiprocket_order_id`
- `orders.fastrr_order_id`
- `orders.shiprocket_order_created_at`
- `orders.shiprocket_tags`
- `orders.shiprocket_payload`

Provider-originated but now potentially reusable/order-logistics fields:

- `orders.platform_order_id`
- `orders.cart_id`
- `orders.checkout_status`
- `orders.source`
- `orders.shipping_plan`
- `orders.rto_prediction`
- `orders.estimated_delivery_date`
- `orders.shipping_address_data`
- `orders.billing_address_data`
- `orders.payments`
- `orders.coupon_codes`
- `orders.discount_detail`
- `orders.awb_code`
- `orders.courier_name`
- `orders.shipment_status`
- `orders.shipment_status_id`
- `orders.shipment_status_updated_at`
- `orders.shipment_track_url`
- `orders.delivered_at`

## Repository Search Result

Runtime search across `app`, `routes`, `config`, active `resources`, env files, and package manifests returned no matches for:

- `shiprocket`
- `Shiprocket`
- `SRCheckout`
- `HeadlessCheckout`
- `fastrr`
- `pickrr`

The only remaining provider-term matches are historical migration filenames/column definitions, retained to preserve historical order data and schema compatibility.

## Validation

Passed:

- `php -l app/Http/Controllers/Public/CheckoutController.php`
- `php -l app/Services/ProductService.php`
- `php -l app/Providers/AppServiceProvider.php`
- `php artisan route:list`
- `php artisan config:clear`
- `php artisan route:clear`
- `php artisan view:clear`

Test result:

- `php artisan test` failed because the test environment could not connect to MySQL.
- Failure occurred in `Tests\Feature\ExampleTest` on `GET /`, at the home page banner query.
- This is an environment/database connectivity issue: `SQLSTATE[HY000] [2002] Unknown error while connecting`.
- `Tests\Unit\ExampleTest` passed.

## Risks

- Checkout currently uses COD/manual pending payment only. No online payment capture is implemented.
- Shipping charge is currently `0`; shipping pricing rules need a future provider-neutral implementation.
- Historical provider columns remain until a separate archival/backfill/drop migration is approved.
- Tracking page is still a static/local page and does not yet have a provider-backed tracking implementation.

## Rollback Instructions

Recommended rollback path:

1. Revert the removal commit(s) with Git.
2. Restore removed env variables only if rolling back to the old provider integration.
3. Re-enable old webhook URLs in the provider dashboard only after the old routes/controllers are restored.
4. Do not run destructive database cleanup unless a backup is available.

No destructive database migration was introduced in this removal, so code rollback should be sufficient for this phase.

## TODOs Before Delhivery Integration

- Introduce provider-neutral shipment tables or columns.
- Add provider-neutral shipment creation service.
- Add provider-neutral tracking/status sync service.
- Add shipping charge calculation rules.
- Add admin shipment controls.
- Add tests for local checkout order creation and stock decrement.
- Decide whether to archive/drop deprecated provider columns in a separate migration.
