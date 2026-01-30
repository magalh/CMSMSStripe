<?php
if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission(CMSMSStripe::SUBSCRIPTIONS_PERM) ) return;

$this->validate_config();
$vars = $smarty->getTemplateVars('cmsms_stripe');

try {
	$stripe = new \Stripe\StripeClient($vars->secret);
	$subscriptions = $stripe->subscriptions->all(['limit' => 100]);
} catch(\Exception $e) {
	$this->DisplayErrorMessage($e->getMessage());
	return;
}

$tpl = $smarty->CreateTemplate($this->GetTemplateResource('admin_subscriptions.tpl'), null, null, $smarty);
$tpl->assign('subscriptions', $subscriptions->data);
$tpl->display();
?>
