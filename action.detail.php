<?php
if( !defined('CMS_VERSION') ) exit;

if(!isset($params['product_id'])) {
	echo '<p class="error">Product ID is required</p>';
	return;
}

try {
	$this->validate_config();
	$stripe = new \Stripe\StripeClient($this->GetPreference('cmsms_stripe_secret'));
	
	$product = $stripe->products->retrieve($params['product_id']);
	
	if($product->default_price) {
		$price = $stripe->prices->retrieve($product->default_price);
		$product->price_formatted = number_format($price->unit_amount / 100, 2) . ' ' . strtoupper($price->currency);
		$product->recurring = $price->type === 'recurring';
		$product->price_id = $product->default_price;
		if($product->recurring && isset($price->recurring->interval)) {
			$product->interval = $price->recurring->interval;
		}
	}
	
	$current_url = \xt_url::current_url();
	$checkout_url = $this->CreateLink($id, 'create-checkout-session', $returnid, '', ['price_id' => $product->price_id, 'returnto' => $current_url], '', true, false, '', true);
	
	$template = \CMSMSStripe\utils::find_layout_template($params, 'template', 'CMSMSStripe::product_detail');
	if(!$template) {
		$template = 'product_detail';
	}
	
	$returnto = $params['returnto'] ?? $this->CreateFrontendLink('cntnt01', $returnid, 'summary', '', [], '', true);
	
	$tpl = $smarty->CreateTemplate($this->GetTemplateResource($template), null, null, $smarty);
	$tpl->assign('product', $product);
	$tpl->assign('checkout_url', $checkout_url);
	$tpl->assign('returnto', $returnto);
	$tpl->display();
	
} catch(\Exception $e) {
	echo '<p class="error">Error loading product: ' . $e->getMessage() . '</p>';
}
?>
