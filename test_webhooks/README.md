# Stripe Webhook Test Samples

This directory contains sample webhook payloads for testing.

## Setup

1. Enable debug mode in config.php:
```php
$config['stripe_debug'] = true;
```

2. Set webhook secret in module settings (Admin → CMSMSStripe → Settings)

## Testing with cURL

Test each webhook event:

```bash
# Checkout Session Completed
curl -X POST https://pixelsolutions.local/index.php?mact=CMSMSStripe,cntnt01,webhook,0 \
  -H "Content-Type: application/json" \
  -d @checkout.session.completed.json

# Payment Intent Succeeded
curl -X POST https://pixelsolutions.local/index.php?mact=CMSMSStripe,cntnt01,webhook,0 \
  -H "Content-Type: application/json" \
  -d @payment_intent.succeeded.json

# Payment Intent Failed
curl -X POST https://pixelsolutions.local/index.php?mact=CMSMSStripe,cntnt01,webhook,0 \
  -H "Content-Type: application/json" \
  -d @payment_intent.payment_failed.json

# Subscription Created
curl -X POST https://pixelsolutions.local/index.php?mact=CMSMSStripe,cntnt01,webhook,0 \
  -H "Content-Type: application/json" \
  -d @customer.subscription.created.json

# Subscription Updated
curl -X POST https://pixelsolutions.local/index.php?mact=CMSMSStripe,cntnt01,webhook,0 \
  -H "Content-Type: application/json" \
  -d @customer.subscription.updated.json

# Subscription Deleted
curl -X POST https://pixelsolutions.local/index.php?mact=CMSMSStripe,cntnt01,webhook,0 \
  -H "Content-Type: application/json" \
  -d @customer.subscription.deleted.json

# Charge Refunded
curl -X POST https://pixelsolutions.local/index.php?mact=CMSMSStripe,cntnt01,webhook,0 \
  -H "Content-Type: application/json" \
  -d @charge.refunded.json

# Invoice Payment Failed
curl -X POST https://pixelsolutions.local/index.php?mact=CMSMSStripe,cntnt01,webhook,0 \
  -H "Content-Type: application/json" \
  -d @invoice.payment_failed.json
```

## Debug Mode Response

With debug mode enabled, you'll get JSON responses:
```json
{
  "message": "Webhook processed successfully",
  "event_type": "checkout.session.completed",
  "event_id": "evt_test_checkout_001",
  "code": 200
}
```

## Production Mode

In production (cmsms_stripe_env = 1), webhooks require valid Stripe signatures.
