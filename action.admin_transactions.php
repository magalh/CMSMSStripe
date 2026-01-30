<?php
if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission(CMSMSStripe::TRANSACTIONS_PERM) ) return;

$this->validate_config();
$vars = $smarty->getTemplateVars('cmsms_stripe');

try {
	$stripe = new \Stripe\StripeClient($vars->secret);
	$charges = $stripe->charges->all(['limit' => 100]);
} catch(\Exception $e) {
	$this->DisplayErrorMessage($e->getMessage());
	return;
}

$tpl = $smarty->CreateTemplate($this->GetTemplateResource('admin_transactions.tpl'), null, null, $smarty);
$tpl->assign('charges', $charges->data);
$tpl->display();
?>
