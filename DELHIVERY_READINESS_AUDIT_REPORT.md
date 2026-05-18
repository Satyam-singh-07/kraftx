# Delhivery Readiness Audit Report

Date: 2026-05-17  
Scope: Architecture audit, readiness verification, gap analysis, and integration planning only. No Delhivery API code or shipment logic has been implemented.

## Sources Reviewed

- Local Laravel codebase: checkout, payment, order, admin order, product, cart, and mail flows.
- Delhivery B2C developer portal entry point: https://one.delhivery.com/developer-portal/documents/b2c/
- Delhivery integration guide: https://delhivery-express-api-doc.readme.io/reference/7-steps-of-integration
- Delhivery package/order creation API: https://delhivery-express-api-doc.readme.io/reference/order-creation-api
- Delhivery bulk waybill API: https://delhivery-express-api-doc.readme.io/reference/bulk-waybill
- Delhivery API FAQ: https://delhivery-express-api-doc.readme.io/reference/frequently-asked-questions
- Delhivery One operational B2C docs:
  - https://help.delhivery.com/docs/create-forward-order
  - https://help.delhivery.com/docs/ship-forward-order

## Readiness Score

Overall readiness: 55/100

The core commerce foundation is strong enough to start designing a Delhivery integration, but it is not ready for API integration yet. Checkout, payment, stock reservation, customer data capture, and product logistics fields mostly exist. The missing layer is a real fulfillment domain: shipments, packages, pickup requests, labels, provider events, webhook idempotency, and admin fulfillment actions.

## Current Architecture Summary

### Checkout Flow

The current checkout is local and no longer depends on Shiprocket runtime code.

- `GET /checkout` creates a checkout token and stores `checkout_cart_id`.
- Checkout supports guest and logged-in users.
- Cart items are loaded with products, variants, and images.
- Frontend and backend validation are hardened for customer data, Indian phone numbers, address quality, pincode, payment method availability, stale checkout sessions, rate limiting, cart integrity, quantity limits, product status, variant validity, stock availability, and server-side total recalculation.
- Duplicate submit handling redirects to the last completed order when the checkout token is no longer valid.

### COD Flow

- COD order is created inside a DB transaction.
- Status becomes `cod_confirmed`.
- `payment_method = COD`.
- `payment_status = pending`.
- Cart is marked `converted`.
- Cart items are deleted.
- Stock is decremented at order creation.
- Confirmation email is sent after order creation.

COD is operationally valid for order placement, but there is no admin review step before fulfillment. For Delhivery this matters because COD orders should not be automatically manifested without review and serviceability checks.

### Prepaid Flow

- Razorpay order is created after local order creation.
- Initial local order status is `pending_payment`.
- `payment_method = Prepaid`.
- `payment_status = pending`.
- `payment_provider = razorpay`.
- Cart is marked `payment_pending`.
- Stock is reserved at local order creation.
- On verified payment callback, `payment_status = paid`, `status = paid`, `paid_at` and payment transaction fields are set.
- Razorpay webhook also confirms captured payments and now checks amount consistency before marking paid.
- Cart is converted and cart items are deleted after successful payment.
- Confirmation email is sent after verified successful payment.

This is safe for payment capture, but fulfillment should not treat every `paid` order as immediately shipment-ready.

## Order Lifecycle Audit

### Current Statuses Found

Runtime statuses:

- `pending_payment`
- `cod_confirmed`
- `paid`
- `payment_failed`

Admin-supported statuses:

- `pending`
- `processing`
- `shipped`
- `delivered`
- `cancelled`

Payment statuses:

- `pending`
- `paid`
- `failed`
- `refunded` is recognized in admin UI styling/comments, but refund flow is not implemented.

Shipment fields:

- `awb_code`
- `courier_name`
- `shipment_status`
- `shipment_status_id`
- `shipment_status_updated_at`
- `shipment_track_url`
- `delivered_at`

### Required Status Support

