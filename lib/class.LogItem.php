<?php

namespace CMSMSStripe;

class LogItem
{
    private $_data = array('id'=>null,'subscription_id'=>null,'event_id'=>null,'module_name'=>null,'user_id'=>null,'action'=>null,'created_at'=>null);

    public function __get($key)
    {
        if( array_key_exists($key, $this->_data) ) return $this->_data[$key];
    }

    public function __set($key,$val)
    {
        switch( $key ) {
        case 'subscription_id':
        case 'event_id':
        case 'module_name':
        case 'action':
            $this->_data[$key] = trim($val);
            break;
        case 'user_id':
        case 'created_at':
            $this->_data[$key] = (int) $val;
            break;
        }
    }

    public function save()
    {
        if( $this->id > 0 ) {
            $this->update();
        } else {
            $this->insert();
        }
    }

    protected function insert()
    {
        $db = \cms_utils::get_db();
        $sql = 'INSERT INTO '.CMS_DB_PREFIX.'module_cmsmsstripe_audit 
                (subscription_id,event_id,module_name,user_id,action,created_at)
                VALUES (?,?,?,?,?,?)';
        $dbr = $db->Execute($sql,array($this->subscription_id,$this->event_id,$this->module_name,$this->user_id,$this->action,$this->created_at));
        if( !$dbr ) return FALSE;
        $this->_data['id'] = $db->Insert_ID();
        return TRUE;
    }

    protected function update()
    {
        $db = \cms_utils::get_db();
        $sql = 'UPDATE '.CMS_DB_PREFIX.'module_cmsmsstripe_audit SET subscription_id = ?, event_id = ?, 
                    module_name = ?, user_id = ?, action = ?, created_at = ? WHERE id = ?';
        $dbr = $db->Execute($sql,array($this->subscription_id,$this->event_id,$this->module_name,$this->user_id,$this->action,$this->created_at,$this->id));
        if( !$dbr ) return FALSE;
        return TRUE;
    }

    public function delete()
    {
        if( !$this->id ) return FALSE;
        $db = \cms_utils::get_db();
        $sql = 'DELETE FROM '.CMS_DB_PREFIX.'module_cmsmsstripe_audit WHERE id = ?';
        $dbr = $db->Execute($sql,array($this->id));
        if( !$dbr ) return FALSE;
        $this->_data['id'] = null;
        return TRUE;
    }

    public function fill_from_array($row)
    {
        foreach( $row as $key => $val ) {
            if( array_key_exists($key,$this->_data) ) {
                $this->_data[$key] = $val;
            }
        }
    }

    public static function &load_by_id($id)
    {
        $id = (int) $id;
        $db = \cms_utils::get_db();
        $sql = 'SELECT * FROM '.CMS_DB_PREFIX.'module_cmsmsstripe_audit WHERE id = ?';
        $row = $db->GetRow($sql,array($id));
        if( is_array($row) ) {
            $obj = new self();
            $obj->fill_from_array($row);
            return $obj;
        }
    }
}
