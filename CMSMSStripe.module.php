<?php

class CMSMSStripe extends CMSModule
{

	public function __construct()
    {
        $autoload_file = cms_join_path($this->GetModulePath(), 'vendor', 'autoload.php');
        if (file_exists($autoload_file)) {
            require_once $autoload_file;
        }
        
        spl_autoload_register([$this, '_autoloader']);
        parent::__construct();
    }

	private final function _autoloader($classname)
	{
		$parts = explode('\\', $classname);
		$classname = end($parts);
		
		$fn = cms_join_path($this->GetModulePath(),'lib','class.' . $classname . '.php');
		
		if(file_exists($fn))
		{
			require_once($fn);
		}
	}

	const MANAGE_PERM = 'manage_cmsms_stripe';
	const PRODUCTS_PERM = 'manage_stripe_products';
	const TRANSACTIONS_PERM = 'view_stripe_transactions';
	const SUBSCRIPTIONS_PERM = 'manage_stripe_subscriptions';
	
	public function GetVersion() { return '2.0.9'; }
	public function MinimumCMSVersion() {
        return '2.2.16';
    }
	public function GetFriendlyName() { return $this->Lang('friendlyname'); }
	public function GetAdminDescription() { return $this->Lang('admindescription'); }
	public function IsPluginModule() { return TRUE; }
	public function HasAdmin() { return TRUE; }
	public function VisibleToAdminUser() { return $this->CheckPermission(self::MANAGE_PERM); }
    public function GetAuthor() { return 'Magal Hezi'; }
    public function GetAuthorEmail() { return 'magal@pixelsolutions.biz'; }
	public function UninstallPreMessage() { return $this->Lang('ask_uninstall'); }
	public function GetAdminSection() { return 'siteadmin'; }
	public function GetDependencies(){ return ['CMSMSExt' => '1.4.3']; }

	public function InitializeFrontend() {
		$this->RegisterModulePlugin();
	}
	

	public function InitializeAdmin() {
		\CMSMSStripe\smarty_plugins::init();
		 $this->SetParameters();
	 }

	public function GetHeaderHTML()
    {
		return $this->_output_header_javascript();
    }

	protected function _output_header_javascript()
    {
		$fn = $this->GetModulePath();
		$fn = str_replace(CMS_ROOT_PATH,CMS_ROOT_URL,$fn);
		$out = '<script src="'.$fn.'/javascript/common.js"></script>'."\n";
		$out .= '<link rel="stylesheet" href="'.$fn.'/css/common.css"/>'."\n";
        $out .= '<link rel="stylesheet" href="'.$fn.'/font/iconsmind-s/css/iconsminds.css"/>'."\n";
		$out .= '<link rel="stylesheet" href="'.$fn.'/font/simple-line-icons/css/simple-line-icons.css"/>'."\n";
        return $out;
    }

	public function GetHelp() { return @file_get_contents(__DIR__.'/doc/help.inc'); }
	public function GetChangeLog() { return @file_get_contents(__DIR__.'/doc/changelog.inc'); }

	public function SetParameters()
    {
        $this->RestrictUnknownParams();
        $this->RegisterModulePlugin();
        $this->AddImageDir('icons');
	
    }