| State | Current Support | Notes |
|---|---:|---|
| `pending_payment` | Yes | Used for Razorpay orders before payment. |
| `paid` | Yes | Used as both payment-confirmed order status and operational order status. This is overloaded. |
| `cod_confirmed` | Yes | Used for COD checkout completion. |
| `processing` | Partial | Admin can set it manually, but no transition guard exists. |
| `ready_to_ship` | Missing | Needed before Delhivery shipment creation. |
| `shipped` | Partial | Admin can set it manually; no AWB/provider enforcement. |
| `delivered` | Partial | Admin can set it manually; no webhook sync. |
| `cancelled` | Partial | Admin can set it manually; no stock/payment/shipment side effects. |
| `rto` | Missing | Critical for logistics/RTO accounting. |
| `refunded` | Partial payment status only | No refund workflow or order transition logic. |

### Unsafe Transitions

The admin panel can directly change an order to `shipped` or `delivered` without:

- AWB existence
- shipment provider status
- label generation
- pickup completion
- payment checks
- cancellation/refund side effects
- transition history

Recommendation: introduce explicit order state transitions before integration. Do not let Delhivery webhooks write arbitrary order statuses directly.

## Fulfillment Workflow Audit

Current fulfillment is manual and shallow.

Admin can:

- view orders
- filter by basic status
- view customer/payment/order item details
- manually change order status among `pending`, `processing`, `shipped`, `delivered`, `cancelled`

Admin cannot:

- mark an order `ready_to_ship`
- check serviceability
- create shipment
- print/download label
- create pickup request
- cancel shipment
- view shipment timeline
- view webhook/API failures
- retry failed shipment creation
- split shipment/package rows
- capture package weight/dimensions at fulfillment time

Conclusion: fulfillment should be manual/admin-reviewed first. Automatic shipment creation immediately after payment would be risky in this architecture.

## Database Readiness Audit

### Customer Shipping Data

| Field | Existing | Readiness |
|---|---:|---|
| full name | Yes: `orders.customer_name` | Good |
| phone | Yes: `orders.customer_phone` | Good, validation hardened |
| email | Yes: `orders.customer_email` | Good |
| address | Yes: `orders.shipping_address` and `shipping_address_data` | Good |
| city | Yes | Good |
| state | Yes | Good |
| pincode | Yes | Good |
| country | Yes | Defaults to India | Good |

### Shipment Data

| Field | Existing | Readiness |
|---|---:|---|
| shipping provider | Partial: `courier_name` | Too generic; no provider enum/source |
| AWB number | Yes: `awb_code` | Single-shipment only |
| shipment ID | Missing | Required for robust provider reconciliation |
| tracking URL | Yes: `shipment_track_url` | Good but provider-specific generation needed |
| shipment status | Yes: `shipment_status`, `shipment_status_id` | No event history |
| shipped_at | Missing | Needed |
| delivered_at | Yes | Good |
| pickup status | Missing | Needed |
| shipment payload | Legacy `shiprocket_payload` exists | Should not reuse for Delhivery |
| label path/blob | Missing | Needed |
| cancellation state | Missing | Needed |

### Package Data

| Field | Existing | Readiness |
|---|---:|---|
| package rows | Missing | Needed |
| package weight | Product-level only | Need shipment/package-level snapshot |
| package dimensions | Product-level only | Need packed box dimensions |
| volumetric weight | Missing | Needed/calculated |
| multi-piece shipment support | Missing | Needed later if orders can ship in multiple boxes |

### Payment Data

| Field | Existing | Readiness |
|---|---:|---|
| prepaid/COD distinction | Yes: `payment_method` | Good |
| payment status | Yes | Good |
| transaction reference | Yes | Good |
| payment provider payload | Yes | Good |
| invoice value | Derivable from `total_amount` / items | Needs explicit invoice record/reference for high-value compliance |
| COD collect amount | Derivable for COD | Should be explicit in shipment payload snapshot |

### Product Logistics Data

| Field | Existing | Readiness |
|---|---:|---|
| SKU | Product and variant SKUs exist | Good |
| HSN | Product-level `hsn_code` exists | Good, but not enforced |
| weight | Product-level kg exists | Good baseline |
| dimensions | Product-level L/W/H cm exist | Good baseline |
| variant logistics | Missing | Variant has SKU/price/stock only; no variant weight/dimensions/HSN override |

