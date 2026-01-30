<?php
if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission(CMSMSStripe::MANAGE_PERM) ) return;

use \CMSMSStripe\utils;

if( isset($params['submit']) ) {
    $this->SetPreference('cmsms_stripe_env',\xt_param::get_string($params,'cmsms_stripe_env'));
	$this->SetPreference('cmsms_stripe_publishable_key',\xt_param::get_string($params,'cmsms_stripe_publishable_key'));
	$this->SetPreference('cmsms_stripe_secret',\xt_param::get_string($params,'cmsms_stripe_secret'));
	$this->SetPreference('cmsms_stripe_currency_code',strtoupper(\xt_param::get_string($params,'cmsms_stripe_currency_code')));
	$this->SetPreference('cmsms_stripe_url_webhook',\xt_param::get_string($params,'cmsms_stripe_url_webhook'));
	$this->SetPreference('cmsms_stripe_url_success',\xt_param::get_string($params,'cmsms_stripe_url_success'));
	$this->SetPreference('cmsms_stripe_success_page',\xt_param::get_string($params,'cmsms_stripe_success_page'));
	$this->SetPreference('cmsms_stripe_url_cancel',\xt_param::get_string($params,'cmsms_stripe_url_cancel'));
	$this->SetMessage("Saved");
    $this->RedirectToAdminTab('settings');
}

$tpl = $smarty->CreateTemplate( $this->GetTemplateResource('admin_settings_tab.tpl'), null, null, $smarty );

/*$mods = array("module"=>"CMSMSStripe","action"=>"webhook","forajax"=>true,"page"=>"tester");
$actionurl = utils::module_action_link($mods,$smarty);
echo $actionurl;
echo '<br>';
echo $this->create_url( '_m1', 'delete_file', $this->_returnid, array('upload_id'=>$this->id));;
*/
$xtensions = xt_utils::get_module('CMSMSExt');
$currency_list = $xtensions->get_currency_list_options();
asort($currency_list);
$smarty->assign('currency_list',$currency_list);
$smarty->assign('webhook_url',$webhook_url);

$tpl->display();

