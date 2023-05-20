<?php

class CMSMSStripe extends CMSModule
{

	const MANAGE_PERM = 'manage_cmsms_stripe';
	
	public function GetVersion() { return '1.0'; }
	public function GetFriendlyName() { return $this->Lang('friendlyname'); }
	public function GetAdminDescription() { return $this->Lang('admindescription'); }
	public function IsPluginModule() { return TRUE; }
	public function HasAdmin() { return TRUE; }
	public function VisibleToAdminUser() { return $this->CheckPermission(self::MANAGE_PERM); }
	public function GetAuthor() { return 'Magal Hezi'; }
	public function GetAuthorEmail() { return 'h_magal@hotmail.com'; }
	public function UninstallPreMessage() { return $this->Lang('ask_uninstall'); }
	public function GetAdminSection() { return 'ecommerce'; }
	
	public function InitializeFrontend() {
		$this->RegisterModulePlugin();
	}

	 public function InitializeAdmin() {
		 $this->SetParameters();
	 }
	
	public function GetHelp() { return @file_get_contents(__DIR__.'/doc/help.inc'); }
	public function GetChangeLog() { return @file_get_contents(__DIR__.'/doc/changelog.inc'); }

	public function checkSetup(){
		try {
			$stripe_secret = $this->GetPreference('stripe_secret');
			if (!$stripe_secret) {
				throw new \LogicException( 'Stripe Keys missing' );
			}
		}
		catch (Error $e) {
			$error = 1;	
			$output = $e->getMessage();
		}

	}

	/*---------------------------------------------------------
   AddItem()
   ---------------------------------------------------------*/
   public function AddItem($name,$number,$quantity,$weight,$amount,$tax = '') {
		$name = strip_tags($name);
		$name = html_entity_decode($name);
		$name = trim($name);
		if( !isset($this->_data['items']) ) {
			$this->_data['items'] = array();
		}
		
		if( !isset($this->_items[$name]) ) {
		$this->_data['items'][$name] = array('name'=>$name,
						'number'=>$number,
						'quantity'=>$quantity,
						'weight'=>$weight,
						'amount'=>$amount);
			if(!empty($tax)) {
				$this->_data['items'][$name]['tax'] = $tax;
			}
		}
	}

}

?>