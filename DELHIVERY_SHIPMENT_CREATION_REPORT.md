# Delhivery Shipment Creation Report

## Scope Implemented

- Manual admin-triggered shipment draft preparation.
- Admin package preparation workflow with editable packed weight and dimensions.
- Delhivery B2C shipment creation through `api/cmu/create.json`.
- AWB/provider shipment ID storage in the shipment domain.
- Delhivery label retrieval through `api/p/packing_slip`.
- Label storage as a local file or provider URL, depending on Delhivery response.
- Centralized shipment status transition rules.
- Idempotent shipment creation attempts using `shipment_attempts`.

## Shipment Workflow

Internal normalized shipment statuses:

- `draft`
- `ready_to_ship`
- `shipment_creating`
- `shipment_created`
- `label_generated`
- `pickup_pending`
- `picked_up`
- `shipped`
- `in_transit`
- `delivered`
- `rto`
- `cancelled`
- `failed`

Shipment creation remains manual and admin-controlled:

1. Admin opens an order.
2. Admin prepares a shipment draft.
3. Package dimensions and packed weight are confirmed.
4. Admin manually creates the Delhivery shipment.
5. AWB is stored against the shipment.
6. Admin manually generates/downloads/prints the label.

## APIs Integrated

### Shipment Creation

- Endpoint: `POST /api/cmu/create.json`
- Transport: Laravel HTTP client through centralized `DelhiveryService`
- Auth: configured Delhivery token only, never hardcoded
- Payload type: URL-encoded `format=json` and `data={...}` provider payload

### Shipping Label

- Endpoint: `GET /api/p/packing_slip`
- Parameters: `wbns`, `pdf=true`, `pdf_size=4R`
- Stored as local PDF when binary PDF is returned, otherwise stores provider URL/response snapshot.

## Payload Normalization Strategy

The application builds Delhivery payloads from normalized shipment/order/package data:

- Order number maps to Delhivery `order`.
- Customer name, phone, address, city, state, and pincode are sanitized.
- Payment mode maps to `COD` or `Prepaid`.
- COD amount is sent only for COD shipments.
- Invoice value maps from order total.
- Packed weight is converted from kg to grams.
- Package dimensions are sent in centimeters.
- Product description, SKU context, and HSN codes come from order items/products.
- Pickup location comes from shipping configuration.

Special characters that Delhivery warns against in raw shipment payloads are stripped from text fields.

## Idempotency Strategy

- Shipment creation uses `shipment_attempts`.
- Attempt keys are deterministic per provider/action/order number.
- Existing shipments with AWB are returned without creating a duplicate provider shipment.
- Admin double-clicks are blocked by attempt status checks.
- Active shipment states prevent another shipment from being created for the same order.
- Provider data is stored only on `shipments` and related shipment domain tables.

## Label Handling

- Admin can generate label only after shipment creation.
- Label path/URL and generation timestamp are stored on `shipments`.
- Admin can download or print generated labels from the order detail page.

## Admin Workflow

Order detail now exposes:

- Shipment draft preparation action.
- Package weight/dimension edit form.
- Payment, COD, invoice, pickup, AWB, label, and serviceability context.
- Manual Create Shipment action.
- Manual Generate Label action.
- Download Label and Print Label actions.
- Timeline placeholder remains for future tracking/webhook work.

## State Machine

Shipment transitions are centralized in `ShipmentStatusService`.

Examples prevented:

- `draft` cannot jump directly to `delivered`.
- `failed` cannot jump directly to `picked_up`.
- Label generation cannot happen before shipment creation.

## Remaining Work Before Tracking Integration

- Tracking API polling or webhook ingestion.
- Pickup request API.
- Pickup cancellation.
- Shipment cancellation API.
- NDR/RTO workflow.
- Multi-piece shipment UI and waybill prefetching.
- Invoice document upload/paperless movement support.
