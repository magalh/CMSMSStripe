<?php
if(!defined('CMS_VERSION')) exit;

// Check for test event parameter
$test_event = $params['event'] ?? '';
$skip_duplicate_check = isset($params['skip_duplicate']) && $params['skip_duplicate'] == 1 ? true : false;

if($test_event) {
	$test_file = cms_join_path($this->GetModulePath(), 'test_webhooks', $test_event . '.json');
	if(file_exists($test_file)) {
		$payload = file_get_contents($test_file);
	} else {
		\xt_utils::send_ajax_and_exit(['error' => 'Test file not found: ' . $test_event . '.json', 'code' => 404]);
	}
} else {
	$payload = @file_get_contents('php://input');
}


$config = cmsms()->GetConfig();
$debug = isset($config['stripe_debug']) && $config['stripe_debug'] === true;

$event = null;

try {
	$event = json_decode($payload);
	if(!$event || !isset($event->type) || !isset($event->data->object)) {
		if($debug) {
			\xt_utils::send_ajax_and_exit(['error' => 'Invalid payload', 'code' => 400]);
		}
		http_response_code(400);
		exit;
	}
} catch(\Exception $e) {
	if($debug) {
		\xt_utils::send_ajax_and_exit(['error' => 'JSON decode failed: ' . $e->getMessage(), 'code' => 400]);
	}
	http_response_code(400);
	exit;
}

$is_production = $this->GetPreference('cmsms_stripe_env') == 1;
$endpoint_secret = $this->GetPreference('cmsms_stripe_webhook_secret');

if($is_production && $endpoint_secret && !$test_event) {
	$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
	if($sig_header) {
		try {
			$event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
		} catch(\Stripe\Exception\SignatureVerificationException $e) {
			if($debug) {
				\xt_utils::send_ajax_and_exit(['error' => 'Signature verification failed: ' . $e->getMessage(), 'code' => 400]);
			}
			http_response_code(400);
			exit;
		}
	}
}
$db = $this->GetDb();

if(!$skip_duplicate_check) {
	$event_id = $db->qstr($event->id);
	$check = $db->GetOne("SELECT event_id FROM ".cms_db_prefix()."module_cmsmsstripe_events WHERE event_id = {$event_id}");

/*	if($check) {
		http_response_code(500);
		if($debug) {
			\xt_utils::send_ajax_and_exit(['message' => 'Duplicate event ignored', 'event_id' => $event->id]);
		}
		exit;
	} else {
		$event_type = $db->qstr($event->type);
		$db->Execute("INSERT INTO ".cms_db_prefix()."module_cmsmsstripe_events (event_id, event_type, created_at) VALUES ({$event_id}, {$event_type}, ".time().")");
	}*/
}

switch($event->type) {
	case 'checkout.session.completed':
		$session = $event->data->object;
		$data = [
			'event_id' => $event->id,
			'session_id' => $session->id,
			'customer_id' => $session->customer,
			'customer_email' => $session->customer_details->email ?? null,
			'customer_name' => $session->customer_details->name ?? null,
			'amount' => $session->amount_total / 100,
			'currency' => $session->currency,
			'subscription_id' => $session->subscription ?? null,
			'payment_status' => $session->payment_status,
			'mode' => $session->mode
		];

		\CMSMS\HookManager::do_hook('CMSMSStripe::SessionCompleted', $data);
		break;
	case 'payment_intent.succeeded':
		$payment_intent = $event->data->object;
		$data = [
			'event_id' => $event->id,
			'payment_intent_id' => $payment_intent->id,
			'amount' => $payment_intent->amount / 100,
			'currency' => $payment_intent->currency,
			'customer' => $payment_intent->customer ?? null
		];
		\CMSMS\HookManager::do_hook('CMSMSStripe::PaymentCompleted', $data);
		break;
	case 'customer.subscription.created':
		$subscription = $event->data->object;
		$data = [
			'event_id' => $event->id,
			'subscription_id' => $subscription->id,
			'customer_id' => $subscription->customer,
			'status' => $subscription->status
		];
		\CMSMS\HookManager::do_hook('CMSMSStripe::SubscriptionCreated', $data);
		break;
	case 'customer.subscription.updated':
		$subscription = $event->data->object;
		$data = [
			'event_id' => $event->id,
			'subscription_id' => $subscription->id,
			'customer_id' => $subscription->customer,
			'status' => $subscription->status
		];
		\CMSMS\HookManager::do_hook('CMSMSStripe::SubscriptionUpdated', $data);
		break;
	case 'customer.subscription.deleted':
		$subscription = $event->data->object;
		$data = [
			'event_id' => $event->id,
			'subscription_id' => $subscription->id,
			'customer_id' => $subscription->customer,
			'status' => $subscription->status
		];
		\CMSMS\HookManager::do_hook('CMSMSStripe::SubscriptionDeleted', $data);
		break;
	case 'customer.subscription.paused':
		$subscription = $event->data->object;
		$data = [
			'event_id' => $event->id,
			'subscription_id' => $subscription->id,
			'customer_id' => $subscription->customer,
			'status' => $subscription->status
		];
		\CMSMS\HookManager::do_hook('CMSMSStripe::SubscriptionPaused', $data);
		break;
	case 'customer.subscription.resumed':
		$subscription = $event->data->object;
		$data = [
			'event_id' => $event->id,
			'subscription_id' => $subscription->id,
			'customer_id' => $subscription->customer,
			'status' => $subscription->status
		];
		\CMSMS\HookManager::do_hook('CMSMSStripe::SubscriptionResumed', $data);
		break;
	case 'charge.refunded':
		$charge = $event->data->object;
		if(isset($charge->refunds->data[0])) {
			$refund = $charge->refunds->data[0];
			$data = [
				'event_id' => $event->id,
				'refund_id' => $refund->id,
				'amount' => $refund->amount / 100,
				'currency' => $refund->currency,
				'payment_intent' => $refund->payment_intent
			];
			\CMSMS\HookManager::do_hook('CMSMSStripe::RefundIssued', $data);
		}
		break;
	case 'invoice.payment_failed':
		$invoice = $event->data->object;
		$data = [
			'event_id' => $event->id,
			'invoice_id' => $invoice->id,
			'customer_id' => $invoice->customer,
			'amount_due' => $invoice->amount_due / 100,
			'subscription_id' => $invoice->subscription ?? null
		];
		\CMSMS\HookManager::do_hook('CMSMSStripe::InvoicePaymentFailed', $data);
		break;
}

if($debug) {
	\xt_utils::send_ajax_and_exit(['message' => 'Webhook processed successfully', 'event_type' => $event->type, 'data' => $data], true);
}
http_response_code(200);
?>
