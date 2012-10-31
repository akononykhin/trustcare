<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */


class TrustCare_Model_DbTable_ReportCommunity extends ZendX_Db_Table_Abstract
{
    protected $_name    = 'report_community';
    protected $_primary = 'id';
    
    protected $_metadata = array (
                'id' => 
                    array (
                      'SCHEMA_NAME' => NULL,
                      'TABLE_NAME' => 'report_community',
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
        $data['id'] = $db->nextSequenceId('report_community_id_seq');

        if(ZendX_Db_Table_Abstract::LABEL_NOW == $data['generation_date']) {
            $data['generation_date'] = new Zend_Db_Expr(sprintf("str_to_date('%s', '%%Y-%%m-%%d %%H:%%i:%%s')", gmdate("Y-m-d H:i:s")));
        }
        else {
            $data['generation_date'] = new Zend_Db_Expr(sprintf("str_to_date('%s', '%%Y-%%m-%%d %%H:%%i:%%s')", $data['generation_date']));
        }

        if(array_key_exists('id_user', $data) && empty($data['id_user'])) {
            $data['id_user'] = new Zend_Db_Expr('NULL');
        }
        
        return parent::insert($data);
    }
    
    
    public function update(array $data, $where)
    {
        foreach($data as $key=>$value) {
            if('filename' != $key && 'id_user' != $key && 'generation_date' != $key) {
                unset($data[$key]);
            }
        }


        if(array_key_exists('id_user', $data) && empty($data['id_user'])) {
            $data['id_user'] = new Zend_Db_Expr('NULL');
        }

        return parent::update($data, $where);
    }
    
}
