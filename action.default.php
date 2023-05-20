<?php
if( !defined('CMS_VERSION') ) exit;

$type = "card";
if (isset($params['type'])) {
    $type = trim($params['type']);
}
$amount = $params['amount'];
if (!isset($amount)) {
    throw new \LogicException( 'Amount is needed' );
}

$this->checkSetup();

$smarty->assign('stripe_publishable_key',$this->GetPreference('stripe_publishable_key'));
$smarty->assign('stripe_secret',$this->GetPreference('stripe_secret'));

$stripe = new \Stripe\StripeClient($this->GetPreference('stripe_secret'));

try {
	$paymentIntent = $stripe->paymentIntents->create([
	  'payment_method_types' => [$type],
	  'amount' => $amount,
	  'currency' => 'usd',
	]);
  } catch (\Stripe\Exception\ApiErrorException $e) {
	http_response_code(400);
	error_log($e->getError()->message);
  ?>
	<h1>Error</h1>
	<p>Failed to create a PaymentIntent</p>
	<p>Please check the server logs for more information</p>
  <?php
	exit;
  } catch (Exception $e) {
	error_log($e);
	http_response_code(500);
	exit;
  }


/*$customer = $stripe->customers->create([
    'description' => 'example customer',
    'email' => 'email@example.com',
    'payment_method' => 'pm_card_visa',
]);*/
echo $customer;


$tpl = $smarty->CreateTemplate($this->GetTemplateResource('default.tpl'),null,null,$smarty);
//$tpl->assign('orders',$orders);
$tpl->display();


?>