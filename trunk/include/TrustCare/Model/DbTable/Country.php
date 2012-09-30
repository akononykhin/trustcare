<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */


class TrustCare_Model_DbTable_Country extends ZendX_Db_Table_Abstract
{
    protected $_name    = 'country';
    protected $_primary = 'id';
    
    
    public function insert(array $data)
    {
        $db = Zend_Registry::get("Storage")->getPersistantDb(); 
        $data['id'] = $db->nextSequenceId('country_id_seq');

        return parent::insert($data);
    }
    
}
