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
	'mode' => 'payment',
	'automatic_tax' => [
		'enabled' => true,
	],
	'success_url' => CMS_ROOT_URL.'/index.php?mact=CMSMSStripe,cntnt01,success,0&cntnt01session_id={CHECKOUT_SESSION_ID}',
	'cancel_url' => $cancel_url,
];

$mams = \cms_utils::get_module('MAMS');

$success_page = $this->GetPreference('cmsms_stripe_success_page');
if($success_page) {
	$returnid = $mams->resolve_alias_or_id($success_page);
	$session_params['success_url'] .= '&cntnt01returnid='.$returnid;
}

$uid = $mams->LoggedInId();
if($uid) {
	$stripe_customer_id = $mams->GetUserPropertyFull('stripe_customer_id',$uid);
	if($stripe_customer_id) {
		$session_params['customer'] = $stripe_customer_id;
	}
}

$price = $stripe->prices->retrieve($params['price_id']);
if($price->type === 'recurring') {
	$session_params['mode'] = 'subscription';
}

$checkout_session = $stripe->checkout->sessions->create($session_params);

header("HTTP/1.1 303 See Other");
header("Location: " . $checkout_session->url);

?>