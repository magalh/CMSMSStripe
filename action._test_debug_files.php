<?php
if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission(CMSMSStripe::MANAGE_PERM) ) return;

$this->validate_config();
$vars = $smarty->getTemplateVars('cmsms_stripe');

try {
	$stripe = new \Stripe\StripeClient($vars->secret);
	$files = $stripe->files->all(['limit' => 100]);
} catch(\Exception $e) {
	$this->DisplayErrorMessage($e->getMessage());
	return;
}

echo '<h3>Stripe Files</h3>';
echo '<table class="pagetable">';
echo '<thead><tr><th>Image</th><th>ID</th><th>Filename</th><th>Purpose</th><th>Size</th><th>Created</th><th>URL</th></tr></thead>';
echo '<tbody>';
foreach($files->data as $file) {
	echo '<tr>';
	echo '<td><img src="' . $file->url . '" style="max-width:50px;max-height:50px;"/></td>';
	echo '<td>' . $file->id . '</td>';
	echo '<td>' . $file->filename . '</td>';
	echo '<td>' . $file->purpose . '</td>';
	echo '<td>' . number_format($file->size) . ' bytes</td>';
	echo '<td>' . date('Y-m-d H:i:s', $file->created) . '</td>';
	echo '<td><a href="' . $file->url . '" target="_blank">View</a></td>';
	echo '</tr>';
}
echo '</tbody>';
echo '</table>';
?>
