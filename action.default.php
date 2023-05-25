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
$vars = $smarty->getTemplateVars('cmsms_stripe');

$template = null;
if (isset($params['template'])) {
    $template = trim($params['template']);
}
else {
    $template = utils::find_layout_template($params,'simple_checkout','CMSMSStripe::Checkout');
}

if (!isset($params['returnto'])) {
    $params['returnto'] = xt_url::current_url();
} 

$mods = array("module"=>"CMSMSStripe","action"=>"webhook","forajax"=>true,"page"=>"tester");
$actionurl = utils::module_action_link($mods,$smarty);
echo $actionurl;
//print_r($actionurl);
//$action_url = $this->create_url('m1_','admin_editcontent','',array('content_id'=>$key3));
//$action_url = $this->create_url('cntnt01','create-checkout-session','',\xt_utils::encrypt_params($params));

//$tpl = $smarty->CreateTemplate($this->GetTemplateResource($template),null,null,$smarty);
$tpl = $smarty->CreateTemplate($this->GetTemplateResource('default.tpl'),null,null,$smarty);
$tpl->assign('action_url',$action_url);
$tpl->display();


?>