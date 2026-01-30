<?php
if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission(CMSMSStripe::MANAGE_PERM) ) return;

$this->validate_config();
$vars = $smarty->getTemplateVars('cmsms_stripe');

try {
	$stripe = new \Stripe\StripeClient($vars->secret);
	
	$file_path = __DIR__ . '/images/star-rating-sprite.png';
	
	if(!file_exists($file_path)) {
		throw new \Exception('File not found: ' . $file_path);
	}
	
	$fp = fopen($file_path, 'r');
	$file = $stripe->files->create([
		'purpose' => 'product_image',
		'file' => $fp
	]);
	fclose($fp);
	
	$file_link = $stripe->fileLinks->create(['file' => $file->id]);
	
	echo '<h3>File Upload Test</h3>';
	echo '<p><strong>Success!</strong></p>';
	echo '<p>File ID: ' . $file->id . '</p>';
	echo '<p>File Link URL: <a href="' . $file_link->url . '" target="_blank">' . $file_link->url . '</a></p>';
	echo '<p><img src="' . $file_link->url . '" style="max-width:200px;"/></p>';
	
} catch(\Exception $e) {
	$this->DisplayErrorMessage($e->getMessage());
}
?>
