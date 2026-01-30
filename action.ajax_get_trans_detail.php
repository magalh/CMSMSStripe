<?php
if( !defined('CMS_VERSION') ) exit;

$error = 0;
use \CMSMSStripe\utils;
use \CMSMSStripe\smarty_plugins;

try {

	if( !isset($params['txn_id'])) throw new \LogicException( 'Subscription not selected' );

	$stripe = new \Stripe\StripeClient($this->GetPreference('cmsms_stripe_secret'));

	$txn = $stripe->paymentIntents->retrieve(
		$params['txn_id'],
		[]
	  );

	if(!$txn->id) throw new \LogicException( 'ID is missing' );
	$data['trans_id'] = $txn->id;
	$currency = utils::get_currency($txn->currency,$this);
	$data['currency'] = $currency['code'];
	$data['currency_symbol'] = $currency['symbol'];
	$data['amount'] = $currency['symbol'].utils::centsToDollars($txn->amount);
	$data['description'] = $txn->description;
	if($txn->customer){
		$cutomer = $stripe->customers->retrieve(
			$txn->customer,
			[]
		  );
		  $data['customer'] = ucwords($cutomer->name);
	}

	$dateformat = trim(cms_userprefs::get_for_user(get_userid(),'date_format_string','%x %X'));
    if( !$dateformat ) $dateformat = '%x %X';

	$data['created'] = strftime($dateformat,$txn->created);

	if($txn->last_payment_error){
		$params['status'] = "declined";
		$params['message'] = $txn->last_payment_error->code;
		$data['outcome'] = $txn->last_payment_error->message;
	} else {
		$params['status'] = $txn->status;
	}
	
	$data['status'] = smarty_plugins::admin_status_icon($params);
	$data['latest_charge'] = $txn->latest_charge;

	$env = null;
	if(!$this->GetPreference('cmsms_stripe_env')) $env = "/test";

	$data['link'] = '<a href="https://dashboard.stripe.com'.$env.'/payments/'.$txn->id.'" target="_blank"><i class="glyph-icon simple-icon-link"></i></a>';
	
}
catch( \Exception $e ) {
	$error = 1;	
	echo $e->getMessage();
	exit();
}
catch( \Stripe\Exception\CardException $e ) {
	$error = 1;	
	$message = $e->getError()->message;
}

$data['error'] = $error;
$data['message'] = $message;
	
\xt_utils::send_ajax_and_exit( $data );
?>