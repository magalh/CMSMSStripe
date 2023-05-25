<?php

require 'vendor/autoload.php';
//require 'lib/class.smarty_plugins.php';

class CMSMSStripe extends CMSModule
{

	public function __construct()
	{
		spl_autoload_register( array($this, '_autoloader') );
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

	public static function validate_config()
    {
        $CMSMSExt = \xt_utils::get_xt();
		$mod = cge_utils::get_module('CMSMSStripe');
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
	
	public function make_templates(){
		$checkout_type = \CMSMSStripe\utils::create_template_type('simple_checkout', $this);
		$fn = __DIR__.'/templates/orig_simple_checkout.tpl';
		if ( is_file($fn) ) \CMSMSStripe\utils::create_template_of_type($checkout_type, 'Stripe Simple Checkout', file_get_contents($fn), true);

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
        case 'Card Form':
            $fn = 'orig_card_template.tpl';
            break;
        case 'Checkout':
            $fn = 'orig_checkout_template.tpl';
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

}

class EcommerceException extends CmsException {}

?>