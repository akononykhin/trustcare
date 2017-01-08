<?php

class Adr_ReportController extends ZendX_Controller_Action
{

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
    }

    public function listAction()
    {
        $offset = (int)$this->getRequest()->getParam('offset', 0);
        $quantity = (int)$this->getRequest()->getParam('quantity', 50);
        
        $list = array();
        try {
        	$pharmacyIds = array_keys(Zend_Registry::get("TrustCare_Registry_User")->getListOfAvailablePharmacies());
        	 
        	$db = Zend_Registry::getInstance()->dbAdapter;
        	$db->setFetchMode(Zend_Db::FETCH_ASSOC);
        	$select = $db->select()
        		->from('nafdac',
        			array(
        					'nafdac.id',
        					'date_of_visit' => new Zend_Db_Expr("date_format(nafdac.date_of_visit, '%Y-%m-%d')"),
        					'generation_date' => new Zend_Db_Expr("date_format(nafdac.generation_date, '%Y-%m-%d %H:%i:%s')")
        			))
        		->joinLeft(array('patient'), 'nafdac.id_patient = patient.id', array('patient_identifier' => 'patient.identifier'))
        		->joinLeft(array('pharmacy'), 'nafdac.id_pharmacy = pharmacy.id', array('pharmacy_name' => 'pharmacy.name'))
        		->where(sprintf("nafdac.id_pharmacy in (%s)", join(",", $pharmacyIds)));
        	
        	$select->order("id desc");
        	$select->limit($quantity, $offset);
        			
			$resultSet = $db->fetchAll($select);
			foreach ( $resultSet as $row ) {
				$list[] = array (
					'id' => $row['id'],
					'gd' => $this->convertTimeToUserTimezone($row['generation_date'], Zend_Registry::getInstance()->dateFormat),
					'vd' => $this->showDateAtSpecifiedFormat($row['date_of_visit']),
					'pi' => $row['patient_identifier'],
					'ph' => $row['pharmacy_name'],
				);
			}
        }
        catch(Exception $ex) {
            $this->getLogger()->error(sprintf("'%s': %s", Zend_Auth::getInstance()->getIdentity(), $ex->getMessage()));
        }
        
        $o = new stdClass();
        $o->list = $list;
        $this->_helper->json($o);
    }
    

    public function deleteActionAccess()
    {
        return true;
    }
    
    
    public function deleteAction()
    {
    	$id = $this->_getParam('id');
    	$db_options = Zend_Registry::get('dbOptions');
    	$db = Zend_Db::factory($db_options['adapter'], $db_options['params']);
    
    	$responseObj = new stdClass();
    	$responseObj->success = false;
    	$errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
    	$db->beginTransaction();
    	try {
    		if(!Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:form", "delete")) {
    			$errorMsg = Zend_Registry::get("Zend_Translate")->_("Access Denied.");
    			throw new Exception();
    		}
    
    		$model = TrustCare_Model_Nafdac::find($id);
    		if(is_null($model)) {
    			throw new Exception(sprintf("Failed to load nafdac.id=%s", $id));
    		}
    		
    		$availablePharmacies = Zend_Registry::get("TrustCare_Registry_User")->getListOfAvailablePharmacies();
    		if(!array_key_exists($model->getIdPharmacy(), $availablePharmacies)) {
    			$errorMsg = Zend_Registry::get("Zend_Translate")->_("Access Denied.");
    			throw new Exception();
    		}
    		
    		$generator = TrustCare_SystemInterface_ReportGenerator_Abstract::factory(TrustCare_SystemInterface_ReportGenerator_Abstract::CODE_NAFDAC);
    		$fileName = $model->getFilename();
    		
    		$fileReportOutput = sprintf("%s/%s", $generator->reportsDirectory(), $fileName);
    		if(file_exists($fileReportOutput) && is_file($fileReportOutput)) {
    			unlink($fileReportOutput);
    		}
    		$model->delete();
    
    		$db->commit();
    
    		$responseObj->success = true;
    	}
    	catch(Exception $ex) {
    		$db->rollBack();
    		$exMessage = $ex->getMessage();
    		if(!empty($exMessage)) {
    			$this->getLogger()->error(sprintf("'%s': %s", Zend_Auth::getInstance()->getIdentity(), $exMessage));
    		}
    		$responseObj->message = $errorMsg;
    	}
    	$this->_helper->json($responseObj);
    }
    

    public function downloadActionAccess()
    {
        return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:form", "view");
    }

    
    public function downloadAction()
    {
        $id = $this->_getParam('id');
    
        $model = TrustCare_Model_Nafdac::find($id);
        if(is_null($model)) {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown report")));
            return;
        }
        
        $availablePharmacies = Zend_Registry::get("TrustCare_Registry_User")->getListOfAvailablePharmacies();
        if(!array_key_exists($model->getIdPharmacy(), $availablePharmacies)) {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Access Denied")));
            return;
        }
        
        $generator = TrustCare_SystemInterface_ReportGenerator_Abstract::factory(TrustCare_SystemInterface_ReportGenerator_Abstract::CODE_NAFDAC);
        $fileName = $model->getFilename();
        
        $fileReportOutput = sprintf("%s/%s", $generator->reportsDirectory(), $fileName);
        if(!file_exists($fileReportOutput) || !is_file($fileReportOutput)) {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Report file not found")));
            return;
        }
        
        $this->outputFileAsAttachment($fileReportOutput);
        die;
    }

    public function attrListsAction()
    {
    	$o = new stdClass();
    	$o->outcome = TrustCare_Model_Nafdac::getOutcomeReactionTypes();
    	$o->subsided = TrustCare_Model_Nafdac::getSubsidedValues();
    	$o->reappeared = TrustCare_Model_Nafdac::getReappearedValues();
    	$o->extent = TrustCare_Model_Nafdac::getExtentValues();
    	$o->seriousness = TrustCare_Model_Nafdac::getSeriousnessValues();
    	$o->relationship = TrustCare_Model_Nafdac::getRelationshipValues();
    	 
    	$this->_helper->json($o);
    }
}