Important gap: `order_items` do not snapshot product weight, dimensions, or HSN at purchase time. If product logistics values change later, historical shipment creation can become inconsistent.

## Delhivery Requirement Mapping

Delhivery’s integration guide identifies the core sequence as pincode serviceability, pickup/warehouse setup, waybill handling, package/order creation, shipping label/packing slip, pickup request, tracking, edit/cancel, and NDR actions.

The package/order creation docs identify key requirements:

- package/order creation API endpoint for staging and production
- `format=json&data=` payload shape
- unique order ID when Delhivery dynamically assigns waybill
- payment mode must be `COD` or `Pre-paid` for forward shipments
- pickup location name must exactly match the registered Delhivery warehouse name and is case-sensitive
- client name must exactly match Delhivery registered client name
- pincode, phone, and address are mandatory
- e-waybill is required if shipment value exceeds INR 50,000
- special payload character handling matters
- single-piece shipment can skip waybill and let Delhivery generate it; MPS requires explicit waybills per box
- serviceability check before order creation is mandatory/recommended in Delhivery FAQ

### Compatibility Matrix

| Delhivery Requirement | Current Field/Capability | Status | Gap |
|---|---|---:|---|
| Consignee name | `orders.customer_name` | Ready | None |
| Consignee phone | `orders.customer_phone` | Ready | Ensure 10-digit normalized phone only |
| Consignee email | `orders.customer_email` | Ready | None |
| Address | `orders.shipping_address` | Ready | Need Delhivery-safe sanitization |
| City/state/pin | `shipping_city/state/pincode` | Ready | Need serviceability check |
| Payment mode COD/Pre-paid | `payment_method` | Ready | Map `Prepaid` to Delhivery `Pre-paid` exactly |
| COD amount | Derive from `total_amount` for COD | Partial | Snapshot `cod_amount` on shipment |
| Invoice value | Derive from order totals | Partial | Need invoice reference and tax/HSN policy |
| Order ID uniqueness | `orders.order_number` unique | Ready | Use stable external shipment reference |
| Product name/SKU | `order_items.name/sku` | Ready | Good |
| HSN code | `products.hsn_code` | Partial | Not snapshotted to order items; may be null |
| Package weight | Product weight exists | Partial | Need package-level final dead weight |
| Package dimensions | Product dimensions exist | Partial | Need packed box L/W/H |
| Volumetric weight | Missing | Missing | Add calculated or persisted package value |
| Pickup location name | Missing settings | Missing | Need admin-configured Delhivery warehouse name |
| Client name | Missing settings | Missing | Need Delhivery client config |
| Waybill | `orders.awb_code` | Partial | Need shipment table and uniqueness/idempotency |
| Label/packing slip | Missing | Missing | Need storage and admin print/download |
| Pickup request | Missing | Missing | Need pickup request model/workflow |
| Tracking pull | Missing | Missing | Need API integration later |
| Tracking push/webhook | Missing | Missing | Need webhook route, signature/auth validation, event table |
| Cancellation API | Missing | Missing | Need guarded cancellation workflow |
| NDR actions | Missing | Missing | Future requirement |
| Serviceability cache | Missing | Missing | Needed before shipment creation; useful at checkout |

## Missing Architecture Before Integration

Required before Delhivery API work:

1. Shipment table
   - `id`, `order_id`, `provider`, `provider_shipment_id`, `awb`, `status`, `status_code`, `tracking_url`, `payment_mode`, `cod_amount`, `invoice_value`, `pickup_location_name`, `serviceability_snapshot`, `created_by`, `created_at`, `shipped_at`, `delivered_at`, `cancelled_at`.

2. Shipment package table
   - `shipment_id`, `box_number`, `weight_kg`, `length_cm`, `width_cm`, `height_cm`, `volumetric_weight_kg`, `awb` for MPS readiness, item mapping if needed.

3. Shipment event table
   - immutable provider events from tracking API/webhooks, with `event_time`, `raw_status`, normalized status, location, payload hash, and idempotency key.

