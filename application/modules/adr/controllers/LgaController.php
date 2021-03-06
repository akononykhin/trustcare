<?php

class Adr_LgaController extends ZendX_Controller_Action
{

    public function init()
    {
        parent::init();
    }
    
    public function listAction()
    {
        $stateId = $this->getRequest()->getParam('state_id', null);
        
        $list = array();
        try {
        	if(!Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.system_dict", "view")) {
        		return new Exception();
        	}
        	
        	Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_ASSOC);
        	$select = Zend_Registry::getInstance()->dbAdapter->select()->from("lga", array('id', 'name'))
        				->order("name");
        	if(!empty($stateId)) {
        	    $select->where('(id_state=? or id_state is NULL)', $stateId);
        	}
        	$records = Zend_Registry::getInstance()->dbAdapter->fetchAll($select);
        	
        	foreach ($records as $record) {
                $list[] = array(
                    'id' => $record['id'],
                    'name' => $record['name'],
                );
        	}
        }
        catch(Exception $ex) {
        	$exMessage = $ex->getMessage();
        	if(!empty($exMessage)) {
            	$this->getLogger()->error(sprintf("'%s': %s", Zend_Auth::getInstance()->getIdentity(), $ex->getMessage()));
        	}
        }
        $this->_helper->json($list);
    }
}

