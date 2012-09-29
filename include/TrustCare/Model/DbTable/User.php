<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */


class TrustCare_Model_DbTable_User extends ZendX_Db_Table_Abstract
{
    protected $_name    = 'user';
    protected $_primary = 'id';
    
    
    public function insert(array $data)
    {
        $db = Zend_Registry::get("Storage")->getPersistantDb(); 
        $data['id'] = $db->nextSequenceId('user_id_seq');

        return parent::insert($data);
    }
    
    
    public function update(array $data, $where)
    {
        if(array_key_exists('login', $data)) {
            unset($data['login']);
        }
        return parent::update($data, $where);
    }
    
}