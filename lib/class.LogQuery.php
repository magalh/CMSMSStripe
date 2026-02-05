<?php

namespace CMSMSStripe;

class LogQuery extends \CmsDbQueryBase
{
    private $_filters = array();

    public function __construct($args = '')
    {
        parent::__construct($args);
        if( isset($this->_args['limit']) ) $this->_limit = (int) $this->_args['limit'];
        if( is_array($args) ) $this->_filters = $args;
    }

    public function execute()
    {
        if( !is_null($this->_rs) ) return;
        $sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM '.CMS_DB_PREFIX.'module_cmsmsstripe_audit WHERE 1=1';
        $params = array();

        if( isset($this->_filters['subscription_id']) ) {
            $sql .= ' AND subscription_id = ?';
            $params[] = $this->_filters['subscription_id'];
        }
        if( isset($this->_filters['event_id']) ) {
            $sql .= ' AND event_id = ?';
            $params[] = $this->_filters['event_id'];
        }
        if( isset($this->_filters['module_name']) ) {
            $sql .= ' AND module_name = ?';
            $params[] = $this->_filters['module_name'];
        }
        if( isset($this->_filters['user_id']) ) {
            $sql .= ' AND user_id = ?';
            $params[] = $this->_filters['user_id'];
        }

        $sql .= ' ORDER BY id DESC';
        $db = \cms_utils::get_db();
        $this->_rs = $db->SelectLimit($sql,$this->_limit,$this->_offset,$params);
        if( $db->ErrorMsg() ) throw new \CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
        $this->_totalmatchingrows = $db->GetOne('SELECT FOUND_ROWS()');  
    }

    public function &GetObject()
    {
        $obj = new LogItem;
        $obj->fill_from_array($this->fields);
        return $obj;
    }
}
