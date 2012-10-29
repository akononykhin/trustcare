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
        
        return parent::update($data, $where);
    }
    
}
