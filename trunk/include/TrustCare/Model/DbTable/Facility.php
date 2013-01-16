<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */


class TrustCare_Model_DbTable_Facility extends ZendX_Db_Table_Abstract
{
    protected $_name    = 'facility';
    protected $_primary = 'id';
    
    protected $_metadata = array (
                'id' => 
                    array (
                      'SCHEMA_NAME' => NULL,
                      'TABLE_NAME' => 'facility',
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
        $data['id'] = $db->nextSequenceId('facility_id_seq');

        if(array_key_exists('id_lga', $data) && empty($data['id_lga'])) {
            $data['id_lga'] = new Zend_Db_Expr('NULL');
        }
        if(array_key_exists('id_facility_type', $data) && empty($data['id_facility_type'])) {
            $data['id_facility_type'] = new Zend_Db_Expr('NULL');
        }
        if(array_key_exists('id_facility_level', $data) && empty($data['id_facility_level'])) {
            $data['id_facility_level'] = new Zend_Db_Expr('NULL');
        }
        
        return parent::insert($data);
    }
    
    
    public function update(array $data, $where)
    {
        if(array_key_exists('id_lga', $data) && empty($data['id_lga'])) {
            $data['id_lga'] = new Zend_Db_Expr('NULL');
        }
        if(array_key_exists('id_facility_type', $data) && empty($data['id_facility_type'])) {
            $data['id_facility_type'] = new Zend_Db_Expr('NULL');
        }
        if(array_key_exists('id_facility_level', $data) && empty($data['id_facility_level'])) {
            $data['id_facility_level'] = new Zend_Db_Expr('NULL');
        }
        
        return parent::update($data, $where);
    }
    
    
}
