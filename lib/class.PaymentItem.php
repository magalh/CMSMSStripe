<?php
class PaymentItem
{

 private $_data = array('id'=>null,'order_id'=>null,'txn_id'=>null,'notes'=>null,'payment_date'=>null);

 public function __get($key)
 {
    switch( $key ) {
        case 'id':
        case 'order_id':
        case 'txn_id':
        case 'payment_date':
        case 'notes':
        return $this->_data[$key];
    }
 }
 public function __set($key,$val)
 {
    switch( $key ) {
        case 'order_id':
        case 'txn_id':
        case 'payment_date':
        case 'notes':
            $this->_data[$key] = trim($val);
        break;
    }
 }
 public function save()
 {
    if( !$this->is_valid() ) return FALSE;
    if( $this->id > 0 ) {
        $this->update();
    } else {
        $this->insert();
    }
 }

 public function is_valid()
 {
    if( !$this->txn_id ) return false;
    return TRUE;
 }
 protected function insert()
 {
    $db = \cms_utils::get_db();
    $sql = 'INSERT INTO '.CMS_DB_PREFIX.'module_cmsmsstripe_payments (order_id,txn_id,payment_date,line,notes)VALUES (?,?,?,?,?)';
    $dbr = $db->Execute($sql,array($this->order_id,$this->txn_id,date('Y-m-d H:i:s'),$this->line,$this->notes));
    if( !$dbr ) return FALSE;
    $this->_data['id'] = $db->Insert_ID();
    return TRUE;
 }
 protected function update()
 {
    $db = \cms_utils::get_db();
    $sql = 'UPDATE '.CMS_DB_PREFIX.'module_cmsmsstripe_payments SET order_id = ?, txn_id = ?, payment_date= ?, notes = ? WHERE id = ?';
    $dbr = $db->Execute($sql,array($this->order_id,$this->txn_id,$this->payment_date,$this->notes,$this->id));
    if( !$dbr ) return FALSE;
        return TRUE;
 }
 public function delete()
 {
    if( !$this->id ) return FALSE;
    $db = \cms_utils::get_db();
    $sql = 'DELETE FROM '.CMS_DB_PREFIX.'module_cmsmsstripe_payments WHERE id = ?';
    $dbr = $db->Execute($sql,array($this->id));
    if( !$dbr ) return FALSE;
    $this->_data['id'] = null;
    return TRUE;
 }
 /** internal */
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
    $sql = 'SELECT * FROM '.CMS_DB_PREFIX.'module_cmsmsstripe_payments WHERE id = ?';
    $row = $db->GetRow($sql,array($id));
    if( is_array($row) ) {
        $obj = new self();
        $obj->fill_from_array($row);
        return $obj;
    }
 }
}
?>