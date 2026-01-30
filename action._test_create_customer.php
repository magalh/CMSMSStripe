<?php
if( !defined('CMS_VERSION') ) exit;

$user_id = \xt_param::get_int($params, 'user_id', 1);

$mams = \cms_utils::get_module('MAMS');
$uinfo = $mams->GetUserInfo($user_id);

if( !is_array($uinfo) || $uinfo[0] == FALSE ) {
	echo '<p>User not found</p>';
	return;
}

$user = $uinfo[1];

$test_params = [
	'id' => $user["id"],
	'username' => $user["username"]
];

//print_r($test_params);

$this->CreateCustomer($test_params);

echo '<p>CreateCustomer called for user ID: ' . $user_id . ' (' . $user["username"] . ')</p>';
echo '<p>Check error log for results</p>';
?>