4. API request log table
   - outbound serviceability/create/cancel/label/pickup/tracking calls with sanitized request/response summaries, status code, latency, failure reason, retry count.

5. Pickup request table
   - pickup date/time, pickup location, provider request ID, status, related shipment count.

6. Serviceability cache table
   - pincode, payment mode support if returned, pickup/delivery flags, last checked timestamp, response summary.

7. Label storage
   - path or generated PDF reference, label version, generated_at, printed_at.

8. Delhivery settings
   - environment, token, client name, pickup location name, warehouse address, default package mode, fragile flag default, surface/express mode.

9. Order transition policy
   - explicit allowed transitions and side effects. Avoid free-form admin status changes after integration.

10. Order item logistics snapshots
   - HSN, weight, L/W/H, product/variant SKU at order time.

## What Already Exists and Is Production-Ready

- Local checkout and order creation.
- Guest checkout.
- Email-based OTP account creation and guest order linking.
- Razorpay payment order creation, callback verification, and webhook capture handling.
- Server-side checkout validation and fraud/junk reduction.
- COD/prepaid payment strategy settings.
- Customer shipping address fields.
- Product-level weight/dimensions/HSN fields.
- Basic order admin panel.
- Confirmation email delivery.
- Basic tracking columns on `orders`.

## What Should Not Be Changed

Do not change before Delhivery integration:

- Checkout order creation timing.
- Razorpay verification flow.
- COD/prepaid business rule system.
- Guest checkout architecture.
- Cart stock reservation behavior without a separate inventory reservation redesign.
- Email-based account linking flow.

Instead, add a fulfillment layer beside the existing order flow.

## Recommended Fulfillment Workflow

### Shipment Trigger Timing

Recommended approach: manual admin review first.

Do not create Delhivery shipments automatically immediately after payment or COD checkout. The safest flow for this project is:

1. Order is created.
2. Payment is verified for prepaid, or COD is confirmed.
3. Order appears in admin as fulfillment-eligible.
4. Admin reviews address, items, stock, COD risk, product dimensions, and packaging.
5. Admin runs serviceability check.
6. Admin marks order `ready_to_ship`.
7. Admin creates Delhivery shipment.
8. System stores AWB/shipment record and label.
9. Admin prints label and schedules/adds to pickup.
10. Tracking/webhooks update shipment events and normalized order shipment state.

Reason: current architecture has no package records, no shipment idempotency, no label workflow, no pickup request management, and no transition history. Automatic shipment creation would amplify bad addresses, COD abuse, duplicate shipment risks, and failed packaging assumptions.

### Serviceability Timing

Recommended serviceability stages:

1. Checkout stage: optional soft check after pincode entry to improve UX.
2. Before payment for prepaid: recommended once stable; prevents payment for undeliverable pincodes.
3. Before shipment creation: mandatory hard check. Delhivery FAQ recommends checking serviceability for every shipment before package creation.

Initial integration should enforce serviceability at admin shipment creation even if checkout only displays a soft warning.

### COD Strategy

COD should not be treated as simply another shipping mode.

Recommended COD controls:

- Check Delhivery serviceability specifically for COD, not only prepaid delivery.
- Keep COD admin review mandatory.
- Add COD risk signals: repeated phone, high order value, poor address, previous RTO, suspicious pincode, fake/repeating phone patterns.
- Consider COD max order value and prepaid-only pincodes once serviceability/risk data exists.
- For Delhivery shipment payload, COD orders should carry explicit collect amount equal to the amount to collect, not a loose derivation at API time.

## API Integration Priority

### 1. Pincode Serviceability API

Purpose: validate whether destination pincode is serviceable before shipment creation.  
Business impact: prevents NSZ/non-serviceable shipments and wasted operations.  
Dependencies: Delhivery token/client configuration, serviceability cache table.  
Required data: delivery pincode, pickup/client context, payment mode if API supports COD/prepaid distinction.  
Risk level: Low. Read-only, safe first integration.

### 2. Warehouse/Pickup Location Configuration

Purpose: ensure the exact Delhivery pickup location name and client name are known and configured.  
Business impact: package creation fails if pickup location/client names mismatch.  
Dependencies: admin settings and Delhivery onboarding.  
Required data: client name, pickup location name, warehouse address/pincode/contact.  
Risk level: Medium. Misconfiguration blocks all shipments.

