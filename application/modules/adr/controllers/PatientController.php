<?php

class Adr_PatientController extends ZendX_Controller_Action
{

    public function init()
    {
        parent::init();
    }
    

    public function createAction()
    {
        $responseObj = new stdClass();
        $responseObj->success = false;
        $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
    
        $db_options = Zend_Registry::get('dbOptions');
        $db = Zend_Db::factory($db_options['adapter'], $db_options['params']);
        $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error.");
        $db->beginTransaction();
        $startedTransaction = true;
        try {
            if(!Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.patient", "create")) {
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("You don't have enougth rights.");
                throw new Exception();
            }
    
            $rawBody = $this->getRequest()->getRawBody();
            $params = Zend_Json::decode($rawBody);
    
            $identifier = array_key_exists('identifier', $params) ? $params['identifier'] : null;
            if(empty($identifier)) {
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Necessary to enter Client ID");
                throw new Exception('');
            }
            $checkModel = TrustCare_Model_Patient::findByIdentifier($identifier);
            if(!is_null($checkModel)) {
                $errorMsg = sprintf(Zend_Registry::get("Zend_Translate")->_("Client ID %s has already been used"), $identifier);
                throw new Exception("");
            }
            $isActive = array_key_exists('is_active', $params) && !empty($params['is_active']) ? true : false;
            $isMale = array_key_exists('is_male', $params) && !empty($params['is_male']) ? true : false;

            $firstName = array_key_exists('first_name', $params) ? $params['first_name'] : null;
            if(empty($firstName)) {
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Necessary to enter First Name");
                throw new Exception('');
            }
            $lastName = array_key_exists('last_name', $params) ? $params['last_name'] : null;
            if(empty($lastName)) {
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Necessary to enter Last Name");
                throw new Exception('');
            }
            $idPhysician = array_key_exists('id_physician', $params) ? $params['id_physician'] : null;
            $birthdate = array_key_exists('birthdate', $params) ? $params['birthdate'] : null;
            if(empty($birthdate)) {
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Necessary to enter birthdate");
                throw new Exception('');
            }
            $idCountry = array_key_exists('id_country', $params) ? $params['id_country'] : null;
            $idState = array_key_exists('id_state', $params) ? $params['id_state'] : null;
            $city = array_key_exists('city', $params) ? $params['city'] : null;
            $address = array_key_exists('address', $params) ? $params['address'] : null;
            $zip = array_key_exists('zip', $params) ? $params['zip'] : null;
            $phone = array_key_exists('phone', $params) ? $params['phone'] : null;
            
            $model = new TrustCare_Model_Patient(
                array(
                    'identifier' => $identifier,
                    'is_active' => $isActive,
                    'is_male' => $isMale,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'id_physician' => $idPhysician,
                    'birthdate' => $birthdate,
                    'id_country' => $idCountry,
                    'id_state' => $idState,
                    'city' => $city,
                    'address' => $address,
                    'zip' => $zip,
                    'phone' => $phone,
                )
                );
            $model->save();
    
            $db->commit();
            $responseObj->id = $model->getId();
            $responseObj->success = true;
        }
        catch(Exception $ex) {
            if($startedTransaction) {
                $db->rollBack();
            }
            $exMessage = $ex->getMessage();
            if(!empty($exMessage)) {
                $this->getLogger()->error(sprintf("Failed to create patient: %s", $exMessage));
            }
            $responseObj->message = $errorMsg;
        }
        $this->_helper->json($responseObj);
    
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

