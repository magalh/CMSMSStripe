<?php
if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission(CMSMSStripe::PRODUCTS_PERM) ) return;

$this->validate_config();
$vars = $smarty->getTemplateVars('cmsms_stripe');

try {
	$stripe = new \Stripe\StripeClient($vars->secret);
	$products = $stripe->products->all([
		'limit' => 100,
		'active' => true,
		'expand' => ['data.default_price']
	]);
} catch(\Exception $e) {
	$this->DisplayErrorMessage($e->getMessage());
	return;
}

$tpl = $smarty->CreateTemplate($this->GetTemplateResource('admin_products_tab.tpl'), null, null, $smarty);
$tpl->assign('products', $products->data);
$tpl->display();
?>
