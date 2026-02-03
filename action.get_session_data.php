<?php
if(!defined('CMS_VERSION')) exit;

$session_id = isset($params['session_id']) ? $params['session_id'] : '';

if(!$session_id) {
	return;
}

try {
	$this->validate_config();
	$stripe = new \Stripe\StripeClient($this->GetPreference('cmsms_stripe_secret'));
	$session = $stripe->checkout->sessions->retrieve($session_id, [
		'expand' => ['line_items', 'customer', 'subscription']
	]);
	
	$customer = $session->customer;
	$subscription = $session->subscription;

	$customer_data = [
		'id' => $customer->id,
		'email' => $customer->email,
		'name' => $customer->name,
		'business_name' => $customer->business_name,
		'individual_name' => $customer->individual_name,
		'country' => $customer->address->country ?? null,
		'created' => $customer->created
	];
	
	$subscription_data = null;
	if($subscription) {
		$subscription_data = [
			'id' => $subscription->id,
			'product_id' => $subscription->plan->product
		];
	}
	
	$mams = \cms_utils::get_module('MAMS');
	$user_registered = false;
	if($customer->email) {
		$uid = $mams->GetUserID($customer->email);
		$user_registered = ($uid > 0);
	}
	
	$smarty->assign('stripe_session_id', $session->id);
	$smarty->assign('stripe_customer', $customer_data);
	$smarty->assign('stripe_subscription', $subscription_data);
	$smarty->assign('stripe_user_registered', $user_registered);
	$smarty->assign('stripe_amount', number_format($session->amount_total / 100, 2));
	$smarty->assign('stripe_currency', strtoupper($session->currency));
	
} catch(\Exception $e) {
	// Silent fail
}
?>
