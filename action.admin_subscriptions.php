<?php
if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission(CMSMSStripe::SUBSCRIPTIONS_PERM) ) return;

$this->validate_config();
$vars = $smarty->getTemplateVars('cmsms_stripe');

try {
	$stripe = new \Stripe\StripeClient($vars->secret);
	$subscriptions = $stripe->subscriptions->all([
		'limit' => 100,
		'expand' => ['data.customer', 'data.default_payment_method']
	]);
	
	// Manually expand products for each subscription
	foreach($subscriptions->data as $sub) {
		if(isset($sub->items->data[0]->price->product)) {
			$product_id = $sub->items->data[0]->price->product;
			if(is_string($product_id)) {
				$sub->items->data[0]->price->product = $stripe->products->retrieve($product_id);
			}
		}
	}
} catch(\Exception $e) {
} catch(\Exception $e) {
	$this->SetError($e->getMessage());
	$this->RedirectToAdminTab();
	return;
}

$tpl = $smarty->CreateTemplate($this->GetTemplateResource('admin_subscriptions.tpl'), null, null, $smarty);
$tpl->assign('subscriptions', $subscriptions->data);
$tpl->display();
?>
