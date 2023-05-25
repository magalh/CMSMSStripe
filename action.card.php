<?php
if( !defined('CMS_VERSION') ) exit;
use \CMSMSStripe\utils;

$type = "card";
if (isset($params['type'])) {
    $type = trim($params['type']);
}
$amount = $params['amount'];
if (!isset($amount)) {
    throw new \LogicException( 'Amount is needed' );
}

$this->validate_config();

/*$card_type = utils::create_template_type('Checkout', $this);
$fn = __DIR__.'/templates/orig_checkout_template.tpl';
if ( is_file($fn) ) utils::create_template_of_type($card_type, $this->GetName().' Sample Checkout Form', file_get_contents($fn), true);
*/
$cmsms_stripe = $smarty->getTemplateVars('cmsms_stripe');
$cmsms_stripe->theme = \xt_param::get_string($params,'theme');
$cmsms_stripe->amount = $amount;
$smarty->left_delimiter = '{**';
$smarty->right_delimiter = '**}';

/*$template = null;
if (isset($params['template'])) {
    $template = trim($params['template']);
}
else {
    $template = utils::find_layout_template($params,'cardtemplate','CMSMSStripe::Card Form');
}
$tpl = $smarty->CreateTemplate($this->GetTemplateResource($template),null,null,$smarty);
*/
$tpl = $smarty->CreateTemplate($this->GetTemplateResource('orig_card_template.tpl'),null,null,$smarty);

$tpl->display();


?>