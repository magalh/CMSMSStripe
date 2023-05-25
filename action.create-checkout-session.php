<?php
if( !defined('CMS_VERSION') ) exit;

$this->validate_config();

$params = \xt_utils::decrypt_params($params);

$vars = $smarty->getTemplateVars('cmsms_stripe');
$stripe = new \Stripe\StripeClient($vars->secret);

$session_params = [
	'payment_method_types' => ['card'],
	'line_items' => [[
		'price_data' => [
		'currency' => $vars->currency_code,
		'product_data' => [
			'name' => $params['item_name'],
		],
		'unit_amount' => $params['amount'],
	],
	'quantity' => 1,
	]],
	'mode' => 'payment',
	'success_url' => $vars->url_success.'?session_id={CHECKOUT_SESSION_ID}',
	'cancel_url' => $params['returnto'],
];

$checkout_session = $stripe->checkout->sessions->create($session_params);

header("HTTP/1.1 303 See Other");
header("Location: " . $checkout_session->url);

?>