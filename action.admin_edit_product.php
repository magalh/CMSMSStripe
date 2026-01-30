<?php
if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission(CMSMSStripe::MANAGE_PERM) ) return;

$this->validate_config();
$vars = $smarty->getTemplateVars('cmsms_stripe');
$stripe = new \Stripe\StripeClient($vars->secret);

$product_id = \xt_param::get_string($params, 'product_id');
$product = null;

if(isset($params['cancel'])) {
	$this->RedirectToAdminTab('products');
}

if(isset($params['submit'])) {
	try {
		$data = [
			'name' => \xt_param::get_string($params, 'name'),
			'description' => \xt_param::get_string($params, 'description'),
			'active' => \xt_param::get_bool($params, 'active', true)
		];
		
		$image_path = \xt_param::get_string($params, 'image');
		if($image_path) {
			$full_path = CMS_ROOT_PATH . '/uploads/' . ltrim($image_path, '/');
			if(file_exists($full_path)) {
				$fp = fopen($full_path, 'r');
				$file = $stripe->files->create([
					'purpose' => 'product_image',
					'file' => $fp
				]);
				fclose($fp);
				$file_link = $stripe->fileLinks->create(['file' => $file->id]);
				$data['images'] = [$file_link->url];
			}
		}
		
		$price = \xt_param::get_string($params, 'price');
		if($price && !$product_id) {
			$price_type = \xt_param::get_string($params, 'price_type', 'one_time');
			$data['default_price_data'] = [
				'currency' => strtolower($vars->currency_code),
				'unit_amount' => (int)($price * 100)
			];
			if($price_type === 'recurring') {
				$data['default_price_data']['recurring'] = [
					'interval' => \xt_param::get_string($params, 'interval', 'month')
				];
			}
		}
		
		if($product_id) {
			$stripe->products->update($product_id, $data);
			$this->SetMessage($this->Lang('product_updated'));
		} else {
			$stripe->products->create($data);
			$this->SetMessage($this->Lang('product_created'));
		}
		$this->RedirectToAdminTab('products');
	} catch(\Exception $e) {
		$this->DisplayErrorMessage($e->getMessage());
	}
}

if($product_id) {
	try {
		$product = $stripe->products->retrieve($product_id);
	} catch(\Exception $e) {
		$this->DisplayErrorMessage($e->getMessage());
		return;
	}
}

$tpl = $smarty->CreateTemplate($this->GetTemplateResource('admin_edit_product.tpl'), null, null, $smarty);
$tpl->assign('product', $product);
$tpl->assign('mod', $this);
$tpl->display();
?>
