<?php
if(!defined('CMS_VERSION')) exit;

$submitted = isset($params['submit']);
$email = isset($params['email']) ? trim($params['email']) : '';
$auto_redirect = isset($params['auto_redirect']) && $params['auto_redirect'] == '1';
$return_url = isset($params['returnto']) ? $params['returnto'] : CMS_ROOT_URL;

if($params['link']) {
	$this->validate_config();
	$stripe = new \Stripe\StripeClient($this->GetPreference('cmsms_stripe_secret'));
	$portal_session = $stripe->billingPortal->sessions->create([
		'customer' => $params['cid'],
		'return_url' => $return_url,
	]);
	$session_url = $portal_session->url;
	if ($params['email']) {
		$session_url .= '?prefilled_email=' . urlencode($params['email']);
	}
	header('Location: ' . $session_url);
	exit();
}

if(isset($params['cid']) && $params['print']) {
	$this->validate_config();
	$stripe = new \Stripe\StripeClient($this->GetPreference('cmsms_stripe_secret'));
	$portal_session = $stripe->billingPortal->sessions->create([
		'customer' => $params['sid'],
		'return_url' => $return_url,
	]);
	$smarty->assign(\trim($params['print']), $portal_session->url);
	return;
}

if($auto_redirect && $email) {
	try {
		$this->validate_config();
		$stripe = new \Stripe\StripeClient($this->GetPreference('cmsms_stripe_secret'));
		
		$customers = $stripe->customers->all(['email' => $email, 'limit' => 1]);
		
		if(!empty($customers->data)) {
			$customer = $customers->data[0];
			$portal_session = $stripe->billingPortal->sessions->create([
				'customer' => $customer->id,
				'return_url' => $return_url,
			]);
			
			header('Location: ' . $portal_session->url . '?prefilled_email=' . urlencode($email));
			exit();
		}
	} catch(\Exception $e) {
		// Silent fail, show form
	}
}

if($submitted && $email) {
	try {
		$this->validate_config();
		$stripe = new \Stripe\StripeClient($this->GetPreference('cmsms_stripe_secret'));
		
		// Find customer by email
		$customers = $stripe->customers->all(['email' => $email, 'limit' => 1]);
		
		if(empty($customers->data)) {
			$smarty->assign('error', 'No subscription found for this email address.');
		} else {
			$customer = $customers->data[0];
			
			// Create portal session
			$portal_session = $stripe->billingPortal->sessions->create([
				'customer' => $customer->id,
				'return_url' => $return_url,
			]);

			// Send email with magic link
			$subject = 'Access Your Subscription';
			$body = "<p>Click the link below to manage your subscription:</p>";
			$body .= "<p><a href='{$portal_session->url}'>Access Subscription Portal</a></p>";
			$body .= "<p>This link will expire in 24 hours.</p>";
			
			$mailer = new \cms_mailer();
			$mailer->AddAddress($email);
			$mailer->SetBody($body);
			$mailer->SetSubject($subject);
			$mailer->IsHTML(TRUE);
			$mailer->SetPriority(1);
			$mailer->Send();
			
			$smarty->assign('success', 'A magic link has been sent to your email address.');
		}
	} catch(\Exception $e) {
		$smarty->assign('error', 'Error: ' . $e->getMessage());
	}
}

$smarty->assign('email', $email);
$tpl = $smarty->CreateTemplate($this->GetTemplateResource('cms_template:CMSMSStripe Portal Access'), null, null, $smarty);
$tpl->display();
?>