	public function GetAdminMenuItems()
	{
		$out = [];

		if($this->CheckPermission(self::MANAGE_PERM))
		{
			$obj = new CmsAdminMenuItem();
			$obj->module = $this->GetName();
			$obj->section = 'siteadmin';
			$obj->title = 'Stripe Settings';
			$obj->description = 'Manage Stripe Module Settings';
			$obj->action = 'defaultadmin';
			$obj->url = $this->create_url('m1_', $obj->action);
			$out[] = $obj;
		}
		
		if($this->CheckPermission(self::PRODUCTS_PERM))
		{
			$obj = new CmsAdminMenuItem();
			$obj->module = $this->GetName();
			$obj->section = 'ecommerce';
			$obj->title = 'Product Catalog';
			$obj->description = 'Manage Stripe Products';
			$obj->action = 'admin_products';
			$obj->url = $this->create_url('m1_', $obj->action);
			$out[] = $obj;
		}
		
		if($this->CheckPermission(self::TRANSACTIONS_PERM))
		{
			$obj = new CmsAdminMenuItem();
			$obj->module = $this->GetName();
			$obj->section = 'ecommerce';
			$obj->title = 'Transactions';
			$obj->description = 'View Stripe Transactions';
			$obj->action = 'admin_transactions';
			$obj->url = $this->create_url('m1_', $obj->action);
			$out[] = $obj;
		}
		
		if($this->CheckPermission(self::SUBSCRIPTIONS_PERM))
		{
			$obj = new CmsAdminMenuItem();
			$obj->module = $this->GetName();
			$obj->section = 'ecommerce';
			$obj->title = 'Subscriptions';
			$obj->description = 'Manage Stripe Subscriptions';
			$obj->action = 'admin_subscriptions';
			$obj->url = $this->create_url('m1_', $obj->action);
			$out[] = $obj;
		}
		
		return $out;
	}

	public static function validate_config()
    {
        $CMSMSExt = \xt_utils::get_xt();
		$mod = xt_utils::get_module('CMSMSStripe');
		try {
			if(!$mod->GetPreference('cmsms_stripe_secret')) throw new \RuntimeException( 'Stripe Keys missing' );
			if(!$mod->GetPreference('cmsms_stripe_publishable_key')) throw new \RuntimeException('Stripe Keys missing');
			if(!$mod->GetPreference('cmsms_stripe_currency_code')) throw new \RuntimeException($mod->Lang('err_config_currency'));
		}
		catch (\Exception $e) {
			$mod->DisplayErrorMessage($e->GetMessage(),'pageerrorcontainer');
		}

		$smarty = cmsms()->GetSmarty();
		$list = $mod->ListPreferencesByPrefix("cmsms_stripe_");
		$profiles = array();
		foreach( $list as $one ) {
			$profiles[$one] = $mod->GetPreference('cmsms_stripe_'.$one);
		}
		$smarty->assign('cmsms_stripe',(object)$profiles);

	}

    public function RegisterEvents()
    {
		$this->AddEventHandler('MAMS', 'OnLogin', false);
		$this->AddEventHandler('MAMS', 'OnLogout', false);
		$this->AddEventHandler('MAMS', 'OnExpireUser', false);
		$this->AddEventHandler('MAMS', 'OnCreateUser', false);
		$this->AddEventHandler('MAMS', 'OnUpdateUser', false);
		$this->AddEventHandler('MAMSRegistration', 'onUserRegistered', false);
		$this->CreateEvent('StripePaymentCompleted');
    }

	function DoEvent($originator, $eventname, &$params)
    {
		//error_log('DoEvent called - Originator: '.$originator.', Event: '.$eventname);
        if ($originator == 'MAMSRegistration' && $eventname == 'onUserRegistered') {
			error_log('CMSMSStripe: onUserRegistered event triggered for user ID '.$params['id']);
            $this->CreateCustomer($params);
        }
    }

