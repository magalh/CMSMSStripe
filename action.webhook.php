<?php
if(!defined('CMS_VERSION')) exit;

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
$endpoint_secret = $this->GetPreference('cmsms_stripe_webhook_secret');

if(!$endpoint_secret) {
	http_response_code(400);
	exit;
}

try {
	$event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
} catch(\UnexpectedValueException $e) {
	http_response_code(400);
	exit;
} catch(\Stripe\Exception\SignatureVerificationException $e) {
	http_response_code(400);
	exit;
}

switch($event->type) {
	case 'checkout.session.completed':
		$session = $event->data->object;
		$this->HandleCheckoutCompleted($session);
		break;
	case 'payment_intent.succeeded':
		$payment_intent = $event->data->object;
		$this->HandlePaymentSucceeded($payment_intent);
		break;
}

http_response_code(200);
?>
