<?php

class Adr_PatientController extends ZendX_Controller_Action
{

    public function init()
    {
        parent::init();
    }
    
    public function getAction()
    {
    	$id = $this->_getParam('id');
    
    	$responseObj = new stdClass();
    	$responseObj->success = false;
    
    	$db_options = Zend_Registry::get('dbOptions');
    	$db = Zend_Db::factory($db_options['adapter'], $db_options['params']);
    	$errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error.");
    	try {
    		if(!Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.patient", "view")) {
    			throw new Exception('');
    		}
    		
        	$model = TrustCare_Model_Patient::find($id, array('mapperOptions' => array('adapter' => $db)));
    		if (is_null($model)) {
    			$errorMsg = Zend_Registry::get("Zend_Translate")->_("Unknown patient.");
    			throw new Exception("");
    		}
    
			$responseObj->info = array(
				'id' => $model->getId(),
    			'last_name' => $model->getLastName(),
    			'first_name' => $model->getFirstName(),
    			'full_name' => sprintf("%s %s", $model->getLastName(), $model->getFirstName()),
			);
			$responseObj->success = true;
    	}
    	catch(Exception $ex) {
    		$exMessage = $ex->getMessage();
    		if(!empty($exMessage)) {
    			$this->getLogger()->error(sprintf("'%s': %s", Zend_Auth::getInstance()->getIdentity(), $exMessage));
    		}
    		$responseObj->success = false;
    		$responseObj->message = $errorMsg;
    	}
    	$this->_helper->json($responseObj);
    }
    
    public function filterAccessableAction()
    {
        $list = array();
        try {
            $rawBody = $this->getRequest()->getRawBody();
            $params = Zend_Json::decode($rawBody);
            $filter = array_key_exists('filter', $params) ? $params['filter'] : '';
            
            if(!Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.patient", "view")) {
            	throw new Exception('');
            }
            
            if(empty($filter)) {
                throw new Exception('');
            }
            $model = new TrustCare_Model_Patient();
            $objs = $model->fetchAllFilteredBy($filter);
            
            foreach($objs as $obj) {
                $list[] = array(
                    'id' => $obj->getId(),
                    'full_name' => $obj->showNameAs()
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