	public function CreateCustomer($params)
	{
		try {
			$this->validate_config();
			$stripe = new \Stripe\StripeClient($this->GetPreference('cmsms_stripe_secret'));
			
			$existing = $stripe->customers->search(['query' => 'email:"' . $params['username'] . '"']);
			
			if ($existing->data && count($existing->data) > 0) {
				$customer = $existing->data[0];
				$stripe->customers->update($customer->id, ['metadata' => ['mams_user_id' => $params['id']]]);
				audit('', 'CMSMSStripe', 'Reused existing Stripe customer ' . $customer->id . ' for user ' . $params['id']);
			} else {
				$customer = $stripe->customers->create([
					'email' => $params['username'],
					'metadata' => ['mams_user_id' => $params['id']]
				]);
				audit('', 'CMSMSStripe', 'Created new Stripe customer ' . $customer->id . ' for user ' . $params['id']);
			}
			
			$mams = \cms_utils::get_module('MAMS');
			$gid = $mams->GetGroupID('stripe');
			if ($gid < 1) {
				$mams->AddGroup('stripe', 'Stripe Integration');
				$gid = $mams->GetGroupID('stripe');
				$propDefn = $mams->GetPropertyDefn('stripe_customer_id');
				if (!$propDefn) {
					$mams->AddPropertyDefn('stripe_customer_id', 'Stripe Customer ID', 0, 80, 255, 'a:0:{}', 0, 0);
				}
				if($gid > 0) {
					$mams->AddGroupPropertyRelation($gid, 'stripe_customer_id', 0, -1, 0);
				}
				audit('', 'CMSMSStripe', 'Created MAMS stripe group and property');
			}
			
			if($gid > 0) {
				$mams->AssignUserToGroup($params['id'], $gid);
			}
			
			$mams->SetUserProperty($params['id'], 'stripe_customer_id', $customer->id);
			audit('', 'CMSMSStripe', 'Linked MAMS user ' . $params['id'] . ' to Stripe customer ' . $customer->id);
			
		} catch(\Exception $e) {
			audit('', 'CMSMSStripe', 'Error creating Stripe customer: ' . $e->getMessage());
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

	public function DisplayErrorMessage(string $txt, string $class = 'alert alert-danger')
	{
	  $smarty = cmsms()->GetSmarty();
	  $tpl = $smarty->CreateTemplate($this->GetTemplateResource('error.tpl'),null,null,$smarty);
	  $tpl->assign('xt_errorclass', $class);
	  $tpl->assign('xt_errormsg', $txt);
	  $tpl->display();
	}
	
	public static function page_type_lang_callback($str)
    {
        $mod = cms_utils::get_module('CMSMSStripe');
        if( is_object($mod) ) return $mod->Lang('type_'.$str);
    }

	public static function reset_page_type_defaults(CmsLayoutTemplateType $type)
    {
        $mod = cms_utils::get_module('CMSMSStripe');
        if( $type->get_originator() != $mod->GetName() ) throw new CmsLogicException('Cannot reset contents for this template type');

        $fn = null;
        switch( $type->get_name() ) {
        case 'product_list':
            $fn = 'orig_product_list.tpl';
            break;
        case 'product_detail':
            $fn = 'orig_product_detail.tpl';
            break;
        case 'payment_success':
            $fn = 'orig_payment_success.tpl';
            break;
        }

        if( !$fn ) return;
        $fn = __DIR__.'/templates/'.$fn;
        if( file_exists($fn) ) return @file_get_contents($fn);
    }

	public static function template_help_callback($str)
    {
        $str = trim($str);
        $mod = cms_utils::get_module('CMSMSStripe');
        if( is_object($mod) ) {
            $file = $mod->GetModulePath().'/doc/tpltype_'.$str.'.inc';
            if( is_file($file) ) return file_get_contents($file);
        }
    }

	public function HandleCheckoutCompleted($session)
	{
		$this->validate_config();
		$stripe = new \Stripe\StripeClient($this->GetPreference('cmsms_stripe_secret'));
		
		$session_full = $stripe->checkout->sessions->retrieve($session->id, ['expand' => ['line_items', 'customer']]);
		
		audit('', 'CMSMSStripe', 'Payment completed - Session: ' . $session->id . ', Amount: ' . ($session->amount_total / 100) . ', Customer: ' . $session->customer);
		
		$this->SendEvent('StripePaymentCompleted', [
			'session' => $session_full,
			'customer_id' => $session->customer,
			'amount' => $session->amount_total / 100,
			'payment_intent' => $session->payment_intent
		]);
	}

	public function HandlePaymentSucceeded($payment_intent)
	{
		audit('', 'CMSMSStripe', 'Payment intent succeeded: ' . $payment_intent->id . ', Amount: ' . ($payment_intent->amount / 100));
	}

}

class EcommerceException extends CmsException {}

?>