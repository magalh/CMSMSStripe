<?php
if( !defined('CMS_VERSION') ) exit;
$this->CreatePermission(CMSMSStripe::MANAGE_PERM,'Manage CMSMSStripe');
$this->CreatePermission(CMSMSStripe::PRODUCTS_PERM,'Manage Stripe Products');
$this->CreatePermission(CMSMSStripe::TRANSACTIONS_PERM,'View Stripe Transactions');
$this->CreatePermission(CMSMSStripe::SUBSCRIPTIONS_PERM,'Manage Stripe Subscriptions');

$db = $this->GetDb();
$dict = NewDataDictionary($db);
$taboptarray = array('mysql' => 'TYPE=MyISAM');

$flds = "id I KEY AUTO,
         order_id      I NOTNULL,
         amount        F,
         payment_date  I,
         status        C(50),
         method        C(50),
         gateway       C(50),
         cc_number     B,
         cc_expiry     I,
         cc_verifycode B,
	     confirmation_num C(255),
         txn_id        C(255),
         notes         X,
         assocdata     X
";
$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_cmsmsstripe_payments", $flds, $taboptarray);

$dict->ExecuteSQLArray($sqlarray);

$this->AddEventHandler('MAMS', 'OnLogin', false);
$this->AddEventHandler('MAMS', 'OnLogout', false);
$this->AddEventHandler('MAMS', 'OnExpireUser', false);
$this->AddEventHandler('MAMS', 'OnCreateUser', false);
$this->AddEventHandler('MAMS', 'OnUpdateUser', false);
$this->AddEventHandler('MAMSRegistration', 'onUserRegistered', false);

try {
	$uid = max(1, get_userid(FALSE));
	
	$product_list_type = new \CmsLayoutTemplateType();
	$product_list_type->set_originator($this->GetName());
	$product_list_type->set_name('product_list');
	$product_list_type->set_dflt_flag(TRUE);
	$product_list_type->set_lang_callback('CMSMSStripe::page_type_lang_callback');
	$product_list_type->set_content_callback('CMSMSStripe::reset_page_type_defaults');
	$product_list_type->reset_content_to_factory();
	$product_list_type->save();
	
	$product_detail_type = new \CmsLayoutTemplateType();
	$product_detail_type->set_originator($this->GetName());
	$product_detail_type->set_name('product_detail');
	$product_detail_type->set_dflt_flag(TRUE);
	$product_detail_type->set_lang_callback('CMSMSStripe::page_type_lang_callback');
	$product_detail_type->set_content_callback('CMSMSStripe::reset_page_type_defaults');
	$product_detail_type->reset_content_to_factory();
	$product_detail_type->save();
	
	$payment_success_type = new \CmsLayoutTemplateType();
	$payment_success_type->set_originator($this->GetName());
	$payment_success_type->set_name('payment_success');
	$payment_success_type->set_dflt_flag(TRUE);
	$payment_success_type->set_lang_callback('CMSMSStripe::page_type_lang_callback');
	$payment_success_type->set_content_callback('CMSMSStripe::reset_page_type_defaults');
	$payment_success_type->reset_content_to_factory();
	$payment_success_type->save();
	
	$tpl = new CmsLayoutTemplate;
	$tpl->set_name($tpl::generate_unique_name('CMSMSStripe Product List'));
	$tpl->set_owner($uid);
	$tpl->set_type($product_list_type);
	$tpl->set_content($product_list_type->get_dflt_contents());
	$tpl->set_type_dflt(TRUE);
	$tpl->save();
	
	$tpl2 = new CmsLayoutTemplate;
	$tpl2->set_name($tpl2::generate_unique_name('CMSMSStripe Product Detail'));
	$tpl2->set_owner($uid);
	$tpl2->set_type($product_detail_type);
	$tpl2->set_content($product_detail_type->get_dflt_contents());
	$tpl2->set_type_dflt(TRUE);
	$tpl2->save();
	
	$tpl3 = new CmsLayoutTemplate;
	$tpl3->set_name($tpl3::generate_unique_name('CMSMSStripe Payment Success'));
	$tpl3->set_owner($uid);
	$tpl3->set_type($payment_success_type);
	$tpl3->set_content($payment_success_type->get_dflt_contents());
	$tpl3->set_type_dflt(TRUE);
	$tpl3->save();
} catch(\Exception $e) {
	audit('', $this->GetName(), 'Template creation error: ' . $e->getMessage());
}

?>