### 3. Shipment Creation / Manifestation API

Purpose: create shipment soft data and obtain/consume AWB.  
Business impact: core shipping capability.  
Dependencies: shipment tables, package dimensions, serviceability pass, order state guard, idempotency key.  
Required data: order, consignee, item details, invoice value, payment mode, COD amount, package weight/dimensions, pickup location, client name, HSN/GST if required.  
Risk level: High. Creates provider-side state and duplicate risks.

### 4. Packing Slip / Label API

Purpose: generate label/packing slip data for printing.  
Business impact: warehouse execution.  
Dependencies: successful shipment creation and AWB.  
Required data: AWB/order reference.  
Risk level: Medium. Operational blocker if missing.

### 5. Pickup Request API

Purpose: schedule pickup or add ready shipments to pickup.  
Business impact: required for handover unless daily pickup is configured manually.  
Dependencies: created shipment, pickup request table, pickup location.  
Required data: pickup location, pickup date/time/window, shipment count.  
Risk level: Medium. Operational timing risk.

### 6. Tracking Pull API

Purpose: reconcile current status by AWB.  
Business impact: customer/admin visibility and recovery from missed webhooks.  
Dependencies: AWB stored, shipment event table.  
Required data: AWB, up to documented batch limit.  
Risk level: Low-medium.

### 7. Tracking Push / Webhook API

Purpose: receive scan events from Delhivery.  
Business impact: near real-time status updates.  
Dependencies: webhook endpoint, authentication/verification strategy, event idempotency, normalized status mapper.  
Required data: provider webhook payload and AWB mapping.  
Risk level: High because duplicate/out-of-order events are common.

### 8. Cancellation API

Purpose: cancel shipment before an irreversible provider state.  
Business impact: support/admin correction flow.  
Dependencies: transition rules, shipment state awareness, refund/stock policy.  
Required data: AWB/order reference.  
Risk level: High if used after pickup/dispatch without guardrails.

### 9. NDR API

Purpose: manage non-delivery actions, reattempts, address/phone corrections.  
Business impact: RTO reduction.  
Dependencies: mature tracking and support workflow.  
Required data: AWB, NDR reason/action, corrected details.  
Risk level: Medium-high. Integrate after baseline shipping is stable.

## Risk Analysis

### Architectural Risks

- Orders currently hold shipment fields directly; this does not scale to multiple shipments, retries, event history, or provider changes.
- `status = paid` is both a payment milestone and order lifecycle state.
- Admin status changes are unguarded.

Mitigation: add shipment domain tables and state transition policy.

### Operational Risks

- No package-level weight/dimensions means labels and charges can be wrong.
- No label storage/print flow means warehouse execution is manual/incomplete.
- No pickup request workflow means shipments may be created but not handed over.

Mitigation: implement package capture and admin fulfillment screens before create-shipment API.

### RTO Risks

- COD orders can be created without logistics serviceability or COD-risk scoring.
- Address quality is improved but no serviceability or NDR feedback loop exists.

Mitigation: COD admin review, serviceability check, RTO history tracking, and later NDR integration.

### Bad Address Risks

- Delhivery payload may reject special characters or bad address formatting.
- Existing address is one text field plus city/state/pin; no landmark field.

Mitigation: sanitize provider payload, keep raw customer address, add optional landmark/address line split later.

### Shipment Duplication Risks

- Duplicate checkout protection exists, but no shipment idempotency exists.
- Retrying shipment creation without a shipment record can create duplicate provider orders/AWBs.

Mitigation: create shipment row before outbound API with idempotency status and unique `order_id + provider + attempt/reference` rules.

### Payment/Shipment Mismatch Risks

- Prepaid orders should ship only after verified `payment_status = paid`.
- COD amount must match final order total intended for collection.

Mitigation: shipment eligibility query must require `payment_status = paid` for prepaid or `status = cod_confirmed` for COD; snapshot COD amount.

### Webhook Synchronization Risks

