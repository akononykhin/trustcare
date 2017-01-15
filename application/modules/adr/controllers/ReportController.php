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
    

    public function createAction()
    {
        $responseObj = new stdClass();
        $responseObj->success = false;
        $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
    
        $db_options = Zend_Registry::get('dbOptions');
        $db = Zend_Db::factory($db_options['adapter'], $db_options['params']);
        $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error.");
        $db->beginTransaction();
        try {
            if(!Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:form", "create")) {
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("You don't have enougth rights.");
                throw new Exception();
            }
            
            $rawBody = $this->getRequest()->getRawBody();
            $params = Zend_Json::decode($rawBody);
    
            $idPharmacy = array_key_exists('id_pharmacy', $params) ? $params['id_pharmacy'] : -1;
            if(!array_key_exists($idPharmacy, Zend_Registry::get("TrustCare_Registry_User")->getListOfAvailablePharmacies())) {
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Access Denied");
                throw new Exception('');
            }
            $patientId = array_key_exists('patient_id', $params) ? $params['patient_id'] : null;
            if(is_null(TrustCare_Model_Patient::find($patientId, array('mapperOptions' => array('adapter' => $db))))) {
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Unknown patient");
                throw new Exception('');
            }

            $date_of_visit = array_key_exists('date_of_visit', $params) ? $params['date_of_visit'] : null;
            $adr_description = array_key_exists('adr_description', $params) ? $params['adr_description'] : null;
            $onset_time = array_key_exists('onset_time', $params) ? $params['onset_time'] : null;
            $onset_type = array_key_exists('onset_type', $params) ? $params['onset_type'] : null;
            $adr_start_date = array_key_exists('adr_start_date', $params) ? $params['adr_start_date'] : null;
            $adr_stop_date = array_key_exists('adr_stop_date', $params) ? $params['adr_stop_date'] : null;
            $outcome_of_reaction_type = array_key_exists('outcome_of_reaction_type', $params) ? $params['outcome_of_reaction_type'] : null;
            $outcome_of_reaction_desc = array_key_exists('outcome_of_reaction_desc', $params) ? $params['outcome_of_reaction_desc'] : null;
            $subsided = array_key_exists('subsided', $params) ? $params['subsided'] : null;
            $reappeared = array_key_exists('reappeared', $params) ? $params['reappeared'] : null;
            $extent = array_key_exists('extent', $params) ? $params['extent'] : null;
            $seriousness = array_key_exists('seriousness', $params) ? $params['seriousness'] : null;
            $relationship = array_key_exists('relationship', $params) ? $params['relationship'] : null;
            $treatment_of_reaction = array_key_exists('treatment_of_reaction', $params) ? $params['treatment_of_reaction'] : null;
            $was_admitted = array_key_exists('was_admitted', $params) && !empty($params['was_admitted']) ? true : false;
            $was_hospitalization_prolonged = array_key_exists('was_hospitalization_prolonged', $params) && !empty($params['was_hospitalization_prolonged']) ? true : false;
            $duration_of_admission = array_key_exists('duration_of_admission', $params) ? $params['duration_of_admission'] : null;
            $relevant_data = array_key_exists('relevant_data', $params) ? $params['relevant_data'] : null;
            $relevant_history = array_key_exists('relevant_history', $params) ? $params['relevant_history'] : null;
            $reporter_name = array_key_exists('reporter_name', $params) ? $params['reporter_name'] : null;
            $reporter_address = array_key_exists('reporter_address', $params) ? $params['reporter_address'] : null;
            $reporter_profession = array_key_exists('reporter_profession', $params) ? $params['reporter_profession'] : null;
            $reporter_contact = array_key_exists('reporter_contact', $params) ? $params['reporter_contact'] : null;
            $reporter_email = array_key_exists('reporter_email', $params) ? $params['reporter_email'] : null;
            
            $nafdacModel = new TrustCare_Model_Nafdac(
                array(
                    'id_user' => Zend_Registry::get("TrustCare_Registry_User")->getUser()->getId(),
                    'id_patient' => $patientId,
                    'generation_date' => ZendX_Db_Table_Abstract::LABEL_NOW,
                    'id_pharmacy' => $idPharmacy,
                    'date_of_visit' => $date_of_visit,
                    'adr_start_date' => $adr_start_date,
                    'adr_stop_date' => $adr_stop_date,
                    'adr_description' => $adr_description,
                    'was_admitted' => $was_admitted,
                    'was_hospitalization_prolonged' => $was_hospitalization_prolonged,
                    'duration_of_admission' => $duration_of_admission,
                    'treatment_of_reaction' => $treatment_of_reaction,
                    'outcome_of_reaction_type' => $outcome_of_reaction_type,
                    'outcome_of_reaction_desc' => $outcome_of_reaction_desc,
                    'reporter_name' => $reporter_name,
                    'reporter_address' => $reporter_address,
                    'reporter_profession' => $reporter_profession,
                    'reporter_contact' => $reporter_contact,
                    'reporter_email' => $reporter_email,
                    'onset_time' => $onset_time,
                    'onset_type' => $onset_type,
                    'subsided' => $subsided,
                    'reappeared' => $reappeared,
                    'extent' => $extent,
                    'seriousness' => $seriousness,
                    'relationship' => $relationship,
                    'relevant_data' => $relevant_data,
                    'relevant_history' => $relevant_history,
                    'mapperOptions' => array('adapter' => $db)
                )
            );
            $nafdacModel->save();


            $suspectedDrugs = array_key_exists('suspected_drugs', $params) ? $params['suspected_drugs'] : array();
            foreach($suspectedDrugs as $drug) {
                $generic_name = array_key_exists('generic_name', $drug) ? $drug['generic_name'] : '';
                $dosage = array_key_exists('dosage', $drug) ? $drug['dosage'] : '';
                $batch_number = array_key_exists('batch_number', $drug) ? $drug['batch_number'] : '';
                $date_started = array_key_exists('date_started', $drug) ? $drug['date_started'] : '';
                $date_stopped = array_key_exists('date_stopped', $drug) ? $drug['date_stopped'] : '';
                $indication_for_use = array_key_exists('indication_for_use', $drug) ? $drug['indication_for_use'] : '';
            
                if(empty($generic_name)) {
                    continue;
                }
                $drugModel = new TrustCare_Model_NafdacDrug(array(
                    'id_nafdac' => $nafdacModel->getId(),
                    'name' => $generic_name,
                    'dosage' => $dosage,
                    'batch' => $batch_number,
                    'started' => $date_started,
                    'stopped' => $date_stopped,
                    'reason' => $indication_for_use,
                    'mapperOptions' => array('adapter' => $db)
                ));
                $drugModel->save();
            }
            
            $concomitantDrugs = array_key_exists('concomitant_drugs', $params) ? $params['concomitant_drugs'] : array();
            foreach($concomitantDrugs as $drug) {
                $generic_name = array_key_exists('generic_name', $drug) ? $drug['generic_name'] : '';
                $dosage = array_key_exists('dosage', $drug) ? $drug['dosage'] : '';
                $batch_number = array_key_exists('batch_number', $drug) ? $drug['batch_number'] : '';
                $date_started = array_key_exists('date_started', $drug) ? $drug['date_started'] : '';
                $date_stopped = array_key_exists('date_stopped', $drug) ? $drug['date_stopped'] : '';
                $indication_for_use = array_key_exists('indication_for_use', $drug) ? $drug['indication_for_use'] : '';
                
                if(empty($generic_name)) {
                    continue;
                }
                $medModel = new TrustCare_Model_NafdacMedicine(array(
                    'id_nafdac' => $nafdacModel->getId(),
                    'name' => $generic_name,
                    'dosage' => $dosage,
                    'route' => $batch_number,
                    'started' => $date_started,
                    'stopped' => $date_stopped,
                    'reason' => $indication_for_use,
                    'mapperOptions' => array('adapter' => $db)
                ));
                $medModel->save();
            }
            $db->commit(); /* Otherwise generator won't have access to thet report */
            
            try {
                $generator = TrustCare_SystemInterface_ReportGenerator_Abstract::factory(TrustCare_SystemInterface_ReportGenerator_Abstract::CODE_NAFDAC);
                
                $fileName = $generator->generate(array(
                    'id' => $nafdacModel->getId()
                ));
                
                $nafdacModel->setFilename($fileName);
                $nafdacModel->save();
            }
            catch(Exception $ex) {}
            
            $responseObj->success = true;
        }
        catch(Exception $ex) {
            $db->rollBack();
            $exMessage = $ex->getMessage();
            if(!empty($exMessage)) {
                $this->getLogger()->error(sprintf("Failed to create report: %s", $exMessage));
            }
            $responseObj->message = $errorMsg;
        }
        $this->_helper->json($responseObj);
    }
    

    public function getAction()
    {
        $id = $this->_getParam('id');
        $db_options = Zend_Registry::get('dbOptions');
        $db = Zend_Db::factory($db_options['adapter'], $db_options['params']);
    
        $responseObj = new stdClass();
        $responseObj->success = false;
        $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
        try {
            if(!Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:form", "view")) {
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Access Denied.");
                throw new Exception();
            }
    
            $model = TrustCare_Model_Nafdac::find($id, array('mapperOptions' => array('adapter' => $db)));
            if(is_null($model)) {
                throw new Exception(sprintf("Failed to load nafdac.id=%s", $id));
            }

            $patientModel = TrustCare_Model_Patient::find($model->getIdPatient(), array('mapperOptions' => array('adapter' => $db)));
            $pharmacyModel = TrustCare_Model_Pharmacy::find($model->getIdPharmacy(), array('mapperOptions' => array('adapter' => $db)));
            
            $suspected_drugs = array();
            $model1 = new TrustCare_Model_NafdacDrug(array('mapperOptions' => array('adapter' => $db)));
            foreach($model1->fetchAllByIdNafdac($model->getId()) as $obj) {
                $suspected_drugs[] = array(
                    'generic_name' => $obj->getName(),
                    'dosage' => $obj->getDosage(),
                    'batch_number' => $obj->getBatch(),
                    'date_started' => $obj->getStarted(),
                    'date_stopped' => $obj->getStopped(),
                    'indication_for_use' => $obj->getReason(),
                );
            }
            
            $concomitant_drugs = array();
            $model2 = new TrustCare_Model_NafdacMedicine(array('mapperOptions' => array('adapter' => $db)));
            foreach($model2->fetchAllByIdNafdac($model->getId()) as $obj) {
                $concomitant_drugs[] = array(
                    'generic_name' => $obj->getName(),
                    'dosage' => $obj->getDosage(),
                    'batch_number' => $obj->getRoute(),
                    'date_started' => $obj->getStarted(),
                    'date_stopped' => $obj->getStopped(),
                    'indication_for_use' => $obj->getReason(),
                );
            }
            
            
            $responseObj->info = array(
                'id' => $model->getId(),
                'patient' => !is_null($patientModel) ? sprintf("%s %s", $patientModel->getLastName(), $patientModel->getFirstName()) : $model->getIdPatient(),
                'generation_date' => $model->getGenerationDate(),
                'pharmacy' => !is_null($pharmacyModel) ? $pharmacyModel->getName() : $model->getIdPharmacy(),
                'date_of_visit' => $model->getDateOfVisit(),
                'adr_start_date' => $model->getAdrStartDate(),
                'adr_stop_date' => $model->getAdrStopDate(),
                'adr_description' => $model->getAdrDescription(),
                'was_admitted' => $model->getWasAdmitted(),
                'was_hospitalization_prolonged' => $model->getWasHospitalizationProlonged(),
                'duration_of_admission' => $model->getDurationOfAdmission(),
                'treatment_of_reaction' => $model->getTreatmentOfReaction(),
                'outcome_of_reaction_type' => $model->getOutcomeReactionTypeName($model->getOutcomeOfReactionType()),
                'outcome_of_reaction_desc' => $model->getOutcomeOfReactionDesc(),
                'reporter_name' => $model->getReporterName(),
                'reporter_address' => $model->getReporterAddress(),
                'reporter_profession' => $model->getReporterProfession(),
                'reporter_contact' => $model->getReporterContact(),
                'reporter_email' => $model->getReporterEmail(),
                'onset_time' => $model->getOnsetTime(),
                'onset_type' => $model->getOnsetType(),
                'subsided' => $model->getSubsidedValueName($model->getSubsided()),
                'reappeared' => $model->getReappearedValueName($model->getReappeared()),
                'extent' => $model->getExtentValueName($model->getExtent()),
                'seriousness' => $model->getSeriousnessValueName($model->getSeriousness()),
                'relationship' => $model->getRelationshipValueName($model->getRelationship()),
                'relevant_data' => $model->getRelevantData(),
                'relevant_history' => $model->getRelevantHistory(),
                'suspected_drugs' => $suspected_drugs,
                'concomitant_drugs' => $concomitant_drugs
            );
            $responseObj->success = true;
        }
        catch(Exception $ex) {
            $exMessage = $ex->getMessage();
            if(!empty($exMessage)) {
                $this->getLogger()->error(sprintf("'%s': %s", Zend_Auth::getInstance()->getIdentity(), $exMessage));
            }
            $responseObj->message = $errorMsg;
        }
        $this->_helper->json($responseObj);
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
    
    		$model = TrustCare_Model_Nafdac::find($id, array('mapperOptions' => array('adapter' => $db)));
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
    
        $model = TrustCare_Model_Nafdac::find($id, array('mapperOptions' => array('adapter' => $db)));
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


    public function regenerateAction()
    {
        $id = $this->_getParam('id');
        $db_options = Zend_Registry::get('dbOptions');
        $db = Zend_Db::factory($db_options['adapter'], $db_options['params']);
    
        $responseObj = new stdClass();
        $responseObj->success = false;
        $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
        try {
            if(!Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:form", "edit")) {
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Access Denied.");
                throw new Exception();
            }

            $model = TrustCare_Model_Nafdac::find($id, array('mapperOptions' => array('adapter' => $db)));
            if(is_null($model)) {
                throw new Exception(sprintf("Failed to load nafdac.id=%s", $id));
            }
            
            $availablePharmacies = Zend_Registry::get("TrustCare_Registry_User")->getListOfAvailablePharmacies();
            if(!array_key_exists($model->getIdPharmacy(), $availablePharmacies)) {
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Access Denied");
                throw new Exception('');
            }
            
            $generator = TrustCare_SystemInterface_ReportGenerator_Abstract::factory(TrustCare_SystemInterface_ReportGenerator_Abstract::CODE_NAFDAC);
            $fileName = $model->getFilename();
            
            $fileReportOutput = sprintf("%s/%s", $generator->reportsDirectory(), $fileName);
            if(file_exists($fileReportOutput) && is_file($fileReportOutput)) {
                unlink($fileReportOutput);
            }
            
            $fileName = $generator->generate(array('id' => $model->getId()));
            
            $model->setFilename($fileName);
            $model->save();
            
            $responseObj->success = true;
        }
        catch(Exception $ex) {
            $exMessage = $ex->getMessage();
            if(!empty($exMessage)) {
                $this->getLogger()->error(sprintf("'%s': %s", Zend_Auth::getInstance()->getIdentity(), $exMessage));
            }
            $responseObj->message = $errorMsg;
        }
        $this->_helper->json($responseObj);
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

