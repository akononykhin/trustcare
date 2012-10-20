<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */


class TrustCare_Model_DbTable_Pharmacy extends ZendX_Db_Table_Abstract
{
    protected $_name    = 'pharmacy';
    protected $_primary = 'id';
    
    protected $_metadata = array (
                'id' => 
                    array (
                      'SCHEMA_NAME' => NULL,
                      'TABLE_NAME' => 'pharmacy',
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
        $data['id'] = $db->nextSequenceId('pharmacy_id_seq');

        if(array_key_exists('id_country', $data) && empty($data['id_country'])) {
            $data['id_country'] = new Zend_Db_Expr('NULL');
        }
        if(array_key_exists('id_state', $data) && empty($data['id_state'])) {
            $data['id_state'] = new Zend_Db_Expr('NULL');
        }
        if(array_key_exists('id_lga', $data) && empty($data['id_lga'])) {
            $data['id_lga'] = new Zend_Db_Expr('NULL');
        }
        if(array_key_exists('id_facility', $data) && empty($data['id_facility'])) {
            $data['id_facility'] = new Zend_Db_Expr('NULL');
        }

        return parent::insert($data);
    }
    
    
    public function update(array $data, $where)
    {
        if(array_key_exists('id_country', $data) && empty($data['id_country'])) {
            $data['id_country'] = new Zend_Db_Expr('NULL');
        }
        if(array_key_exists('id_state', $data) && empty($data['id_state'])) {
            $data['id_state'] = new Zend_Db_Expr('NULL');
        }
        if(array_key_exists('id_lga', $data) && empty($data['id_lga'])) {
            $data['id_lga'] = new Zend_Db_Expr('NULL');
        }
        if(array_key_exists('id_facility', $data) && empty($data['id_facility'])) {
            $data['id_facility'] = new Zend_Db_Expr('NULL');
        }
        return parent::update($data, $where);
    }
    
}
