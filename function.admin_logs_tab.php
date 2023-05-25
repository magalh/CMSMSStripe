<?php

if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission(CMSMSStripe::MANAGE_PERM) ) return;

$cmsms_stripe = $smarty->getTemplateVars('cmsms_stripe');

$query = new PaymentQuery;
$payments = $query->GetMatches();
/*$stripe = new \Stripe\StripeClient($cmsms_stripe->secret);

$logs = $stripe->paymentIntents->all([
    'limit' => 3,
  ]);*/

 //print_r($logs->data);

$tpl = $smarty->CreateTemplate( $this->GetTemplateResource('admin_activity_tab.tpl'), null, null, $smarty );
$tpl->assign('logs',$payments);
$tpl->assign('message',$thetemplate);
$tpl->assign('error',$error);
$tpl->display();



