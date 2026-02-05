<?php

if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission(CMSMSStripe::MANAGE_PERM) ) return;

$query = new \CMSMSStripe\LogQuery(array('limit'=>100));
$logs = $query->GetMatches();

$tpl = $smarty->CreateTemplate( $this->GetTemplateResource('admin_activity_tab.tpl'), null, null, $smarty );
$tpl->assign('logs', $logs);
$tpl->display();



