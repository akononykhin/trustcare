<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */


class TrustCare_Model_DbTable_FrmCommunity extends ZendX_Db_Table_Abstract
{
    protected $_name    = 'frm_community';
    protected $_primary = 'id';
    
    protected $_metadata = array (
                'id' => 
                    array (
                      'SCHEMA_NAME' => NULL,
                      'TABLE_NAME' => 'frm_community',
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
        $data['id'] = $db->nextSequenceId('frm_community_id_seq');

        if(ZendX_Db_Table_Abstract::LABEL_NOW == $data['generation_date']) {
            $data['generation_date'] = new Zend_Db_Expr(sprintf("str_to_date('%s', '%%Y-%%m-%%d %%H:%%i:%%s')", gmdate("Y-m-d H:i:s")));
        }
        else {
            $data['generation_date'] = new Zend_Db_Expr(sprintf("str_to_date('%s', '%%Y-%%m-%%d %%H:%%i:%%s')", $data['generation_date']));
        }
        
        if(ZendX_Db_Table_Abstract::LABEL_NOW == $data['date_of_visit']) {
            $dateOfVisit = gmdate("Y-m-d");
        }
        else {
            $dateOfVisit = $data['date_of_visit'];
        }
        $data['date_of_visit'] = new Zend_Db_Expr(sprintf("str_to_date('%s', '%%Y-%%m-%%d')", $dateOfVisit));
        if(!preg_match('/^(\d{4})-(\d{2})-\d{2}$/', $dateOfVisit, $matches)) {
            throw new Exception(sprintf("Incorrect format of date_of_visit: %s", $dateOfVisit));
        }
        $data['date_of_visit_month_index'] = $matches[1].$matches[2];
        
        if(array_key_exists('htc_result_id', $data) && empty($data['htc_result_id'])) {
            $data['htc_result_id'] = new Zend_Db_Expr('NULL');
        }
        if(!array_key_exists('id_nafdac', $data) || empty($data['id_nafdac'])) {
            $data['id_nafdac'] = new Zend_Db_Expr('NULL');
        }
        if(!array_key_exists('hiv_status', $data) || empty($data['hiv_status'])) {
            $data['hiv_status'] = new Zend_Db_Expr('NULL');
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
    
    
    public function update(array $data, $where)
    {
        if(array_key_exists('date_of_visit', $data)) {
            if(ZendX_Db_Table_Abstract::LABEL_NOW == $data['date_of_visit']) {
                $dateOfVisit = gmdate("Y-m-d");
            }
            else {
                $dateOfVisit = $data['date_of_visit'];
            }
            $data['date_of_visit'] = new Zend_Db_Expr(sprintf("str_to_date('%s', '%%Y-%%m-%%d')", $dateOfVisit));
            if(!preg_match('/^(\d{4})-(\d{2})-\d{2}$/', $dateOfVisit, $matches)) {
                throw new Exception(sprintf("Incorrect format of date_of_visit: %s", $dateOfVisit));
            }
            $data['date_of_visit_month_index'] = $matches[1].$matches[2];
        }
        
        if(array_key_exists('htc_result_id', $data) && empty($data['htc_result_id'])) {
            $data['htc_result_id'] = new Zend_Db_Expr('NULL');
        }
        if(array_key_exists('id_nafdac', $data) && empty($data['id_nafdac'])) {
            $data['id_nafdac'] = new Zend_Db_Expr('NULL');
        }
        if(array_key_exists('hiv_status', $data) && empty($data['hiv_status'])) {
            $data['hiv_status'] = new Zend_Db_Expr('NULL');
        }
        if(array_key_exists('adr_start_date', $data)) {
            if(empty($data['adr_start_date'])) {
                $data['adr_start_date'] = new Zend_Db_Expr('NULL');
            }
            else if(ZendX_Db_Table_Abstract::LABEL_NOW == $data['adr_start_date']) {
                $data['adr_start_date'] = new Zend_Db_Expr(sprintf("str_to_date('%s', '%%Y-%%m-%%d')", gmdate("Y-m-d")));
            }
            else {
                $data['adr_start_date'] = new Zend_Db_Expr(sprintf("str_to_date('%s', '%%Y-%%m-%%d')", $data['adr_start_date']));
            }
        }
        if(array_key_exists('adr_stop_date', $data)) {
            if(empty($data['adr_stop_date'])) {
                $data['adr_stop_date'] = new Zend_Db_Expr('NULL');
            }
            else if(ZendX_Db_Table_Abstract::LABEL_NOW == $data['adr_stop_date']) {
                $data['adr_stop_date'] = new Zend_Db_Expr(sprintf("str_to_date('%s', '%%Y-%%m-%%d')", gmdate("Y-m-d")));
            }
            else {
                $data['adr_stop_date'] = new Zend_Db_Expr(sprintf("str_to_date('%s', '%%Y-%%m-%%d')", $data['adr_stop_date']));
            }
        }
        
        return parent::update($data, $where);
    }
    
}
