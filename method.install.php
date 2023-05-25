<?php
if( !defined('CMS_VERSION') ) exit;
$this->CreatePermission(CMSMSStripe::MANAGE_PERM,'Manage CMSMSStripe');

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


?>