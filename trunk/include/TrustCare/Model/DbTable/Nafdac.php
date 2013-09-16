<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */


class TrustCare_Model_DbTable_Nafdac extends ZendX_Db_Table_Abstract
{
    protected $_name    = 'nafdac';
    protected $_primary = 'id';
    
    protected $_metadata = array (
                'id' => 
                    array (
                      'SCHEMA_NAME' => NULL,
                      'TABLE_NAME' => 'nafdac',
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
        $data['id'] = $db->nextSequenceId('nafdac_id_seq');

        
        if(ZendX_Db_Table_Abstract::LABEL_NOW == $data['date_of_visit']) {
            $dateOfVisit = gmdate("Y-m-d");
        }
        else {
            $dateOfVisit = $data['date_of_visit'];
        }
        $data['date_of_visit'] = new Zend_Db_Expr(sprintf("str_to_date('%s', '%%Y-%%m-%%d')", $dateOfVisit));
        if(ZendX_Db_Table_Abstract::LABEL_NOW == $data['generation_date']) {
            $data['generation_date'] = new Zend_Db_Expr(sprintf("str_to_date('%s', '%%Y-%%m-%%d %%H:%%i:%%s')", gmdate("Y-m-d H:i:s")));
        }
        else {
            $data['generation_date'] = new Zend_Db_Expr(sprintf("str_to_date('%s', '%%Y-%%m-%%d %%H:%%i:%%s')", $data['generation_date']));
        }

        
        if (!array_key_exists('adr_start_date', $data) || empty($data['adr_start_date'])) {
            $data['adr_start_date'] = new Zend_Db_Expr('NULL');
        }
        else {
            if(ZendX_Db_Table_Abstract::LABEL_NOW == $data['adr_start_date']) {
                $data['adr_start_date'] = new Zend_Db_Expr(sprintf("str_to_date('%s', '%%Y-%%m-%%d')", gmdate("Y-m-d")));
            }
            else {
                $data['adr_start_date'] = new Zend_Db_Expr(sprintf("str_to_date('%s', '%%Y-%%m-%%d')", $data['adr_start_date']));
            }
        }
        if (!array_key_exists('adr_stop_date', $data) || empty($data['adr_stop_date'])) {
            $data['adr_stop_date'] = new Zend_Db_Expr('NULL');
        }
        else {
            if(ZendX_Db_Table_Abstract::LABEL_NOW == $data['adr_stop_date']) {
                $data['adr_stop_date'] = new Zend_Db_Expr(sprintf("str_to_date('%s', '%%Y-%%m-%%d')", gmdate("Y-m-d")));
            }
            else {
                $data['adr_stop_date'] = new Zend_Db_Expr(sprintf("str_to_date('%s', '%%Y-%%m-%%d')", $data['adr_stop_date']));
            }
        }
        
        return parent::insert($data);
    }
    
}
