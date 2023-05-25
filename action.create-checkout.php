<?php
if( !defined('CMS_VERSION') ) exit;

$this->validate_config();
$config = \cms_config::get_instance();

$vars = $smarty->getTemplateVars('cmsms_stripe');
\Stripe\Stripe::setApiKey($vars->secret);

try {
    // retrieve JSON from POST body
    $jsonStr = file_get_contents('php://input');
    $jsonObj = json_decode($jsonStr);

    // Create a PaymentIntent with amount and currency
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $jsonObj->data->price,
        'currency' => $vars->currency_code,
        'automatic_payment_methods' => [
            'enabled' => true,
        ]
    ]);

    $output = [
        'clientSecret' => $paymentIntent->client_secret,
    ];

} catch (Error $e) {
    $output = ['error' => $e->getMessage()];
}

\xt_utils::send_ajax_and_exit( $output );

?>