<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */


class TrustCare_Model_DbTable_LogAccess extends ZendX_Db_Table_Abstract
{
    protected $_name    = 'log_access';
    protected $_primary = 'id';

    public function insert(array $data)
    {
        if (empty($data['time'])) {
            $data['time'] = new Zend_Db_Expr(sprintf("str_to_date('%s', '%%Y-%%m-%%d %%H:%%i:%%s')", gmdate("Y-m-d H:i:s")));
        }
        return parent::insert($data);
    }
    
    public function update()
    {
        throw new Exception("Can't update LogAccess entity");
    }
    
    public function delete()
    {
        throw new Exception("Can't delete LogAccess entity");
    }
}
