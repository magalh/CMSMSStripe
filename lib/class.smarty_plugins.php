<?php

namespace CMSMSStripe;

final class smarty_plugins
{
    private function __construct() {} // static class.. cannot be instantiated

    public static function init()
    {
        $smarty = \CmsApp::get_instance()->GetSmarty();
        $smarty->register_function('centsToDollars',array('\\CMSMSStripe\\smarty_plugins','centsToDollars'));
        $smarty->register_function('admin_status_icon',array('\\CMSMSStripe\\smarty_plugins','admin_status_icon'));
        $smarty->register_function('pix_admin_icon',array('\\CMSMSStripe\\smarty_plugins','pix_admin_icon'));
    }

    public static function centsToDollars($params,&$smarty){
        return number_format(($params['cents'] /100), 2, '.', ' ');
    }

    public static function admin_status_icon($params){	
		
		switch($params['status']){
				case 'requires_payment_method':
            		$data = array('simple-icon-clock','warning','Incomplete','');
					break;
				case 'requires_action':
            		$data = array('simple-icon-check','warning','Incomplete','');
					break;
                case 'succeeded':
                    $data = array('simple-icon-check','success','Succeeded','');
                    break;
                case 'declined':
                    $data = array('simple-icon-exclamation','danger','Failed',$params['message']);
                    break;
				default:
                    $data = array('simple-icon-info','danger','other',$params['message']);
					break;
		}

        return self::load_icon($data);
	}

    public static function load_icon($params) {
        $out = '<div class="btn btn-xs btn-'.$params[1].'">'.$params[2];
        $out .= " <i class=\"{$params[0]}\" title=\"{$params[3]}\"></i>";
        $out .= '</div>';
        
        return $out;
    }

  public static function pix_admin_icon($params,&$smarty)
    {

        if($params['img']){
            $fnd = \cms_admin_utils::get_icon($params['img']);
            if( !$fnd ) return;
            if( !isset($params['alt']) ) $params['alt'] = basename($fnd);

            $out = "<img src=\"{$fnd}\"";
            foreach( $params as $key => $value ) {
                $out .= " $key=\"$value\"";
            }
            $out .= '/>';
        }
        
        if($params['icon']){
            $out = "<i class=\"{$params['icon']}\"";
            foreach( $params as $key => $value ) {
                $out .= " $key=\"$value\"";
            }
            $out .= '></i>';
        }
        
        return $out;
    }
    
}