- Webhooks can be duplicate, late, out-of-order, or missing.
- Directly updating order status from webhook could regress state.

Mitigation: store immutable shipment events, normalize statuses, update current shipment state only through guarded state machine, run periodic tracking reconciliation.

## Logging and Observability Plan

Log without sensitive data:

- Serviceability request start/end: pincode, payment mode, provider, result, latency.
- Shipment create attempt: order ID, local shipment ID, provider, package count, payment mode, not full address or token.
- Shipment create success: local shipment ID, AWB, provider shipment ID.
- Shipment create failure: local shipment ID, provider error code/message summary, retryable flag.
- Label generation: shipment ID, AWB, generated/failed, storage path hash or ID.
- Pickup request: pickup ID, shipment count, requested slot/date, provider status.
- Tracking pull: AWB count, success/failure count, latency.
- Webhook received: event ID/hash, AWB, provider status, event time, duplicate flag.
- Status transition: previous state, next state, actor/source.
- Cancellation attempts and results.

Do not log:

- API tokens
- full customer address
- full phone/email when not needed
- payment signatures or raw payment payloads
- full provider payloads unless encrypted/redacted and access-controlled

## Admin Workflow Gap Analysis

Current admin support:

- List orders.
- Filter by basic order status.
- View order detail.
- View customer shipping/contact data.
- View payment fields.
- Manually set basic order status.

Missing admin capabilities:

- Fulfillment readiness badge.
- Serviceability check action/result.
- Mark `ready_to_ship`.
- Capture/edit package box weight/dimensions before shipment.
- Create Delhivery shipment.
- Retry failed shipment creation.
- View AWB and provider shipment ID.
- Print/download label.
- Schedule/create pickup request.
- Cancel shipment.
- View tracking timeline.
- View webhook/API failure history.
- View COD amount and prepaid/COD logistics risk.
- Restrict unsafe manual transitions.

## Recommended DB Changes Before API Integration

Minimum recommended migrations before Delhivery service code:

1. `shipments`
2. `shipment_packages`
3. `shipment_events`
4. `shipment_api_logs`
5. `pickup_requests`
6. `pickup_request_shipments` if one pickup can contain many shipments
7. `serviceability_checks` or `pincode_serviceability_cache`
8. `logistics_settings` or extend store settings with Delhivery-specific encrypted config
9. `order_items` logistics snapshot columns:
   - `weight`
   - `length`
   - `width`
   - `height`
   - `hsn_code`
10. optional `order_status_histories`

Keep the existing `orders.awb_code` fields for backward compatibility/display only or backfill from the primary shipment later. Do not build the new integration directly on legacy Shiprocket-named payload columns.

## Recommended Integration Order

1. Add fulfillment schema and admin UI skeleton.
2. Add Delhivery settings page with encrypted token/client/pickup configuration.
3. Add serviceability check API.
4. Add shipment eligibility/state machine.
5. Add manual package capture and `ready_to_ship`.
6. Add shipment creation API with idempotency.
7. Add label/packing slip retrieval.
8. Add pickup request workflow.
9. Add tracking pull reconciliation.
10. Add webhook ingestion and event timeline.
11. Add cancellation API.
12. Add NDR actions after baseline shipping is stable.

## Must Be Completed Before API Integration

- Create shipment/package/event/log tables.
- Add Delhivery settings with encrypted token and exact client/pickup names.
- Add serviceability cache/check workflow.
- Add admin fulfillment actions and guarded status transitions.
- Add package-level data capture.
- Add shipment idempotency design.
- Add webhook event idempotency design.
- Define normalized shipment statuses and mapping to order statuses.
- Decide COD shipment policy and max-risk restrictions.
- Snapshot order item logistics fields.

## Final Recommendation

Proceed with Delhivery integration only after the fulfillment layer is added. The current checkout/payment/order foundation is usable, but direct API integration now would couple Delhivery state to the `orders` table, make duplicate shipments likely during retries, and leave admins without the controls needed to handle labels, pickups, cancellations, RTO, and webhook mismatches.

The safest next phase is not API integration. It is fulfillment infrastructure: schema, admin workflow, state machine, serviceability storage, and observability.
