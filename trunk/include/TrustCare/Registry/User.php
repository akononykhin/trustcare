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
    public function getUser()
    {
        if(!is_null($this->_user)) {
            return $this->_user;
        }
        
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            
            $model = TrustCare_Model_User::findByLogin($identity);
            if(!is_null($model)) {
                $this->_user = $model;
            }
        }
        
        return $this->_user;
    }

    /**
     * 
     * Get the list of pharmacies available for currently logged user.
     * The list will contain active and directly specified (probably not active) pharmacies
     */
    public function getListOfAvailablePharmacies($ids = array())
    {
        $pharmacy = TrustCare_Model_Pharmacy::find($this->getUser()->getIdPharmacy());
        /**
         * If $pharmacy=null - it's a user without assigned Pharmacy. She has access to all active pharmacies
         */
        
        if(!is_array($ids)) {
            if(empty($ids)) {
                $ids = array();
            }
            else {
                $ids = array($ids);
            }
        }
        
        $pharmacyList = array();
        $model = new TrustCare_Model_Pharmacy();
        if(count($ids)) {
            $clause = sprintf("(is_active=1 or id in (%s))", join(',', $ids));
        }
        else {
            $clause = "is_active=1";
        }
        foreach ($model->fetchAll($clause) as $obj) {
            if(!is_null($pharmacy) && $pharmacy->getId() != $obj->getId()) {
                continue;
            }
            $pharmacyList[$obj->getId()] = $obj->getName();
        }
        
        return $pharmacyList;
    }
}

