# Payment Gateway and Checkout UI Report

Generated on: 2026-05-15

## Summary

Razorpay prepaid payments have been integrated using the official `razorpay/razorpay` PHP SDK. COD remains available. The checkout UI was rebuilt into a cleaner ecommerce checkout with customer details, shipping address, order summary, and payment selection.

Delhivery was not integrated.

## Files Changed

- `composer.json`
- `composer.lock`
- `.env.example`
- `config/services.php`
- `routes/web.php`
- `routes/api.php`
- `app/Models/Order.php`
- `app/Http/Controllers/Public/CheckoutController.php`
- `app/Http/Controllers/Public/PaymentController.php`
- `app/Services/Payments/PaymentGatewayInterface.php`
- `app/Services/Payments/RazorpayService.php`
- `app/Services/Payments/PaymentVerificationService.php`
- `app/Services/Payments/PaymentWebhookService.php`
- `resources/views/checkout.blade.php`
- `resources/views/checkout-payment.blade.php`
- `resources/views/admin/orders/show.blade.php`
- `tests/Feature/CheckoutTest.php`

## Migration Added

- `database/migrations/2026_05_15_000001_add_payment_gateway_fields_to_orders_table.php`

Adds provider-neutral fields:

- `payment_provider`
- `payment_transaction_id`
- `payment_reference`
- `payment_payload`
- `paid_at`

## Payment Flow

```text
Cart
  -> Checkout form
  -> Create local order inside DB transaction
  -> Reserve/decrement stock once
  -> COD:
       status = cod_confirmed
       payment_status = pending
       cart cleared
       success page
  -> Razorpay:
       status = pending_payment
       payment_status = pending
       payment_provider = razorpay
       Razorpay order created via SDK
       customer completes Razorpay checkout
       callback posts Razorpay IDs/signature to server
       server verifies signature via SDK
       order marked paid
       cart cleared
       success page
```

## Webhook Routes

- `POST /api/payments/razorpay/webhook`

Callback verification route:

- `POST /payments/razorpay/{order}/verify`

## Security Protections

- Uses the official Razorpay PHP SDK.
- Razorpay checkout success is not trusted directly.
- Server verifies `razorpay_order_id`, `razorpay_payment_id`, and `razorpay_signature`.
- Webhook verifies `X-Razorpay-Signature` with `RAZORPAY_WEBHOOK_SECRET`.
- Payment metadata is provider-neutral on the order model.
- Webhook handling is idempotent for already-paid orders.
- Payment reference mismatch is logged and rejected.
- Sensitive card/UPI data is never stored.

## Duplicate Prevention Logic

- Checkout form uses a session checkout token.
- Cart row is locked during order creation.
- Existing orders for the same cart are reused instead of creating another order when the cart is no longer active.
- Razorpay webhook processing locks the matched order row.
- Paid orders are not marked paid twice.

## Checkout UI Improvements

- Rebuilt page into a two-column desktop checkout and stacked mobile layout.
- Added card-style sections:
  - Customer Information
  - Shipping Address
  - Payment
  - Sticky Order Summary
- Added inline validation error rendering.
- Added COD and Pay Online payment choices.
- Added disabled submit/loading state to prevent repeated clicks.
- Improved product summary layout with thumbnails, quantities, subtotal, shipping, discount, and total.
- Added dedicated Razorpay payment handoff page.

## Production Safeguards

- Server recalculates totals from cart/order data.
- Frontend totals are ignored.
- Order creation and stock decrement run in a DB transaction.
- If Razorpay order creation fails after stock reservation, reserved stock is released and the cart is reactivated.
- Failed Razorpay webhooks restore reserved stock once and mark the order `payment_failed`.
- Verification failures keep the order retryable instead of blindly marking it paid.
- Checkout errors are logged and return user-friendly messages.

## Required Environment Variables

Set these before enabling prepaid payments:

- `RAZORPAY_KEY`
- `RAZORPAY_SECRET`
- `RAZORPAY_WEBHOOK_SECRET`

## Validation

Passed:

- `composer require razorpay/razorpay`
- `composer show razorpay/razorpay`
- PHP syntax checks for checkout/payment controllers and services
- `php artisan route:list` for checkout/payment routes
- `php artisan config:clear`
- `php artisan route:clear`
- `php artisan view:clear`

Test status:

- `php artisan test` still cannot fully run in this environment because PHP has no SQLite PDO driver and there is no reachable MySQL server.
- Current error: `could not find driver (Connection: sqlite, SQL: PRAGMA foreign_keys = ON;)`
- `PDO::getAvailableDrivers()` returns only `mysql`.
- Install/enable `pdo_sqlite` or provide a reachable test MySQL database to run the new checkout feature tests.

## Remaining Risks

- Online payment requires valid Razorpay credentials and configured webhook secret.
- Refund handling is modeled in statuses/fields but not automated yet.
- Stock reservation happens when the pending payment order is created. Failed webhook releases stock, but abandoned unpaid Razorpay orders may need a scheduled cleanup/release command.
- Shipping remains free/zero until provider-neutral shipping rules are added.

## Next Recommended Step Before Delhivery

Add an unpaid-order cleanup command that expires stale `pending_payment` orders, restores stock, and reactivates or abandons carts. After that, introduce provider-neutral shipment records before adding Delhivery.
