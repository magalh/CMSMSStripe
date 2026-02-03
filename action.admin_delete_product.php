<?php
if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission(CMSMSStripe::MANAGE_PERM) ) return;

$product_id = \xt_param::get_string($params, 'product_id');
if(!$product_id) {
	$this->SetError($this->Lang('error'));
	$this->RedirectToAdminTab();
}

$this->validate_config();
$vars = $smarty->getTemplateVars('cmsms_stripe');

try {
	$stripe = new \Stripe\StripeClient($vars->secret);
	$stripe->products->update($product_id, ['active' => false, 'default_price' => null]);
	$this->SetMessage($this->Lang('product_archived'));
} catch(\Exception $e) {
	$this->SetError($e->getMessage());
}

$this->RedirectToAdminTab('','','admin_products');

?>
