<?php
if( !defined('CMS_VERSION') ) exit;

try {
	$this->validate_config();
	$stripe = new \Stripe\StripeClient($this->GetPreference('cmsms_stripe_secret'));
	
	$product_ids = isset($params['products']) ? explode(',', trim($params['products'])) : [];
	
	if(!empty($product_ids)) {
		// Fetch specific products
		$products_data = [];
		foreach($product_ids as $product_id) {
			$product_id = trim($product_id);
			if($product_id) {
				try {
					$product = $stripe->products->retrieve($product_id);
					if($product->active) {
						$products_data[] = $product;
					}
				} catch(\Exception $e) {
					// Skip invalid product IDs
				}
			}
		}
	} else {
		// Fetch all active products
		$products = $stripe->products->all(['active' => true, 'limit' => 100]);
		$products_data = $products->data;
	}
	
	$current_url = \xt_url::current_url();
	$product_list = [];
	
	$currency_symbols = [
		'usd' => '$', 'eur' => '€', 'gbp' => '£', 'jpy' => '¥',
		'cad' => 'CA$', 'aud' => 'A$', 'chf' => 'CHF', 'cny' => '¥',
		'sek' => 'kr', 'nzd' => 'NZ$', 'inr' => '₹', 'brl' => 'R$'
	];
	
	foreach($products_data as $product) {
		if($product->default_price) {
			$price = $stripe->prices->retrieve($product->default_price);
			$currency_lower = strtolower($price->currency);
			$symbol = $currency_symbols[$currency_lower] ?? strtoupper($price->currency);
			
			$amount = $price->unit_amount / 100;
			$product->price_amount = ($amount == floor($amount)) ? number_format($amount, 0) : number_format($amount, 2);
			$product->price_formatted = $symbol . $product->price_amount;
			$product->price_amount = number_format($price->unit_amount / 100, 0);
			$product->currency_symbol = $symbol;
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
