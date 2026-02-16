<?php
if(!defined('CMS_VERSION')) exit;

$stripe_customer_id = isset($params['stripe_customer_id']) ? $params['stripe_customer_id'] : '';
if(!$stripe_customer_id) throw new \Exception("Stripe customer ID is required");

try {
	$this->validate_config();
	$stripe = new \Stripe\StripeClient($this->GetPreference('cmsms_stripe_secret'));
	
	$subscriptions = $stripe->subscriptions->all([
		'customer' => $stripe_customer_id,
		'limit' => 100,
		'expand' => ['data.plan.product']
	]);

	//\xt_utils::send_ajax_and_exit( $subscriptions->data, true );

	
	$smarty->assign('has_subscription', true);
	$smarty->assignGlobal('subscriptions', $subscriptions->data);
	$smarty->assign('stripe_customer_id', $stripe_customer_id);
} catch(\Exception $e) {
	$smarty->assign('has_subscription', false);
	$smarty->assign('error', $e->getMessage());
}

$tpl = $smarty->CreateTemplate($this->GetTemplateResource('org_subscriptions.tpl'), null, null, $smarty);
$tpl->display();
?>
