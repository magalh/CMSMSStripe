<?php
if( !defined('CMS_VERSION') ) exit;

try {
	$this->validate_config();
	$stripe = new \Stripe\StripeClient($this->GetPreference('cmsms_stripe_secret'));
	
	$products = $stripe->products->all(['active' => true, 'limit' => 100]);
	
	$current_url = \xt_url::current_url();
	$product_list = [];
	foreach($products->data as $product) {
		if($product->default_price) {
			$price = $stripe->prices->retrieve($product->default_price);
			$product->price_formatted = number_format($price->unit_amount / 100, 2) . ' ' . strtoupper($price->currency);
			$product->recurring = $price->type === 'recurring';
			$product->price_id = $product->default_price;
			if($product->recurring && isset($price->recurring->interval)) {
				$product->interval = $price->recurring->interval;
			}
			$product->checkout_url = $this->CreateLink($id, 'create-checkout-session', $returnid, '', ['price_id' => $product->price_id, 'returnto' => $current_url], '', true, false, '', true);
		}
		$product_list[] = $product;
	}
	
	$template = \CMSMSStripe\utils::find_layout_template($params, 'template', 'CMSMSStripe::product_list');
	if(!$template) {
		$template = 'product_list';
	}
	
	$tpl = $smarty->CreateTemplate($this->GetTemplateResource($template), null, null, $smarty);
	$tpl->assign('products', $product_list);
	$tpl->assign('actionid', $id);
	$tpl->display();
	
} catch(\Exception $e) {
	echo '<p class="error">Error loading products: ' . $e->getMessage() . '</p>';
}
?>
