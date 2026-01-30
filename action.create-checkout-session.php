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
	'success_url' => $vars->url_success.'?session_id={CHECKOUT_SESSION_ID}',
	'cancel_url' => $cancel_url,
];

$price = $stripe->prices->retrieve($params['price_id']);
if($price->type === 'recurring') {
	$session_params['mode'] = 'subscription';
}

$checkout_session = $stripe->checkout->sessions->create($session_params);

header("HTTP/1.1 303 See Other");
header("Location: " . $checkout_session->url);

?>