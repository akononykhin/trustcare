<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */


class TrustCare_Model_DbTable_LogObjects extends ZendX_Db_Table_Abstract
{
    protected $_name    = 'log_objects';
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
        if(!array_key_exists('timestamp', $data) || empty($data['timestamp'])) {
            $data['timestamp'] = new Zend_Db_Expr(sprintf("str_to_date('%s', '%%Y-%%m-%%d %%H:%%i:%%s')", gmdate("Y-m-d H:i:s")));
        }
        
        return parent::insert($data);
    }
}
