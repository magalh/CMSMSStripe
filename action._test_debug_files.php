<?php
if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission(CMSMSStripe::MANAGE_PERM) ) return;

$test_webhooks_dir = cms_join_path($this->GetModulePath(), 'test_webhooks');
$files = glob($test_webhooks_dir . '/*.json');

echo '<h3>Test Webhook Files</h3>';
echo '<ul>';
foreach($files as $file) {
	$filename = basename($file, '.json');
	$url = $this->CreateFrontendLink('cntnt01', $returnid, 'webhook', '', ['event' => $filename, 'skip_duplicate' => 1], '', true);
	echo '<li><a href="' . $url . '" target="_blank">' . $filename . '</a></li>';
}
echo '</ul>';
?>
