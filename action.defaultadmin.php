<?php
if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission(CMSMSStripe::MANAGE_PERM) ) return;

use \CMSMSStripe\utils;
use \CMSMSStripe\smarty_plugins;

$this->validate_config();

echo $this->StartTabHeaders();
echo $this->SetTabHeader('products', "Products");
echo $this->SetTabHeader('logs', "Logs");
echo $this->SetTabHeader('settings',"Settings");
echo $this->EndTabHeaders();

echo $this->StartTabContent();
echo $this->StartTab('products');
include(__DIR__.'/function.admin_products_tab.php');
echo $this->EndTab();
echo $this->StartTab('logs');
//include(__DIR__.'/function.admin_logs_tab.php');
echo $this->EndTab();
echo $this->StartTab('settings');
include(__DIR__.'/function.admin_settings_tab.php');
echo $this->EndTab();
echo $this->EndTabContent();

?>