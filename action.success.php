<?php
if(!defined('CMS_VERSION')) exit;

$session_id = isset($params['session_id']) ? $params['session_id'] : '';

if(!$session_id) {
	echo '<p class="error">No session found.</p>';
	return;
}

try {
	$this->validate_config();
	$stripe = new \Stripe\StripeClient($this->GetPreference('cmsms_stripe_secret'));
	$session = $stripe->checkout->sessions->retrieve($session_id, ['expand' => ['line_items']]);
	
	$smarty->assign('session', $session);
	$smarty->assign('amount', number_format($session->amount_total / 100, 2));
	$smarty->assign('currency', strtoupper($session->currency));
	
	$template = \CMSMSStripe\utils::find_layout_template($params, 'template', 'CMSMSStripe::payment_success');
	if(!$template) {
		$template = 'success';
	}
	
	$tpl = $smarty->CreateTemplate($this->GetTemplateResource($template), null, null, $smarty);
	$tpl->display();
	
} catch(\Exception $e) {
	echo '<p class="error">Error: ' . $e->getMessage() . '</p>';
}
?>
