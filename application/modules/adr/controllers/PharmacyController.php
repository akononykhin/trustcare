<?php

class Adr_PharmacyController extends ZendX_Controller_Action
{

    public function init()
    {
        parent::init();
    }
    
    public function listActiveAction()
    {
        $list = array();
        try {
        	if(!Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.pharmacy", "view")) {
        		return new Exception();
        	}
        	
        	Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_ASSOC);
        	$select = Zend_Registry::getInstance()->dbAdapter->select()->from("pharmacy", array('id', 'name'))
        				->where("is_active!=0")
        				->order("name");
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
            if(!Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.pharmacy", "create")) {
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("You don't have enougth rights.");
                throw new Exception();
            }
        
            $rawBody = $this->getRequest()->getRawBody();
            $params = Zend_Json::decode($rawBody);

            $name = array_key_exists('name', $params) ? $params['name'] : null;
            if(empty($name)) {
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Necessary to enter title");
                throw new Exception('');
            }
            $isActive = array_key_exists('is_active', $params) && !empty($params['is_active']) ? true : false;
            $idCountry = array_key_exists('id_country', $params) ? $params['id_country'] : null;
            $idState = array_key_exists('id_state', $params) ? $params['id_state'] : null;
            $address = array_key_exists('address', $params) ? $params['address'] : null;
            $idLga = array_key_exists('id_lga', $params) ? $params['id_lga'] : null;
            $idFacility = array_key_exists('id_facility', $params) ? $params['id_facility'] : null;
            if(is_null($idFacility)) {
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Necessary to choose facility");
                throw new Exception('');
            }
        
            $model = new TrustCare_Model_Pharmacy(
                array(
                    'name' => $name,
                    'is_active' => $isActive,
                    'id_country' => $idCountry,
                    'id_state' => $idState,
                    'address' => $address,
                    'id_lga' => $idLga,
                    'id_facility' => $idFacility,
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
                $this->getLogger()->error(sprintf("Failed to create pharmacy: %s", $exMessage));
            }
            $responseObj->message = $errorMsg;
        }
        $this->_helper->json($responseObj);
        
    }
}

