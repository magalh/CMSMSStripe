<?php
if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission(CMSMSStripe::MANAGE_PERM) ) return;

try {
	$secret = \xt_param::get_string($params,'secret');
	if(!$secret) throw new \LogicException('Secret key required');
	
	$stripe = new \Stripe\StripeClient($secret);
	$account = $stripe->accounts->retrieve();
	
	$data = [
		'success' => true,
		'message' => 'Connection successful',
		'account_id' => $account->id
	];
} catch(\Exception $e) {
	$data = [
		'success' => false,
		'message' => $e->getMessage()
	];
}

\xt_utils::send_ajax_and_exit($data);
?>
