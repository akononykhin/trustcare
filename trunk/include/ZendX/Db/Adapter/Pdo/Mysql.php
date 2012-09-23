<?php
/**
 * Alexey Kononykhin (alexey.kononykhin@gmail.com)
 * 26.10.2010
 */



class ZendX_Db_Adapter_Pdo_Mysql extends Zend_Db_Adapter_Pdo_Mysql
{
    /**
     * @var Logger
     */
    private $_logger = null;
    
    private $_logSQLErrors = true;
    
    public function logSQLErrors($value)
    {
        $this->_logSQLErrors = !empty($value) ? true : false;
    }
    
    public function query($sql, $bind = array())
    {
        try {
            return parent::query($sql, $bind);
        }
        catch(Exception $ex) {
            if($this->_logSQLErrors) {
                if(is_null($this->_logger)) {
                    $this->_logger = LoggerManager::getLogger("ZendX_Db_Adapter_Pdo_Mysql");
                }
                $this->_logger->error(sprintf("%s\n\n\t%s", $sql, $ex->getMessage()));
            }
            throw $ex;
        }
    }
    
    public function lastSequenceId($sequenceName)
    {
        $this->_connect();
        $value = $this->fetchOne(sprintf("SELECT value from db_sequence where name=%s;", $this->quote($sequenceName)));
        return $value;
    }

    public function nextSequenceId($sequenceName)
    {
        $this->_connect();
        $this->beginTransaction();
        try {
            /* It's very important to specify FOR UPDATE. In this case MySQL will lock information until commit/rollback */
            $value = $this->fetchOne(sprintf("SELECT value from db_sequence where name=%s for update;", $this->quote($sequenceName)));
            $this->update("db_sequence", array('value' => new Zend_Db_Expr("value+1")), "name=" . $this->quote($sequenceName));
            $value = $this->fetchOne(sprintf("SELECT value from db_sequence where name=%s for update;", $this->quote($sequenceName)));

            $this->commit();
        }
        catch(Exception $ex) {
            $this->rollback();
        }
        return $value;
    }
}

 


