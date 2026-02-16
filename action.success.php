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

	//\xt_utils::send_ajax_and_exit( $session, true );
	
	// Use customer if exists, otherwise use customer_details
	$customer = $session->customer;
	$subscription = $session->subscription;
	
	if($customer) {
		// Customer object exists
		$customer_data = [
			'id' => $customer->id,
			'email' => $customer->email,
			'name' => $customer->name,
			'business_name' => $customer->business_name ?? null,
			'individual_name' => $customer->individual_name ?? null,
			'country' => $customer->address->country ?? null,
			'created' => $customer->created
		];
	} else {
		// Use customer_details from session
		$details = $session->customer_details;
		$customer_data = [
			'id' => null,
			'email' => $details->email,
			'name' => $details->name ?? $details->business_name ?? $details->individual_name,
			'business_name' => $details->business_name ?? null,
			'individual_name' => $details->individual_name ?? null,
			'country' => $details->address->country ?? null,
			'created' => $session->created
		];
	}

	//\xt_utils::send_ajax_and_exit( $customer_data, true);

	$product_id = null;
	$product_name = null;
	$product_type = null;
	$credits = null;
	
	if($session->line_items && $session->line_items->data) {
		$first_item = $session->line_items->data[0];
		if($first_item->price->product) {
			$product_id = $first_item->price->product;
			$product = $stripe->products->retrieve($product_id);
			$product_name = $product->name;
			$product_type = $product->type;
			
			if(isset($first_item->price->metadata->credits)) {
				$credits = $first_item->price->metadata->credits;
			}
		}
	}

	$subscription_data = null;
	if($subscription) {
		$subscription_data = [
			'id' => $subscription->id,
			'product_id' => $product_id,
			'product_name' => $product_name
		];
	}

	$mams = \cms_utils::get_module('MAMS');
	$isloggedin = $mams->LoggedIn();
	$isuser = false;
	$isnewuser = false;

	if(!$customer_data['email']) throw new \Exception("Customer email is required.");

	$uid = $mams->GetUserID($customer_data['email']);
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
		$result = $mams->AddUser($customer_data['email'], $random_password, $expires);
		$uid = $result[1];
		$isuser = true;
		$isnewuser = true;

		$mams->AssignUserToGroup($uid, $stripe_gid);
		if($customer_data['id']) {
			$mams->SetUserPropertyFull('stripe_customer_id', $customer_data['id'], $uid);
		}
		$mams->ForcePasswordChange($uid, true);
		$mams->Login($customer_data['email'], $random_password);
	} else {
		if($customer_data['id']) {
			$mams->SetUserPropertyFull('stripe_customer_id', $customer_data['id'], $uid);
		}
		$uinfo = $mams->GetUserInfo($uid);
		$mams->AssignUserToGroup($uid, $stripe_gid);
		$smarty->assign('uinfo', $uinfo);
	}
	
	
	$smarty->assign('session_id', $session->id);
	$smarty->assign('stripe_customer_id', $customer_data["id"]);
	$smarty->assign('customer', $customer_data);
	$smarty->assign('customer_email', $customer_data["email"]);
	$smarty->assign('subscription', $subscription_data);
	$smarty->assign('product_id', $product_id);
	$smarty->assign('product_name', $product_name);
	$smarty->assign('product_type', $product_type);
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
		'customer_id' => $customer_data['id'],
		'customer_email' => $customer_data['email'],
		'customer_name' => $customer_data['name'],
		'user_id' => $uid ?? null,
		'is_new_user' => $isnewuser,
		'product_id' => $product_id,
		'product_name' => $product_name,
		'amount' => $session->amount_total / 100,
		'currency' => $session->currency,
		'payment_status' => $session->payment_status,
		'invoice' => $session->invoice ?? null
	];
	
	if($subscription) {
		$data['subscription_id'] = $subscription_data['id'];
		$data['product_type'] = "subscription";
	}
	
	if($credits) {
		$data['credits_total'] = $credits;
		$data['product_type'] = "credits";
	}

	//\xt_utils::send_ajax_and_exit( $data );
	
	\CMSMS\HookManager::do_hook('CMSMSStripe::CheckoutSuccess', $data);

	
} catch(\Exception $e) {
	echo '<p class="error">Error: ' . $e->getMessage() . '</p>';
}
?>
