<?php
if( !defined('CMS_VERSION') ) exit;

try {
	$this->validate_config();
	$stripe = new \Stripe\StripeClient($this->GetPreference('cmsms_stripe_secret'));
	
	$product_ids = isset($params['products']) ? explode(',', trim($params['products'])) : [];
	$category_filter = isset($params['category']) ? trim($params['category']) : null;
	
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
		// Filter by category if specified
		if($category_filter && (!isset($product->metadata->category) || $product->metadata->category !== $category_filter)) {
			continue;
		}
		
		// Fetch all active prices for this product
		$prices = $stripe->prices->all(['product' => $product->id, 'active' => true, 'limit' => 100]);
		
		$product->prices = [];
		$product->has_multiple_prices = count($prices->data) > 1;
		
		foreach($prices->data as $price) {
			$currency_lower = strtolower($price->currency);
			$symbol = $currency_symbols[$currency_lower] ?? strtoupper($price->currency);
			
			$amount = $price->unit_amount / 100;
			$price_amount = ($amount == floor($amount)) ? number_format($amount, 0) : number_format($amount, 2);
			
			// Split price into integer and decimal parts
			$price_parts = explode('.', $price_amount);
			$price_integer = $price_parts[0];
			$price_decimal = isset($price_parts[1]) ? $price_parts[1] : null;
			
			$price_data = [
				'id' => $price->id,
				'nickname' => $price->nickname ?? null,
				'metadata' => $price->metadata ?? null,
				'amount' => $price_amount,
				'amount_raw' => $amount,
				'amount_integer' => $price_integer,
				'amount_decimal' => $price_decimal,
				'formatted' => $symbol . $price_amount,
				'symbol' => $symbol,
				'currency' => $price->currency,
				'type' => $price->type,
				'recurring' => $price->type === 'recurring',
				'interval' => $price->type === 'recurring' ? $price->recurring->interval : null,
				'interval_count' => $price->type === 'recurring' ? $price->recurring->interval_count : null,
				'checkout_url' => $this->CreateLink($id, 'create-checkout-session', $returnid, '', ['price_id' => $price->id, 'returnto' => $current_url], '', true, false, '', true)
			];
			
			$product->prices[] = (object)$price_data;
		}
		
		// Sort prices: recurring first, then by amount ascending
		usort($product->prices, function($a, $b) {
			if($a->recurring != $b->recurring) {
				return $b->recurring - $a->recurring; // recurring first
			}
			return $a->amount_raw <=> $b->amount_raw; // then by amount
		});
		
		$product_list[] = $product;
	}

	//\xt_utils::send_ajax_and_exit( $product_list, true);
	
	$template = \CMSMSStripe\utils::find_layout_template($params, 'template', 'CMSMSStripe::product_list');
	if(!$template) {
		$template = 'product_list';
	}

	$tpl = $smarty->CreateTemplate($this->GetTemplateResource($template), null, null, $smarty);
	$tpl->assign('products', $product_list);
	$tpl->assign('actionid', $id);
	$tpl->assign('highlight', $params['highlight'] ?? null);
	$tpl->display();
	
} catch(\Exception $e) {
	echo '<p class="error">Error loading products: ' . $e->getMessage() . '</p>';
}
?>
