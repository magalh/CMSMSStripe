<?php
if( !defined('CMS_VERSION') ) exit;

$this->validate_config();

$params = \xt_utils::decrypt_params($params);

$vars = $smarty->getTemplateVars('cmsms_stripe');
$stripe = new \Stripe\StripeClient($vars->secret);

$cancel_url = isset($params['returnto']) ? html_entity_decode($params['returnto'], ENT_QUOTES | ENT_HTML5) : $vars->url_cancel;
while(strpos($cancel_url, '&amp;') !== false) {
	$cancel_url = html_entity_decode($cancel_url, ENT_QUOTES | ENT_HTML5);
}

$session_params = [
	'payment_method_types' => ['card'],
	'line_items' => [[
		'price' => $params['price_id'],
		'quantity' => 1,
	]],
	'name_collection' => [
    'business' => ['enabled' => true, 'optional' => true],
    'individual' => [
      'enabled' => true
    ]],
	'mode' => 'payment',
	'automatic_tax' => [
		'enabled' => true,
	],
	'tax_id_collection' => [
		'enabled' => true
	],
	'allow_promotion_codes' => true,
	'success_url' => CMS_ROOT_URL.'/index.php?mact=CMSMSStripe,cntnt01,success,0&cntnt01session_id={CHECKOUT_SESSION_ID}',
	'cancel_url' => $cancel_url,
];

$mams = \cms_utils::get_module('MAMS');

$success_page = $this->GetPreference('cmsms_stripe_success_page');
if($success_page) {
	$returnid = $mams->resolve_alias_or_id($success_page);
	$session_params['success_url'] .= '&cntnt01returnid=' . $returnid;
}


$price = $stripe->prices->retrieve($params['price_id']);
if($price->type === 'recurring') {
	$session_params['mode'] = 'subscription';
} else {
	$session_params['invoice_creation'] = ['enabled' => true];
}

$uid = $mams->LoggedInId();
if($uid) {
	$stripe_customer_id = $mams->GetUserPropertyFull('stripe_customer_id',$uid);
	if($stripe_customer_id) {
		$session_params['client_reference_id'] = $uid;
		$session_params['customer'] = $stripe_customer_id;
		$session_params['customer_update'] = ['name' => 'auto'];
	}
} elseif($session_params['mode'] === 'payment') {
	$session_params['customer_creation'] = 'always';
}

$checkout_session = $stripe->checkout->sessions->create($session_params);

$hook_data = [
	'session_id' => $checkout_session->id,
	'amount' => $checkout_session->amount_total / 100,
	'currency' => $checkout_session->currency,
];
if($uid) {
	$hook_data['client_reference_id'] = $uid;
	$hook_data['email'] = $mams->GetUserName($uid);
}
\CMSMS\HookManager::do_hook('CMSMSStripe::SessionCreated', $hook_data);

header("HTTP/1.1 303 See Other");
header("Location: " . $checkout_session->url);

?>