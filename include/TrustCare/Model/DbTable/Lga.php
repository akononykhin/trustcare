<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */


class TrustCare_Model_DbTable_Lga extends ZendX_Db_Table_Abstract
{
    protected $_name    = 'lga';
    protected $_primary = 'id';
    
    protected $_metadata = array (
                'id' => 
                    array (
                      'SCHEMA_NAME' => NULL,
                      'TABLE_NAME' => 'lga',
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
        $data['id'] = $db->nextSequenceId('lga_id_seq');

        if(array_key_exists('id_state', $data) && empty($data['id_state'])) {
            $data['id_state'] = new Zend_Db_Expr('NULL');
        }
        
        return parent::insert($data);
    }
    
    
    public function update(array $data, $where)
    {
        if(array_key_exists('id_state', $data) && empty($data['id_state'])) {
            $data['id_state'] = new Zend_Db_Expr('NULL');
        }
    
        return parent::update($data, $where);
    }
    
}
