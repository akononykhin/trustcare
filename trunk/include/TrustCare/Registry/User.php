<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */


class TrustCare_Registry_User
{
    /**
     * @var TrustCare_Model_User
     */
    protected $_user = null;
    
    /**
     * @return TrustCare_Model_User
     */
    public function getAdmin()
    {
        if(!is_null($this->_user)) {
            return $this->_user;
        }
        
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            
            $model = TrustCare_Model_User::findByName($identity);
            if(!is_null($model)) {
                $this->_user = $model;
            }
        }
        
        return $this->_user;
    }
}

