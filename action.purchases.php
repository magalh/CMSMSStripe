<?php
if(!defined('CMS_VERSION')) exit;

$stripe_customer_id = isset($params['stripe_customer_id']) ? $params['stripe_customer_id'] : '';
if(!$stripe_customer_id) throw new \Exception("Stripe customer ID is required");

try {
	$this->validate_config();
	$stripe = new \Stripe\StripeClient($this->GetPreference('cmsms_stripe_secret'));
	
	$all_purchases = [];
	
	// Get paid invoices (one-time purchases)
	$invoices = $stripe->invoices->all([
		'customer' => $stripe_customer_id,
		'status' => 'paid',
		'limit' => 100,
		'expand' => ['data.lines.data.price']
	]);

	$currency_symbols = [
		'usd' => '$', 'eur' => '€', 'gbp' => '£', 'jpy' => '¥',
		'cad' => 'CA$', 'aud' => 'A$', 'chf' => 'CHF', 'cny' => '¥',
		'sek' => 'kr', 'nzd' => 'NZ$', 'inr' => '₹', 'brl' => 'R$'
	];

	foreach($invoices->data as $invoice) {
		if(!$invoice->subscription) {
			foreach($invoice->lines->data as $line) {
				if(isset($line->price)) {
					$product = $stripe->products->retrieve($line->price->product);
					$all_purchases[] = (object)[
						'type' => 'invoice',
						'id' => $invoice->id,
						'created' => $invoice->created,
						'amount' => number_format($line->price->unit_amount / 100, 2),
						'currency' => ($currency_symbols[strtolower($invoice->currency)] ?? strtoupper($invoice->currency)),
						'status' => $invoice->status,
						'receipt_url' => $invoice->hosted_invoice_url,
						'description' => $line->description,
						'product_id' => $product->id,
						'product_name' => $product->name,
						'product_description' => $product->description,
						'product_image' => $product->images[0] ?? null,
						'price_id' => $line->price->id,
						'price_amount' => ($currency_symbols[strtolower($invoice->currency)] ?? strtoupper($invoice->currency)) . number_format($line->price->unit_amount / 100, 2) . ' per ' . $line->price->transform_quantity->divide_by,
						'invoice_pdf' => $invoice->invoice_pdf
					];
				}
			}
		}
	}

	//\xt_utils::send_ajax_and_exit( $all_purchases, true);
	
	$smarty->assign('has_purchases', count($all_purchases) > 0);
	$smarty->assignGlobal('purchases', $all_purchases);
	$smarty->assign('stripe_customer_id', $stripe_customer_id);
} catch(\Exception $e) {
	$smarty->assign('has_purchases', false);
	$smarty->assign('error', $e->getMessage());
}

$tpl = $smarty->CreateTemplate($this->GetTemplateResource('org_purchases.tpl'), null, null, $smarty);
$tpl->display();
?>
