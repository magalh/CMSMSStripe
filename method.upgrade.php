<?php
#---------------------------------------------------------------------------------------------------
# Module: LogWatch
# Authors: Magal Hezi, with CMS Made Simple Foundation.
# Copyright: (C) 2025 Pixel Solutions, info@pixelsolutions.biz
# License: GNU General Public License version 2
#          see /LogWatch/README.md or <http://www.gnu.org/licenses/gpl-2.0.html>
#---------------------------------------------------------------------------------------------------
# CMS Made Simple(TM) is (c) CMS Made Simple Foundation 2004-2020 (info@cmsmadesimple.org)
# Project's homepage is: http://www.cmsmadesimple.org
#---------------------------------------------------------------------------------------------------
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple. You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin
# section that the site was built with CMS Made simple.
#---------------------------------------------------------------------------------------------------
if (!isset($gCms)) exit;

if( version_compare($oldversion,'2.0.6') < 0 ) {
		$this->AddEventHandler('MAMS', 'OnLogin', false);
		$this->AddEventHandler('MAMS', 'OnLogout', false);
		$this->AddEventHandler('MAMS', 'OnExpireUser', false);
		$this->AddEventHandler('MAMS', 'OnCreateUser', false);
		$this->AddEventHandler('MAMS', 'OnUpdateUser', false);
		$this->AddEventHandler('MAMSRegistration', 'onUserRegistered', false);
}

if( version_compare($oldversion,'2.0.7') < 0 ) {
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
	} catch(\Exception $e) {
		audit('', $this->GetName(), 'Template creation error: ' . $e->getMessage());
	}
}

if( version_compare($oldversion,'2.0.8') < 0 ) {
	$this->CreatePermission(CMSMSStripe::PRODUCTS_PERM,'Manage Stripe Products');
	$this->CreatePermission(CMSMSStripe::TRANSACTIONS_PERM,'View Stripe Transactions');
	$this->CreatePermission(CMSMSStripe::SUBSCRIPTIONS_PERM,'Manage Stripe Subscriptions');
}

if( version_compare($oldversion,'2.0.9') < 0 ) {
	try {
		$uid = max(1, get_userid(FALSE));
		
		$payment_success_type = new \CmsLayoutTemplateType();
		$payment_success_type->set_originator($this->GetName());
		$payment_success_type->set_name('payment_success');
		$payment_success_type->set_dflt_flag(TRUE);
		$payment_success_type->set_lang_callback('CMSMSStripe::page_type_lang_callback');
		$payment_success_type->set_content_callback('CMSMSStripe::reset_page_type_defaults');
		$payment_success_type->reset_content_to_factory();
		$payment_success_type->save();
		
		$tpl = new CmsLayoutTemplate;
		$tpl->set_name($tpl::generate_unique_name('CMSMSStripe Payment Success'));
		$tpl->set_owner($uid);
		$tpl->set_type($payment_success_type);
		$tpl->set_content($payment_success_type->get_dflt_contents());
		$tpl->set_type_dflt(TRUE);
		$tpl->save();
	} catch(\Exception $e) {
		audit('', $this->GetName(), 'Template creation error: ' . $e->getMessage());
	}
}

if( version_compare($oldversion,'2.0.10') < 0 ) {
	$this->CreateEvent('SessionCompleted');
	$this->CreateEvent('PaymentCompleted');
	$this->CreateEvent('PaymentFailed');
	$this->CreateEvent('SubscriptionCreated');
	$this->CreateEvent('SubscriptionUpdated');
	$this->CreateEvent('SubscriptionExpired');
	$this->CreateEvent('RefundIssued');
	$this->CreateEvent('InvoicePaymentFailed');
	
	$db = $this->GetDb();
	$dict = NewDataDictionary($db);
	$taboptarray = array('mysql' => 'TYPE=MyISAM');
	$flds = "event_id C(255) KEY,
	         event_type C(100),
	         created_at I
	";
	$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_cmsmsstripe_events", $flds, $taboptarray);
	$dict->ExecuteSQLArray($sqlarray);
}

if( version_compare($oldversion,'2.0.11') < 0 ) {
	$db = $this->GetDb();
	$table_exists = $db->GetOne("SHOW TABLES LIKE '".cms_db_prefix()."module_cmsmsstripe_events'");
	if(!$table_exists) {
		$dict = NewDataDictionary($db);
		$taboptarray = array('mysql' => 'TYPE=MyISAM');
		$flds = "event_id C(255) KEY,
		         event_type C(100),
		         created_at I
		";
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_cmsmsstripe_events", $flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);
	}
}

if( version_compare($oldversion,'2.0.12') < 0 ) {
	$this->CreateEvent('SessionCompleted');
}

if ( version_compare($oldversion,'2.0.13') < 0 ) {

	$this->RemoveEvent('InvoicePaymentFailed'); // custom
	$this->RemoveEvent('PaymentCompleted'); // checkout.session.completed
	$this->RemoveEvent('PaymentFailed'); // payment_intent.succeeded
	$this->RemoveEvent('SessionCompleted'); // payment_intent.payment_failed
	$this->RemoveEvent('SessionCreated'); // customer.subscription.created
	$this->RemoveEvent('SubscriptionCreated'); // customer.subscription.updated
	$this->RemoveEvent('SubscriptionExpired'); // customer.subscription.deleted
	$this->RemoveEvent('SubscriptionUpdated'); // customer.subscription.paused

	$this->CreateEvent('SessionCreated'); // custom
	$this->CreateEvent('SessionCompleted'); // checkout.session.completed
	$this->CreateEvent('PaymentCompleted'); // payment_intent.succeeded
	$this->CreateEvent('PaymentFailed'); // payment_intent.payment_failed
	$this->CreateEvent('SubscriptionCreated'); // customer.subscription.created
	$this->CreateEvent('SubscriptionUpdated'); // customer.subscription.updated
	$this->CreateEvent('SubscriptionDeleted'); // customer.subscription.deleted
	$this->CreateEvent('SubscriptionPaused'); // customer.subscription.paused
	$this->CreateEvent('SubscriptionResumed'); // customer.subscription.resumed
	$this->CreateEvent('InvoicePaid'); // invoice.paid
	$this->CreateEvent('InvoicePaymentFailed'); // invoice.payment_failed
	$this->CreateEvent('InvoiceFinalized'); // invoice.finalized
	$this->CreateEvent('RefundIssued'); // charge.refunded
}

if( version_compare($oldversion,'2.0.14') < 0 ) {
	$db = $this->GetDb();
	$table_exists = $db->GetOne("SHOW TABLES LIKE '".cms_db_prefix()."module_cmsmsstripe_audit'");
	if(!$table_exists) {
		$dict = NewDataDictionary($db);
		$taboptarray = array('mysql' => 'TYPE=MyISAM');
		$flds = "id I KEY AUTO,
		         subscription_id C(255),
		         event_id C(255),
		         module_name C(100),
		         user_id I,
		         action C(255),
		         created_at I
		";
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_cmsmsstripe_audit", $flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);
	}
}

if( version_compare($oldversion,'2.0.15') < 0 ) {
	$this->CreateEvent('CheckoutSuccess');
}

?>