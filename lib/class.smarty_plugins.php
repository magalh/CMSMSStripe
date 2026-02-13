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
        $smarty->register_function('get_charge_details',array('\\CMSMSStripe\\smarty_plugins','get_charge_details'));
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
    
    public static function get_charge_details($payment_intent)
    {

        if(!isset($payment_intent)) return null;
        
        $mod = \cms_utils::get_module('CMSMSStripe');
        $stripe = new \Stripe\StripeClient($mod->GetPreference('cmsms_stripe_secret'));
        
        $pi = $stripe->paymentIntents->retrieve($payment_intent, [
            'expand' => ['invoice.lines.data.price']
        ]);

        //print_r($pi);
        
        if(isset($pi->invoice)) {
            foreach($pi->invoice->lines->data as $line) {
                if(isset($line->price)) {
                    $product = $stripe->products->retrieve($line->price->product);
                    print_r($product);
                    return (object)[
                        'product' => $product,
                        'price' => $line->price
                    ];
                }
            }
        } elseif (isset($pi->payment_details->order_reference)) {
            $product_id = $pi->payment_details->order_reference;

            echo "Product ID: " . $product_id . "\n";
            // Retrieve the product
            $product = $stripe->products->retrieve($product_id);

            // Get the price information
            // Note: You need to find the correct price ID
            // Since multiple prices can be associated with a product
            $prices = $stripe->prices->all([
            'product' => $product_id,
            'limit' => 100
            ]);

            // Display product and price information
            echo "Product Name: " . $product->name . "\n";
            echo "Product Description: " . $product->description . "\n";

            foreach ($prices->data as $price) {
            echo "Price: " . ($price->unit_amount / 100) . " " . strtoupper($price->currency);
            if (isset($price->recurring)) {
                echo " / " . $price->recurring->interval;
            }
            echo "\n";
            }

        }
        
        return null;
    }
}