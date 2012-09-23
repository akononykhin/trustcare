<?php
/**
 * Alexey Kononykhin (alexey.kononykhin@gmail.com)
 */

class TrustCare_Registry_Storage
{
    /**
     * @var Zend_Db
     */
    protected $_persistantDb = null;
    
    /**
     * @return Zend_Db
     */
    public function getPersistantDb()
    {
    	if(!is_null($this->_persistantDb)) {
    		try {
    			$this->_persistantDb->logSQLErrors(false); /* We don't need info about lost connection */
    			$this->_persistantDb->fetchRow("select current_timestamp();");
                $this->_persistantDb->logSQLErrors(true);
    		}
    		catch(Exception $ex) {
    			$this->_persistantDb->closeConnection();
    			$this->_persistantDb = null;
    		}
    	}
        if(is_null($this->_persistantDb)) {
        	$db_options = Zend_Registry::get('dbOptions');
        	$this->_persistantDb = Zend_Db::factory($db_options['adapter'], $db_options['params']);
        }
        
        return $this->_persistantDb;
    }
}


 


