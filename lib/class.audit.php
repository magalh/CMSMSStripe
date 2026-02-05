<?php

namespace CMSMSStripe;

final class audit
{
    private function __construct() {}

    public static function log($subscription_id, $event_id, $module_name, $user_id, $action)
    {
        $db = \cmsms()->GetDb();
        
        $sql = "INSERT INTO " . cms_db_prefix() . "module_cmsmsstripe_audit 
                (subscription_id, event_id, module_name, user_id, action, created_at) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $db->Execute($sql, [
            $subscription_id,
            $event_id,
            $module_name,
            (int)$user_id,
            $action,
            time()
        ]);
    }
}
