<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */


class TrustCare_Model_DbTable_Patient extends ZendX_Db_Table_Abstract
{
    protected $_name    = 'patient';
    protected $_primary = 'id';
    
    protected $_metadata = array (
                'id' => 
                    array (
                      'SCHEMA_NAME' => NULL,
                      'TABLE_NAME' => 'patient',
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
        $data['id'] = $db->nextSequenceId('patient_id_seq');

        if(empty($data['birthdate'])) {
            $data['birthdate'] = new Zend_Db_Expr('NULL');
        }
        else {
            $data['birthdate'] = new Zend_Db_Expr(sprintf("str_to_date('%s', '%%Y-%%m-%%d %%H:%%i:%%s')", $data['birthdate']));
        }

        return parent::insert($data);
        

        return parent::insert($data);
    }
    
    
    public function update(array $data, $where)
    {
        if(array_key_exists('birthdate', $data)) {
            if(empty($data['birthdate'])) {
                $data['birthdate'] = new Zend_Db_Expr('NULL');
            }
            else {
                $data['birthdate'] = new Zend_Db_Expr(sprintf("str_to_date('%s', '%%Y-%%m-%%d %%H:%%i:%%s')", $data['birthdate']));
            }
        }
        
        return parent::update($data, $where);
    }
    
}
