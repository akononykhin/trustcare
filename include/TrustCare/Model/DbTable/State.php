<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */


class TrustCare_Model_DbTable_State extends ZendX_Db_Table_Abstract
{
    protected $_name    = 'state';
    protected $_primary = 'id';
    
    protected $_metadata = array (
                'id' => 
                    array (
                      'SCHEMA_NAME' => NULL,
                      'TABLE_NAME' => 'state',
                      'COLUMN_NAME' => 'id',
                      'COLUMN_POSITION' => 1,
                      'DATA_TYPE' => 'int',
                      'DEFAULT' => NULL,
                      'NULLABLE' => false,
                      'LENGTH' => NULL,
                      'SCALE' => NULL,
                      'PRECISION' => NULL,
                      'UNSIGNED' => NULL,
                      'PRIMARY' => true,
                      'PRIMARY_POSITION' => 1,
                      'IDENTITY' => false,
                    ),
              );
    
    
    public function insert(array $data)
    {
        $db = Zend_Registry::get("Storage")->getPersistantDb(); 
        $data['id'] = $db->nextSequenceId('state_id_seq');

        if(array_key_exists('id_country', $data) && empty($data['id_country'])) {
            $data['id_country'] = new Zend_Db_Expr('NULL');
        }

        return parent::insert($data);
    }
    
    public function update(array $data, $where)
    {
        if(array_key_exists('id_country', $data) && empty($data['id_country'])) {
            $data['id_country'] = new Zend_Db_Expr('NULL');
        }
        
        return parent::update($data, $where);
    }
    
}
