<?php
if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission(CMSMSStripe::MANAGE_PERM) ) return;

$tpl = $smarty->CreateTemplate($this->GetTemplateResource('defaultadmin.tpl'),null,null,$smarty);
   
   
   if( isset($params['submit']) ) {
	$this->SetPreference('stripe_env',$params['stripe_env']);
	$this->SetPreference('stripe_publishable_key',$params['stripe_publishable_key']);
	$this->SetPreference('stripe_secret',$params['stripe_secret']);
	$this->SetMessage("Saved");
	$this->RedirectToAdminTab();	
   }

    $smarty->assign('stripe_env',$this->GetPreference('stripe_env'));
	$smarty->assign('stripe_publishable_key',$this->GetPreference('stripe_publishable_key'));
	$smarty->assign('stripe_secret',$this->GetPreference('stripe_secret'));

	$list = $this->ListPreferencesByPrefix("setting_");
	$profiles = array();
	foreach( $list as $one ) {
		$profiles[] = $this->GetPreference('setting_'.$one);
	}
	$smarty->assign('mod_settings',$profiles);


	$tpl->display();

?>