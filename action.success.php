<?php
if(!defined('CMS_VERSION')) exit;

$session_id = isset($params['session_id']) ? $params['session_id'] : '';
if(!$session_id ) throw new \Exception("Session ID is required.");

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
	$isloggedin = $mams->LoggedIn();
	$isuser = false;
	$isnewuser = false;

	if(!$customer->email) throw new \Exception("Customer email is required.");

	$uid = $mams->GetUserID($customer->email);
	$isuser = ($uid > 0);
	
	if($isloggedin) {
		$logged_uid = $mams->LoggedInId();
		if($uid > 0 && $uid != $logged_uid) {
			throw new \Exception("Payment email does not match logged in user.");
		}
	}
	
	$stripe_gid = $mams->GetGroupID('stripe');

	if(!$isuser) {
		$random_password = bin2hex(random_bytes(8));
		$tmp = (int) $mams->GetPreference('expireage_months',520);
		$expires = strtotime("+{$tmp} months 00:00");
		if(!$expires) $expires = PHP_INT_MAX;
		$result = $mams->AddUser($customer->email, $random_password, $expires);
		$uid = $result[1];
		$isuser = true;
		$isnewuser = true;

		$mams->AssignUserToGroup($uid, $stripe_gid);
		$mams->SetUserPropertyFull('stripe_customer_id', $customer->id, $uid);
		$mams->Login($customer->email, $random_password);
	} else {
		$mams->SetUserPropertyFull('stripe_customer_id', $customer->id, $uid);
		$uinfo = $mams->GetUserInfo($uid);
		$mams->AssignUserToGroup($uid, $stripe_gid);
		$smarty->assign('uinfo', $uinfo);
	}
	
	
	$smarty->assign('session_id', $session->id);
	$smarty->assign('stripe_customer_id', $customer_data["id"]);
	$smarty->assign('customer', $customer_data);
	$smarty->assign('customer_email', $customer_data["email"]);
	$smarty->assign('subscription', $subscription_data);
	$smarty->assign('isloggedin', $isloggedin);
	$smarty->assign('isuser', $isuser);
	$smarty->assign('isnewuser', $isnewuser);
	$smarty->assign('amount', number_format($session->amount_total / 100, 2));
	$smarty->assign('currency', strtoupper($session->currency));
	
	$template = \CMSMSStripe\utils::find_layout_template($params, 'template', 'CMSMSStripe::payment_success');
	$tpl = $smarty->CreateTemplate($this->GetTemplateResource($template), null, null, $smarty);
	$tpl->display();

	$data = [
		'session_id' => $session->id,
		'customer_id' => $customer->id,
		'customer_email' => $customer->email,
		'customer_name' => $customer->name,
		'user_id' => $uid ?? null,
		'is_new_user' => $isnewuser,
		'subscription_id' => $subscription->id ?? null,
		'product_id' => $subscription_data['product_id'] ?? null,
		'amount' => $session->amount_total / 100,
		'currency' => $session->currency,
		'payment_status' => $session->payment_status
	];
	
	\CMSMS\HookManager::do_hook('CMSMSStripe::CheckoutSuccess', $data);
	
} catch(\Exception $e) {
	echo '<p class="error">Error: ' . $e->getMessage() . '</p>';
}
?>
