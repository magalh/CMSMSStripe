<?php
if(!defined('CMS_VERSION')) exit;

$endpoint_secret = $this->GetPreference('cmsms_stripe_webhook_secret');
$payload = @file_get_contents('php://input');
$event = null;

try {
	$event = json_decode($payload);
	if(!$event || !isset($event->type)) {
		http_response_code(400);
		exit;
	}
} catch(\Exception $e) {
	http_response_code(400);
	exit;
}

if($endpoint_secret) {
	$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
	if($sig_header) {
		try {
			$event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
		} catch(\Stripe\Exception\SignatureVerificationException $e) {
			http_response_code(400);
			exit;
		}
	}
}
/*
$db = $this->GetDb();
$event_id = $db->GetOne('SELECT event_id FROM '.cms_db_prefix().'module_cmsmsstripe_events WHERE event_id = ?', [$event->id]);

if($event_id) {
	http_response_code(200);
	exit;
}

try {
	$db->Execute('INSERT INTO '.cms_db_prefix().'module_cmsmsstripe_events (event_id, event_type, created_at) VALUES (?,?,?)', 
		[$event->id, $event->type, time()]);
} catch(\Exception $e) {
	http_response_code(200);
	exit;
}*/
switch($event->type) {
	case 'checkout.session.completed':
		$session = $event->data->object;
		\CMSMS\HookManager::do_hook('CMSMSStripe::SessionCreated', [
			'session_id' => $session->id,
			'customer_id' => $session->customer,
			'customer_email' => $session->customer_details->email ?? null,
			'customer_name' => $session->customer_details->name ?? null,
			'amount' => $session->amount_total / 100,
			'currency' => $session->currency,
			'subscription_id' => $session->subscription ?? null,
			'payment_status' => $session->payment_status,
			'mode' => $session->mode
		]);
		break;
	case 'payment_intent.succeeded':
		$payment_intent = $event->data->object;
		\CMSMS\HookManager::do_hook('CMSMSStripe::PaymentCompleted', [
			'payment_intent_id' => $payment_intent->id,
			'amount' => $payment_intent->amount / 100,
			'currency' => $payment_intent->currency,
			'customer' => $payment_intent->customer ?? null
		]);
		break;
	case 'payment_intent.payment_failed':
		$payment_intent = $event->data->object;
		\CMSMS\HookManager::do_hook('CMSMSStripe::PaymentFailed', [
			'payment_intent_id' => $payment_intent->id,
			'amount' => $payment_intent->amount / 100,
			'currency' => $payment_intent->currency,
			'customer' => $payment_intent->customer ?? null,
			'error' => $payment_intent->last_payment_error->message ?? 'Unknown error'
		]);
		break;
	case 'customer.subscription.created':
		$subscription = $event->data->object;
		\CMSMS\HookManager::do_hook('CMSMSStripe::SubscriptionCreated', [
			'subscription_id' => $subscription->id,
			'customer_id' => $subscription->customer,
			'status' => $subscription->status
		]);
		break;
	case 'customer.subscription.updated':
		$subscription = $event->data->object;
		\CMSMS\HookManager::do_hook('CMSMSStripe::SubscriptionUpdated', [
			'subscription_id' => $subscription->id,
			'customer_id' => $subscription->customer,
			'status' => $subscription->status
		]);
		break;
	case 'customer.subscription.deleted':
		$subscription = $event->data->object;
		\CMSMS\HookManager::do_hook('CMSMSStripe::SubscriptionExpired', [
			'subscription_id' => $subscription->id,
			'customer_id' => $subscription->customer,
			'status' => $subscription->status
		]);
		break;
	case 'charge.refunded':
		$charge = $event->data->object;
		if(isset($charge->refunds->data[0])) {
			$refund = $charge->refunds->data[0];
			\CMSMS\HookManager::do_hook('CMSMSStripe::RefundIssued', [
				'refund_id' => $refund->id,
				'amount' => $refund->amount / 100,
				'currency' => $refund->currency,
				'payment_intent' => $refund->payment_intent
			]);
		}
		break;
	case 'invoice.payment_failed':
		$invoice = $event->data->object;
		\CMSMS\HookManager::do_hook('CMSMSStripe::InvoicePaymentFailed', [
			'invoice_id' => $invoice->id,
			'customer_id' => $invoice->customer,
			'amount_due' => $invoice->amount_due / 100,
			'subscription_id' => $invoice->subscription ?? null
		]);
		break;
}

http_response_code(200);
?>
