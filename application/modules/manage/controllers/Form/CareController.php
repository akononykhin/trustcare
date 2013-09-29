<?php

class Form_CareController extends ZendX_Controller_Action
{

    public function init()
    {
        parent::init();
    }


    public function indexAction()
    {
        $this->getRedirector()->gotoSimpleAndExit("list", $this->getRequest()->getControllerName());
    }
    
    public function listActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:form", "view");
    }
    
    public function listAction()
    {
        $columnsInfo = array(
            'id' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("ID"),
                'width' => '3%',
            ),
            'generation_date' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Generation Date"),
                'width' => '10%',
            ),
            'date_of_visit' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Date of Visit"),
                'width' => '8%',
            ),
            'pharmacy_name' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Pharmacy"),
                'filter' => array(
                    'type' => 'text',
                ),
                'width' => '20%',
            ),
            'patient_identifier' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Patient ID"),
                'filter' => array(
                    'type' => 'text',
                ),
                'width' => '20%',
            ),
            'patient_last_name' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Last Name"),
                'width' => '15%',
            ),
            'patient_first_name' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("First Name"),
                'width' => '15%',
            ),
        );

        $this->view->DataTable = array(
            'serverUrl' => $this->view->url(array('action' => 'list-load')),
            'toolbar' => array(
                array(
                    'text' => Zend_Registry::get("Zend_Translate")->_("Create"),
                    'url' => $this->view->url(array('action' => 'create'))
                ),
            ),
            'defSortColumn' => 1,
            'defSortDir' => 'desc',
            'chooseColumnVisibility' => true,
            'columnsInfo' => $columnsInfo,
            'bActionsColumn' => true
        );
        $this->render('list', null, true);
        return;
    }
    
    
    public function listLoadActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:form", "view");
    }
    
    
    public function listLoadAction()
    {
        $select = Zend_Registry::getInstance()->dbAdapter->select()->from('frm_care', array('count(id)'));
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_NUM);
        $result = Zend_Registry::getInstance()->dbAdapter->fetchAll($select);
        $iTotal = $result[0][0];


        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_ASSOC);
        $select = Zend_Registry::getInstance()->dbAdapter->select()
                                                         ->from('frm_care',
                                                             array(
                                                                 'frm_care.id',
                                                                 'date_of_visit' => new Zend_Db_Expr("date_format(date_of_visit, '%Y-%m-%d')"),
                                                                 'generation_date' => new Zend_Db_Expr("date_format(frm_care.generation_date, '%Y-%m-%d %H:%i:%s')"),
                                                                 'frm_care.is_commited'
                                                             ))
                                                         ->joinLeft(array('patient'), 'frm_care.id_patient = patient.id', array('patient_identifier' => 'patient.identifier',
                                                                                                                                'patient_first_name' => 'patient.first_name',
                                                                                                                                'patient_last_name' => 'patient.last_name'))
                                                         ->joinLeft(array('pharmacy'), 'frm_care.id_pharmacy = pharmacy.id', array('pharmacy_name' => 'pharmacy.name'));
         
        $this->processListLoadAjaxRequest($select, array('pharmacy_name' => 'pharmacy.name',
            'patient_identifier' => 'patient.identifier',
            'patient_first_name' => 'patient.first_name',
            'patient_last_name' => 'patient.last_name'));

        $rows = Zend_Registry::getInstance()->dbAdapter->selectWithLimit($select->__toString(), $iFilteredTotal);
                
        
        $output = array(
            "sEcho" => intval($_REQUEST['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );
        foreach ($rows as $row) {
            $row['DT_RowId'] = $row['id'];
            $row['generation_date'] = $this->convertDateToUserTimezone($row['generation_date']);
            $row['date_of_visit'] = $this->showDateAtSpecifiedFormat($row['date_of_visit']);
            
            $row['_row_actions_'] = array(
                array(
                    'title' => Zend_Registry::get("Zend_Translate")->_("View"),
                    'url' => $this->view->url(array('action' => 'view', 'id' => $row['id'])),
                    'type' => 'view'
                ),
                array(
                    'title' => Zend_Registry::get("Zend_Translate")->_("Edit"),
                    'url' => $this->view->url(array('action' => 'edit', 'id' => $row['id'])),
                    'type' => 'edit',
                    'conditions' => 'full.is_commited != 1'
                ),
                array(
                    'title' => Zend_Registry::get("Zend_Translate")->_("Delete"),
                    'url' => $this->view->url(array('action' => 'delete', 'id' => $row['id'])),
                	'type' => 'delete',
                	'askConfirm' => sprintf(Zend_Registry::get("Zend_Translate")->_("Are you sure you want to delete form %s generated %s?"), $row['id'], $row['date_of_visit']),
                ),
            );
            $output['aaData'][] = $row;
        }

                                              
        $this->_helper->json($output);
    }
    
    
    public function createActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:form", "create");
    }
    
    public function createAction()
    {
        if($this->getRequest()->isPost()) {
            $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
            
            $db_options = Zend_Registry::get('dbOptions');
            $db = Zend_Db::factory($db_options['adapter'], $db_options['params']);
            $db->beginTransaction();
            try {
                $isCommited = $this->_getParam('is_commited');
                $idPharmacy = $this->_getParam('id_pharmacy');
                $idPatient = $this->_getParam('id_patient');
                $dateOfVisit = $this->_getParam('date_of_visit');
                $isPregnant = $this->_getParam('is_pregnant');
                $isReceivePrescription = $this->_getParam('is_receive_prescription');
                $isMedErrorScreened = $this->_getParam('is_med_error_screened');
                $isMedErrorIdentified = $this->_getParam('is_med_error_identified');
                $isMedAdhProblemScreened = $this->_getParam('is_med_adh_problem_screened');
                $isMedAdhProblemIdentified = $this->_getParam('is_med_adh_problem_identified');
                $isMedErrorInterventionProvided = $this->_getParam('is_med_error_intervention_provided');
                $isAdhInterventionProvided = $this->_getParam('is_adh_intervention_provided');
                $isAdrScreened = $this->_getParam('is_adr_screened');
                $isAdrSymptoms = $this->_getParam('is_adr_symptoms');
                $adrSeverityId = $this->_getParam('adr_severity_id');
                $adrStartDate = $this->_getParam('adr_start_date');
                $adrStopDate = $this->_getParam('adr_stop_date');
                $isAdrInterventionProvided = $this->_getParam('is_adr_intervention_provided');
                $medErrorTypes = $this->_getParam('med_error_type');
                if(!$isMedErrorIdentified) {
                    $medErrorTypes = array();
                }
                $medAdhProblems = $this->_getParam('med_adh_problem');
                if(!$isMedAdhProblemIdentified) {
                    $medAdhProblems = array();
                }
                $medErrorInterventions = $this->_getParam('med_error_intervention');
                if(!$isMedErrorInterventionProvided) {
                    $medErrorInterventions = array();
                }
                $adhInterventions = $this->_getParam('adh_intervention');
                if(!$isAdhInterventionProvided) {
                    $adhInterventions = array();
                }
                $medErrorInterventionOutcomes = $this->_getParam('med_error_intervention_outcome');
                $adhInterventionOutcomes = $this->_getParam('adh_intervention_outcome');
                $suspectedAdrHepatic = $this->_getParam('suspected_adr_hepatic');
                $suspectedAdrNervous = $this->_getParam('suspected_adr_nervous');
                $suspectedAdrCardiovascular = $this->_getParam('suspected_adr_cardiovascular');
                $suspectedAdrSkin = $this->_getParam('suspected_adr_skin');
                $suspectedAdrMetabolic = $this->_getParam('suspected_adr_metabolic');
                $suspectedAdrMusculoskeletal = $this->_getParam('suspected_adr_musculoskeletal');
                $suspectedAdrGeneral = $this->_getParam('suspected_adr_general');
                $adrInterventions = $this->_getParam('adr_intervention');
                if(!$isAdrInterventionProvided) {
                    $adrInterventions = array();
                }
                
                if(empty($idPharmacy)) {
                    $errorMsg = Zend_Registry::get("Zend_Translate")->_("Necessary to choose pharmacy");
                    throw new Exception('');
                }
                if(empty($idPatient)) {
                    $errorMsg = Zend_Registry::get("Zend_Translate")->_("Necessary to choose patient");
                    throw new Exception('');
                }
                if(empty($dateOfVisit)) {
                    $errorMsg = Zend_Registry::get("Zend_Translate")->_("Necessary to enter date of visit");
                    throw new Exception('');
                }
                
                $pharmacyModel = TrustCare_Model_Pharmacy::find($idPharmacy);
                if(is_null($pharmacyModel)) {
                    throw new Exception(sprintf("Trying to create form for unknown pharmacy.id=%s", $idPharmacy));
                }

                $patientModel = TrustCare_Model_Patient::find($idPatient);
                if(is_null($patientModel)) {
                    throw new Exception(sprintf("Trying to create form for unknown patient.id=%s", $idPatient));
                }
                
                if(!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $patientModel->getBirthdate(), $matches)) {
                    throw new Exception(sprintf("Failed to parse birthdate='%s' of patient.id=%s", $patientModel->getBirthdate(), $patientModel->getId()));
                }
                $patientYear = $matches[1];
                $patientMonth = $matches[2];
                $patientDay = $matches[3];
                if(!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $dateOfVisit, $matches)) {
                    throw new Exception(sprintf("Failed to parse date_of_visit='%s'", $dateOfVisit));
                }
                $visitYear = $matches[1];
                $visitMonth = $matches[2];
                $visitDay = $matches[3];
                $diffYears = $visitYear - $patientYear;
                if($visitMonth < $patientMonth) {
                    $diffYears--;
                }
                else if($visitMonth == $patientMonth && $visitDay < $patientDay) {
                    $diffYears--;
                }
                $isPatientYounger15 = ($diffYears < 15) ? true : false;
                
                $frmModel = new TrustCare_Model_FrmCare(
                    array(
                        'id_user' => Zend_Registry::get("TrustCare_Registry_User")->getUser()->getId(),
                        'is_commited' => $isCommited,
                        'id_pharmacy' => $idPharmacy,
                    	'id_patient' => $idPatient,
                		'date_of_visit' => $dateOfVisit,
                		'is_pregnant' => $isPregnant,
                		'is_receive_prescription' => $isReceivePrescription,
                		'is_med_error_screened' => $isMedErrorScreened,
                		'is_med_error_identified' => $isMedErrorIdentified,
                		'is_med_adh_problem_screened' => $isMedAdhProblemScreened,
                		'is_med_adh_problem_identified' => $isMedAdhProblemIdentified,
                		'is_med_error_intervention_provided' => $isMedErrorInterventionProvided,
                        'is_adh_intervention_provided' => $isAdhInterventionProvided,
                		'is_adr_screened' => $isAdrScreened,
                		'is_adr_symptoms' => $isAdrSymptoms,
                        'adr_start_date' => $adrStartDate,
                        'adr_stop_date' => $adrStopDate,
                    	'adr_severity_id' => $adrSeverityId,
                		'is_adr_intervention_provided' => $isAdrInterventionProvided,
                		'is_patient_younger_15' => $isPatientYounger15,
                		'is_patient_male' => $patientModel->getIsMale(),
                    	'mapperOptions' => array('adapter' => $db)
                    )
                );
                $frmModel->save();
                
                foreach($medErrorTypes  as $dictId) {
                    $model = new TrustCare_Model_FrmCareMedErrorType(
                        array(
          					'id_frm_care' => $frmModel->getId(),
           					'id_pharmacy_dictionary' => $dictId,
                           	'mapperOptions' => array('adapter' => $db)
                        )
                    );
                    $model->save();
                }
                
                foreach($medAdhProblems  as $dictId) {
                    $model = new TrustCare_Model_FrmCareMedAdhProblem(
                        array(
          					'id_frm_care' => $frmModel->getId(),
           					'id_pharmacy_dictionary' => $dictId,
                           	'mapperOptions' => array('adapter' => $db)
                        )
                    );
                    $model->save();
                }
                
                foreach($medErrorInterventions  as $dictId) {
                    $model = new TrustCare_Model_FrmCareMedErrorIntervention(
                            array(
                                    'id_frm_care' => $frmModel->getId(),
                                    'id_pharmacy_dictionary' => $dictId,
                                    'mapperOptions' => array('adapter' => $db)
                            )
                    );
                    $model->save();
                }
                
                foreach($adhInterventions  as $dictId) {
                    $model = new TrustCare_Model_FrmCareAdhIntervention(
                        array(
          					'id_frm_care' => $frmModel->getId(),
           					'id_pharmacy_dictionary' => $dictId,
                           	'mapperOptions' => array('adapter' => $db)
                        )
                    );
                    $model->save();
                }

                foreach($medErrorInterventionOutcomes  as $dictId) {
                    $model = new TrustCare_Model_FrmCareMedErrorInterventionOutcome(
                            array(
                                    'id_frm_care' => $frmModel->getId(),
                                    'id_pharmacy_dictionary' => $dictId,
                                    'mapperOptions' => array('adapter' => $db)
                            )
                    );
                    $model->save();
                }
                
                foreach($adhInterventionOutcomes  as $dictId) {
                    $model = new TrustCare_Model_FrmCareAdhInterventionOutcome(
                        array(
          					'id_frm_care' => $frmModel->getId(),
           					'id_pharmacy_dictionary' => $dictId,
                           	'mapperOptions' => array('adapter' => $db)
                        )
                    );
                    $model->save();
                }
                
                foreach($suspectedAdrHepatic  as $dictId) {
                    $model = new TrustCare_Model_FrmCareSuspectedAdrHepatic(
                        array(
          					'id_frm_care' => $frmModel->getId(),
           					'id_pharmacy_dictionary' => $dictId,
                           	'mapperOptions' => array('adapter' => $db)
                        )
                    );
                    $model->save();
                }
                foreach($suspectedAdrNervous  as $dictId) {
                    $model = new TrustCare_Model_FrmCareSuspectedAdrNervous(
                        array(
          					'id_frm_care' => $frmModel->getId(),
           					'id_pharmacy_dictionary' => $dictId,
                           	'mapperOptions' => array('adapter' => $db)
                        )
                    );
                    $model->save();
                }
                foreach($suspectedAdrCardiovascular  as $dictId) {
                    $model = new TrustCare_Model_FrmCareSuspectedAdrCardiovascular(
                        array(
          					'id_frm_care' => $frmModel->getId(),
           					'id_pharmacy_dictionary' => $dictId,
                           	'mapperOptions' => array('adapter' => $db)
                        )
                    );
                    $model->save();
                }
                foreach($suspectedAdrSkin  as $dictId) {
                    $model = new TrustCare_Model_FrmCareSuspectedAdrSkin(
                        array(
          					'id_frm_care' => $frmModel->getId(),
           					'id_pharmacy_dictionary' => $dictId,
                           	'mapperOptions' => array('adapter' => $db)
                        )
                    );
                    $model->save();
                }
                foreach($suspectedAdrMetabolic  as $dictId) {
                    $model = new TrustCare_Model_FrmCareSuspectedAdrMetabolic(
                        array(
          					'id_frm_care' => $frmModel->getId(),
           					'id_pharmacy_dictionary' => $dictId,
                           	'mapperOptions' => array('adapter' => $db)
                        )
                    );
                    $model->save();
                }
                foreach($suspectedAdrMusculoskeletal  as $dictId) {
                    $model = new TrustCare_Model_FrmCareSuspectedAdrMusculoskeletal(
                        array(
          					'id_frm_care' => $frmModel->getId(),
           					'id_pharmacy_dictionary' => $dictId,
                           	'mapperOptions' => array('adapter' => $db)
                        )
                    );
                    $model->save();
                }
                foreach($suspectedAdrGeneral  as $dictId) {
                    $model = new TrustCare_Model_FrmCareSuspectedAdrGeneral(
                        array(
          					'id_frm_care' => $frmModel->getId(),
           					'id_pharmacy_dictionary' => $dictId,
                           	'mapperOptions' => array('adapter' => $db)
                        )
                    );
                    $model->save();
                }
                
                foreach($adrInterventions  as $dictId) {
                    $model = new TrustCare_Model_FrmCareAdrIntervention(
                        array(
          					'id_frm_care' => $frmModel->getId(),
           					'id_pharmacy_dictionary' => $dictId,
                           	'mapperOptions' => array('adapter' => $db)
                        )
                    );
                    $model->save();
                }
                
                $db->commit();
                
                $generateNafdacForm = $this->_getParam('generate_nafdac_form');
                $isGenerateNafdacForm = $isCommited ? !empty($generateNafdacForm) : false;
                
                if(!$isGenerateNafdacForm) {
                    $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName());
                }
                else {
                    $this->getRedirector()->gotoSimpleAndExit('create', 'nafdac', null, array('id_frm_care' => $frmModel->getId()));
                }
            }
            catch(Exception $ex) {
                $db->rollback();
                $message = $ex->getMessage();
                if(!empty($message)) {
                    $this->getLogger()->error($message);
                }
            }
            $this->view->error = $errorMsg;
        }
        else {
            $idPharmacy = null;
            $idPatient = null;
            $dateOfVisit = $this->convertDateToUserTimezone(gmdate("Y-m-d"), 'yyyy-MM-dd');
            $isPregnant = false;
            $isReceivePrescription = true;
            $isMedErrorScreened = true;
            $isMedErrorIdentified = true;
            $medErrorTypes = array();
            $isMedAdhProblemScreened = true;
            $isMedAdhProblemIdentified = true;
            $medAdhProblems = array();
            $isMedErrorInterventionProvided = false;
            $isAdhInterventionProvided = true;
            $medErrorInterventions = array();
            $adhInterventions = array();
            $medErrorInterventionOutcomes = array();
            $adhInterventionOutcomes = array();
            $isAdrScreened = true;
            $isAdrSymptoms = false;
            $adrSeverityId = null;
            $adrStartDate = '';
            $adrStopDate = '';
            $suspectedAdrHepatic = array();
            $suspectedAdrNervous = array();
            $suspectedAdrCardiovascular = array();
            $suspectedAdrSkin = array();
            $suspectedAdrMetabolic = array();
            $suspectedAdrMusculoskeletal = array();
            $suspectedAdrGeneral = array();
            $isAdrInterventionProvided = true;
            $adrInterventions = array();
            $isNafdacAdrFilled = false;
        }
        
        $dictEntities = array(
            TrustCare_Model_PharmacyDictionary::DTYPE_MEDICATION_ERROR_TYPE => $medErrorTypes,
            TrustCare_Model_PharmacyDictionary::DTYPE_MEDICATION_ADH_PROBLEM => $medAdhProblems,
            TrustCare_Model_PharmacyDictionary::DTYPE_MED_ERROR_INTERVENTION_PROVIDED => $medErrorInterventions,
            TrustCare_Model_PharmacyDictionary::DTYPE_ADH_INTERVENTION_PROVIDED => $adhInterventions,
            TrustCare_Model_PharmacyDictionary::DTYPE_MED_ERROR_INTERVENTION_OUTCOME => $medErrorInterventionOutcomes,
            TrustCare_Model_PharmacyDictionary::DTYPE_ADH_INTERVENTION_OUTCOME => $adhInterventionOutcomes,
            TrustCare_Model_PharmacyDictionary::DTYPE_ADR_SEVERITY_GRADE => array($adrSeverityId),
            TrustCare_Model_PharmacyDictionary::DTYPE_HEPATIC => $suspectedAdrHepatic,
            TrustCare_Model_PharmacyDictionary::DTYPE_NERVOUS => $suspectedAdrNervous,
            TrustCare_Model_PharmacyDictionary::DTYPE_CARDIOVASCULAR => $suspectedAdrCardiovascular,
            TrustCare_Model_PharmacyDictionary::DTYPE_SKIN => $suspectedAdrSkin,
            TrustCare_Model_PharmacyDictionary::DTYPE_METABOLIC => $suspectedAdrMetabolic,
            TrustCare_Model_PharmacyDictionary::DTYPE_MUSCULOSKELETAL => $suspectedAdrMusculoskeletal,
            TrustCare_Model_PharmacyDictionary::DTYPE_GENERAL => $suspectedAdrGeneral,
            TrustCare_Model_PharmacyDictionary::DTYPE_ADR_INTERVENTION_TYPE => $adrInterventions,
        );

        
        $pharmaciesList = array();
        $pharmModel = new TrustCare_Model_Pharmacy();
        foreach($pharmModel->fetchAll("is_active != 0", "name") as $obj) {
            $pharmaciesList[$obj->getId()] = $obj->getName();
        }    
            
        $this->view->allow_create_patient = Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.patient", "create");
        $this->view->allow_create_pharmacy = Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.pharmacy", "create");
        
        $this->view->idPharmacy = $idPharmacy;
        $this->view->pharmacies = $pharmaciesList;
        $this->view->id_patient = $idPatient;
        $this->view->dateOfVisit = $dateOfVisit;
        $this->view->isPregnant = $isPregnant;
        $this->view->isReceivePrescription = $isReceivePrescription;
        $this->view->isMedErrorScreened = $isMedErrorScreened;
        $this->view->isMedErrorIdentified = $isMedErrorIdentified;
        $this->view->isMedAdhProblemScreened = $isMedAdhProblemScreened;
        $this->view->isMedAdhProblemIdentified = $isMedAdhProblemIdentified;
        $this->view->isMedErrorInterventionProvided = $isMedErrorInterventionProvided;
        $this->view->isAdhInterventionProvided = $isAdhInterventionProvided;
        $this->view->isAdrScreened = $isAdrScreened;
        $this->view->isAdrSymptoms = $isAdrSymptoms;
        $this->view->adrStartDate = $adrStartDate;
        $this->view->adrStopDate = $adrStopDate;
        $this->view->isAdrInterventionProvided = $isAdrInterventionProvided;
        $this->view->isNafdacAdrFilled = $isNafdacAdrFilled;
        $this->view->dictEntities = $dictEntities;
        
        $this->render('create');
        return;
    }
    
    
    
    public function viewActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:form", "view");
    }
    
    public function viewAction()
    {
        $id = $this->_getParam('id');
        $formModel = TrustCare_Model_FrmCare::find($id);
        if(is_null($formModel)) {
            $this->getLogger()->error(sprintf("'%s' tries to view unknown frm_care.id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Form")));
            return;
        }
        
        $patientModel = TrustCare_Model_Patient::find($formModel->getIdPatient());
        if(is_null($patientModel)) {
            $this->getLogger()->error(sprintf("Failed to load patient.id=%s specified for frm_care.id=%s", $formModel->getIdPatient(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Internal Error")));
            return;
        }

        
        $pharmacyModel = TrustCare_Model_Pharmacy::find($formModel->getIdPharmacy());
        if(is_null($pharmacyModel)) {
            $this->getLogger()->error(sprintf("Failed to load pharmacy.id=%s specified for frm_care.id=%s", $formModel->getIdPharmacy(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Internal Error")));
            return;
        }
        
        $medErrorTypes = array();
        $model = new TrustCare_Model_FrmCareMedErrorType();
        foreach($model->fetchAllForFrmCare($formModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $medErrorTypes[] = $dict->getName();
            }
        }
        
        $medAdhProblems = array();
        $model = new TrustCare_Model_FrmCareMedAdhProblem();
        foreach($model->fetchAllForFrmCare($formModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $medAdhProblems[] = $dict->getName();
            }
        }
        
        $medErrorInterventions = array();
        $model = new TrustCare_Model_FrmCareMedErrorIntervention();
        foreach($model->fetchAllForFrmCare($formModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $medErrorInterventions[] = $dict->getName();
            }
        }
        
        $adhInterventions = array();
        $model = new TrustCare_Model_FrmCareAdhIntervention();
        foreach($model->fetchAllForFrmCare($formModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $adhInterventions[] = $dict->getName();
            }
        }

        
        $medErrorInterventionOutcomes = array();
        $model = new TrustCare_Model_FrmCareMedErrorInterventionOutcome();
        foreach($model->fetchAllForFrmCare($formModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $medErrorInterventionOutcomes[] = $dict->getName();
            }
        }
        
        $adhInterventionOutcomes = array();
        $model = new TrustCare_Model_FrmCareAdhInterventionOutcome();
        foreach($model->fetchAllForFrmCare($formModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $adhInterventionOutcomes[] = $dict->getName();
            }
        }
        
        $severityName = '';
        $dict = TrustCare_Model_PharmacyDictionary::find($formModel->getAdrSeverityId());
        if(!is_null($dict)) {
            $severityName = $dict->getName();
        }
        
        $suspectedAdrHepatic = array();
        $model = new TrustCare_Model_FrmCareSuspectedAdrHepatic();
        foreach($model->fetchAllForFrmCare($formModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $suspectedAdrHepatic[] = $dict->getName();
            }
        }
        
        $suspectedAdrNervous = array();
        $model = new TrustCare_Model_FrmCareSuspectedAdrNervous();
        foreach($model->fetchAllForFrmCare($formModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $suspectedAdrNervous[] = $dict->getName();
            }
        }
        
        $suspectedAdrCardiovascular = array();
        $model = new TrustCare_Model_FrmCareSuspectedAdrCardiovascular();
        foreach($model->fetchAllForFrmCare($formModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $suspectedAdrCardiovascular[] = $dict->getName();
            }
        }
        
        $suspectedAdrSkin = array();
        $model = new TrustCare_Model_FrmCareSuspectedAdrSkin();
        foreach($model->fetchAllForFrmCare($formModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $suspectedAdrSkin[] = $dict->getName();
            }
        }
        
        $suspectedAdrMetabolic = array();
        $model = new TrustCare_Model_FrmCareSuspectedAdrMetabolic();
        foreach($model->fetchAllForFrmCare($formModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $suspectedAdrMetabolic[] = $dict->getName();
            }
        }
        
        $suspectedAdrMusculoskeletal = array();
        $model = new TrustCare_Model_FrmCareSuspectedAdrMusculoskeletal();
        foreach($model->fetchAllForFrmCare($formModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $suspectedAdrMusculoskeletal[] = $dict->getName();
            }
        }
        
        $suspectedAdrGeneral = array();
        $model = new TrustCare_Model_FrmCareSuspectedAdrGeneral();
        foreach($model->fetchAllForFrmCare($formModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $suspectedAdrGeneral[] = $dict->getName();
            }
        }
        
        $adrInterventions = array();
        $model = new TrustCare_Model_FrmCareAdrIntervention();
        foreach($model->fetchAllForFrmCare($formModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $adrInterventions[] = $dict->getName();
            }
        }
        
        $this->view->formModel = $formModel;
        $this->view->patientModel = $patientModel;
        $this->view->pharmacyName = $pharmacyModel->getName();
        $this->view->medErrorTypes = $medErrorTypes;
        $this->view->medErrorInterventions = $medErrorInterventions;
        $this->view->medErrorInterventions = $medErrorInterventions;
        $this->view->adhInterventions = $adhInterventions;
        $this->view->medErrorInterventionOutcomes = $medErrorInterventionOutcomes;
        $this->view->adhInterventionOutcomes = $adhInterventionOutcomes;
        $this->view->severityName = $severityName;
        $this->view->suspectedAdrHepatic = $suspectedAdrHepatic;
        $this->view->suspectedAdrNervous = $suspectedAdrNervous;
        $this->view->suspectedAdrCardiovascular = $suspectedAdrCardiovascular;
        $this->view->suspectedAdrSkin = $suspectedAdrSkin;
        $this->view->suspectedAdrMetabolic = $suspectedAdrMetabolic;
        $this->view->suspectedAdrMusculoskeletal = $suspectedAdrMusculoskeletal;
        $this->view->suspectedAdrGeneral = $suspectedAdrGeneral;
        $this->view->adrInterventions = $adrInterventions;
        
        $this->render('view');
        return;
    }

    
    public function editActionAccess()
    {
        return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:form", "edit");
    }
    
    public function editAction()
    {
        $id = $this->_getParam('id');
        
        $db_options = Zend_Registry::get('dbOptions');
        $db = Zend_Db::factory($db_options['adapter'], $db_options['params']);
        
        $frmModel = TrustCare_Model_FrmCare::find($id, array('mapperOptions' => array('adapter' => $db)));
        if(is_null($frmModel)) {
            $this->getLogger()->error(sprintf("'%s' tries to edit unknown frm_care.id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Form")));
            return;
        }
        
        if($frmModel->getIsCommited()) {
            $this->getRedirector()->gotoSimpleAndExit("view", $this->getRequest()->getControllerName(), null, array('id' => $id));
        }
    
        $patientModel = TrustCare_Model_Patient::find($frmModel->getIdPatient(), array('mapperOptions' => array('adapter' => $db)));
        if(is_null($patientModel)) {
            $this->getLogger()->error(sprintf("Failed to load patient.id=%s specified for frm_care.id=%s", $frmModel->getIdPatient(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Internal Error")));
            return;
        }
    
    
        $pharmacyModel = TrustCare_Model_Pharmacy::find($frmModel->getIdPharmacy(), array('mapperOptions' => array('adapter' => $db)));
        if(is_null($pharmacyModel)) {
            $this->getLogger()->error(sprintf("Failed to load pharmacy.id=%s specified for frm_care.id=%s", $frmModel->getIdPharmacy(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Internal Error")));
            return;
        }
        
        
        if($this->getRequest()->isPost()) {
            $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
        
            $db->beginTransaction();
            try {
                $isCommited = $this->_getParam('is_commited');
                $isPregnant = $this->_getParam('is_pregnant');
                $isReceivePrescription = $this->_getParam('is_receive_prescription');
                $isMedErrorScreened = $this->_getParam('is_med_error_screened');
                $isMedErrorIdentified = $this->_getParam('is_med_error_identified');
                $isMedAdhProblemScreened = $this->_getParam('is_med_adh_problem_screened');
                $isMedAdhProblemIdentified = $this->_getParam('is_med_adh_problem_identified');
                $isMedErrorInterventionProvided = $this->_getParam('is_med_error_intervention_provided');
                $isAdhInterventionProvided = $this->_getParam('is_adh_intervention_provided');
                $isAdrScreened = $this->_getParam('is_adr_screened');
                $isAdrSymptoms = $this->_getParam('is_adr_symptoms');
                $adrSeverityId = $this->_getParam('adr_severity_id');
                $adrStartDate = $this->_getParam('adr_start_date');
                $adrStopDate = $this->_getParam('adr_stop_date');
                $isAdrInterventionProvided = $this->_getParam('is_adr_intervention_provided');
                $medErrorTypes = $this->_getParam('med_error_type');
                if(!$isMedErrorIdentified) {
                    $medErrorTypes = array();
                }
                $medAdhProblems = $this->_getParam('med_adh_problem');
                if(!$isMedAdhProblemIdentified) {
                    $medAdhProblems = array();
                }
                $medErrorInterventions = $this->_getParam('med_error_intervention');
                if(!$isMedErrorInterventionProvided) {
                    $medErrorInterventions = array();
                }
                $adhInterventions = $this->_getParam('adh_intervention');
                if(!$isAdhInterventionProvided) {
                    $adhInterventions = array();
                }
                $medErrorInterventionOutcomes = $this->_getParam('med_error_intervention_outcome');
                $adhInterventionOutcomes = $this->_getParam('adh_intervention_outcome');
                $suspectedAdrHepatic = $this->_getParam('suspected_adr_hepatic');
                $suspectedAdrNervous = $this->_getParam('suspected_adr_nervous');
                $suspectedAdrCardiovascular = $this->_getParam('suspected_adr_cardiovascular');
                $suspectedAdrSkin = $this->_getParam('suspected_adr_skin');
                $suspectedAdrMetabolic = $this->_getParam('suspected_adr_metabolic');
                $suspectedAdrMusculoskeletal = $this->_getParam('suspected_adr_musculoskeletal');
                $suspectedAdrGeneral = $this->_getParam('suspected_adr_general');
                $adrInterventions = $this->_getParam('adr_intervention');
                if(!$isAdrInterventionProvided) {
                    $adrInterventions = array();
                }

                if($patientModel->getIsMale()) {
                    $isPregnant = false;
                }

                $frmModel->setIsCommited($isCommited);
                $frmModel->setIsPregnant($isPregnant);
                $frmModel->setIsReceivePrescription($isReceivePrescription);
                $frmModel->setIsMedErrorScreened($isMedErrorScreened);
                $frmModel->setIsMedErrorIdentified($isMedErrorIdentified);
                $frmModel->setIsMedAdhProblemScreened($isMedAdhProblemScreened);
                $frmModel->setIsMedAdhProblemIdentified($isMedAdhProblemIdentified);
                $frmModel->setIsMedErrorInterventionProvided($isMedErrorInterventionProvided);
                $frmModel->setIsAdhInterventionProvided($isAdhInterventionProvided);
                $frmModel->setIsAdrScreened($isAdrScreened);
                $frmModel->setIsAdrSymptoms($isAdrSymptoms);
                $frmModel->setAdrStartDate($adrStartDate);
                $frmModel->setAdrStopDate($adrStopDate);
                $frmModel->setAdrSeverityId($adrSeverityId);
                $frmModel->setIsAdrInterventionProvided($isAdrInterventionProvided);
                $frmModel->save();
        
                TrustCare_Model_FrmCareMedErrorType::replaceForFrmCare($frmModel->getId(), $medErrorTypes, array('mapperOptions' => array('adapter' => $db)));
                TrustCare_Model_FrmCareMedAdhProblem::replaceForFrmCare($frmModel->getId(), $medAdhProblems, array('mapperOptions' => array('adapter' => $db)));
                TrustCare_Model_FrmCareMedErrorIntervention::replaceForFrmCare($frmModel->getId(), $medErrorInterventions, array('mapperOptions' => array('adapter' => $db)));
                TrustCare_Model_FrmCareAdhIntervention::replaceForFrmCare($frmModel->getId(), $adhInterventions, array('mapperOptions' => array('adapter' => $db)));
                TrustCare_Model_FrmCareMedErrorInterventionOutcome::replaceForFrmCare($frmModel->getId(), $medErrorInterventionOutcomes, array('mapperOptions' => array('adapter' => $db)));
                TrustCare_Model_FrmCareAdhInterventionOutcome::replaceForFrmCare($frmModel->getId(), $adhInterventionOutcomes, array('mapperOptions' => array('adapter' => $db)));
                TrustCare_Model_FrmCareSuspectedAdrHepatic::replaceForFrmCare($frmModel->getId(), $suspectedAdrHepatic, array('mapperOptions' => array('adapter' => $db)));
                TrustCare_Model_FrmCareSuspectedAdrNervous::replaceForFrmCare($frmModel->getId(), $suspectedAdrNervous, array('mapperOptions' => array('adapter' => $db)));
                TrustCare_Model_FrmCareSuspectedAdrCardiovascular::replaceForFrmCare($frmModel->getId(), $suspectedAdrCardiovascular, array('mapperOptions' => array('adapter' => $db)));
                TrustCare_Model_FrmCareSuspectedAdrSkin::replaceForFrmCare($frmModel->getId(), $suspectedAdrSkin, array('mapperOptions' => array('adapter' => $db)));
                TrustCare_Model_FrmCareSuspectedAdrMetabolic::replaceForFrmCare($frmModel->getId(), $suspectedAdrMetabolic, array('mapperOptions' => array('adapter' => $db)));
                TrustCare_Model_FrmCareSuspectedAdrMusculoskeletal::replaceForFrmCare($frmModel->getId(), $suspectedAdrMusculoskeletal, array('mapperOptions' => array('adapter' => $db)));
                TrustCare_Model_FrmCareSuspectedAdrGeneral::replaceForFrmCare($frmModel->getId(), $suspectedAdrGeneral, array('mapperOptions' => array('adapter' => $db)));
                TrustCare_Model_FrmCareAdrIntervention::replaceForFrmCare($frmModel->getId(), $adrInterventions, array('mapperOptions' => array('adapter' => $db)));
        
                $db->commit();
                
                $generateNafdacForm = $this->_getParam('generate_nafdac_form');
                $isGenerateNafdacForm = $isCommited ? !empty($generateNafdacForm) : false;
                
                if(!$isGenerateNafdacForm) {
                    $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName());
                }
                else {
                    $this->getRedirector()->gotoSimpleAndExit('create', 'nafdac', null, array('id_frm_care' => $frmModel->getId()));
                }
                
            }
            catch(Exception $ex) {
                $db->rollback();
                $message = $ex->getMessage();
                if(!empty($message)) {
                    $this->getLogger()->error($message);
                }
            }
            $this->view->error = $errorMsg;
        }
        else {
            $isPregnant = $frmModel->getIsPregnant();
            $isReceivePrescription = $frmModel->getIsReceivePrescription();
            $isMedErrorScreened = $frmModel->getIsMedErrorScreened();
            $isMedErrorIdentified = $frmModel->getIsMedErrorIdentified();
            $isMedAdhProblemScreened = $frmModel->getIsMedAdhProblemScreened();
            $isMedAdhProblemIdentified = $frmModel->getIsMedAdhProblemIdentified();
            $isMedErrorInterventionProvided = $frmModel->getIsMedErrorInterventionProvided();
            $isAdhInterventionProvided = $frmModel->getIsAdhInterventionProvided();
            $isAdrScreened = $frmModel->getIsAdrScreened();
            $isAdrSymptoms = $frmModel->getIsAdrSymptoms();
            $adrSeverityId = $frmModel->getAdrSeverityId();
            $adrStartDate = $frmModel->getAdrStartDate();
            $adrStopDate = $frmModel->getAdrStopDate();
            $isAdrInterventionProvided = $frmModel->getIsAdrInterventionProvided();
            
            $medErrorTypes = array();
            $model = new TrustCare_Model_FrmCareMedErrorType(array('mapperOptions' => array('adapter' => $db)));
            foreach($model->fetchAllForFrmCare($frmModel->getId()) as $obj) {
                $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
                if(is_null($dict)) {
                    $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
                }
                else {
                    $medErrorTypes[] = $dict->getId();
                }
            }

            $medAdhProblems = array();
            $model = new TrustCare_Model_FrmCareMedAdhProblem(array('mapperOptions' => array('adapter' => $db)));
            foreach($model->fetchAllForFrmCare($frmModel->getId()) as $obj) {
                $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
                if(is_null($dict)) {
                    $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
                }
                else {
                    $medAdhProblems[] = $dict->getId();
                }
            }

            $medErrorInterventions = array();
            $model = new TrustCare_Model_FrmCareMedErrorIntervention(array('mapperOptions' => array('adapter' => $db)));
            foreach($model->fetchAllForFrmCare($frmModel->getId()) as $obj) {
                $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
                if(is_null($dict)) {
                    $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
                }
                else {
                    $medErrorInterventions[] = $dict->getId();
                }
            }

            $adhInterventions = array();
            $model = new TrustCare_Model_FrmCareAdhIntervention(array('mapperOptions' => array('adapter' => $db)));
            foreach($model->fetchAllForFrmCare($frmModel->getId()) as $obj) {
                $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
                if(is_null($dict)) {
                    $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
                }
                else {
                    $adhInterventions[] = $dict->getId();
                }
            }

            $medErrorInterventionOutcomes = array();
            $model = new TrustCare_Model_FrmCareMedErrorInterventionOutcome(array('mapperOptions' => array('adapter' => $db)));
            foreach($model->fetchAllForFrmCare($frmModel->getId()) as $obj) {
                $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
                if(is_null($dict)) {
                    $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
                }
                else {
                    $medErrorInterventionOutcomes[] = $dict->getId();
                }
            }

            $adhInterventionOutcomes = array();
            $model = new TrustCare_Model_FrmCareAdhInterventionOutcome(array('mapperOptions' => array('adapter' => $db)));
            foreach($model->fetchAllForFrmCare($frmModel->getId()) as $obj) {
                $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
                if(is_null($dict)) {
                    $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
                }
                else {
                    $adhInterventionOutcomes[] = $dict->getId();
                }
            }

            $suspectedAdrHepatic = array();
            $model = new TrustCare_Model_FrmCareSuspectedAdrHepatic(array('mapperOptions' => array('adapter' => $db)));
            foreach($model->fetchAllForFrmCare($frmModel->getId()) as $obj) {
                $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
                if(is_null($dict)) {
                    $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
                }
                else {
                    $suspectedAdrHepatic[] = $dict->getId();
                }
            }

            $suspectedAdrNervous = array();
            $model = new TrustCare_Model_FrmCareSuspectedAdrNervous(array('mapperOptions' => array('adapter' => $db)));
            foreach($model->fetchAllForFrmCare($frmModel->getId()) as $obj) {
                $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
                if(is_null($dict)) {
                    $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
                }
                else {
                    $suspectedAdrNervous[] = $dict->getId();
                }
            }

            $suspectedAdrCardiovascular = array();
            $model = new TrustCare_Model_FrmCareSuspectedAdrCardiovascular(array('mapperOptions' => array('adapter' => $db)));
            foreach($model->fetchAllForFrmCare($frmModel->getId()) as $obj) {
                $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
                if(is_null($dict)) {
                    $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
                }
                else {
                    $suspectedAdrCardiovascular[] = $dict->getId();
                }
            }

            $suspectedAdrSkin = array();
            $model = new TrustCare_Model_FrmCareSuspectedAdrSkin(array('mapperOptions' => array('adapter' => $db)));
            foreach($model->fetchAllForFrmCare($frmModel->getId()) as $obj) {
                $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
                if(is_null($dict)) {
                    $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
                }
                else {
                    $suspectedAdrSkin[] = $dict->getId();
                }
            }

            $suspectedAdrMetabolic = array();
            $model = new TrustCare_Model_FrmCareSuspectedAdrMetabolic(array('mapperOptions' => array('adapter' => $db)));
            foreach($model->fetchAllForFrmCare($frmModel->getId()) as $obj) {
                $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
                if(is_null($dict)) {
                    $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
                }
                else {
                    $suspectedAdrMetabolic[] = $dict->getId();
                }
            }

            $suspectedAdrMusculoskeletal = array();
            $model = new TrustCare_Model_FrmCareSuspectedAdrMusculoskeletal(array('mapperOptions' => array('adapter' => $db)));
            foreach($model->fetchAllForFrmCare($frmModel->getId()) as $obj) {
                $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
                if(is_null($dict)) {
                    $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
                }
                else {
                    $suspectedAdrMusculoskeletal[] = $dict->getId();
                }
            }

            $suspectedAdrGeneral = array();
            $model = new TrustCare_Model_FrmCareSuspectedAdrGeneral(array('mapperOptions' => array('adapter' => $db)));
            foreach($model->fetchAllForFrmCare($frmModel->getId()) as $obj) {
                $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
                if(is_null($dict)) {
                    $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
                }
                else {
                    $suspectedAdrGeneral[] = $dict->getId();
                }
            }

            $adrInterventions = array();
            $model = new TrustCare_Model_FrmCareAdrIntervention(array('mapperOptions' => array('adapter' => $db)));
            foreach($model->fetchAllForFrmCare($frmModel->getId()) as $obj) {
                $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
                if(is_null($dict)) {
                    $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_care.id=%s", $obj->getIdPharmacyDictionary(), $id));
                }
                else {
                    $adrInterventions[] = $dict->getId();
                }
            }

        }
        
        $dictEntities = array(
            TrustCare_Model_PharmacyDictionary::DTYPE_MEDICATION_ERROR_TYPE => $medErrorTypes,
            TrustCare_Model_PharmacyDictionary::DTYPE_MEDICATION_ADH_PROBLEM => $medAdhProblems,
            TrustCare_Model_PharmacyDictionary::DTYPE_MED_ERROR_INTERVENTION_PROVIDED => $medErrorInterventions,
            TrustCare_Model_PharmacyDictionary::DTYPE_ADH_INTERVENTION_PROVIDED => $adhInterventions,
            TrustCare_Model_PharmacyDictionary::DTYPE_MED_ERROR_INTERVENTION_OUTCOME => $medErrorInterventionOutcomes,
            TrustCare_Model_PharmacyDictionary::DTYPE_ADH_INTERVENTION_OUTCOME => $adhInterventionOutcomes,
            TrustCare_Model_PharmacyDictionary::DTYPE_ADR_SEVERITY_GRADE => array($adrSeverityId),
            TrustCare_Model_PharmacyDictionary::DTYPE_HEPATIC => $suspectedAdrHepatic,
            TrustCare_Model_PharmacyDictionary::DTYPE_NERVOUS => $suspectedAdrNervous,
            TrustCare_Model_PharmacyDictionary::DTYPE_CARDIOVASCULAR => $suspectedAdrCardiovascular,
            TrustCare_Model_PharmacyDictionary::DTYPE_SKIN => $suspectedAdrSkin,
            TrustCare_Model_PharmacyDictionary::DTYPE_METABOLIC => $suspectedAdrMetabolic,
            TrustCare_Model_PharmacyDictionary::DTYPE_MUSCULOSKELETAL => $suspectedAdrMusculoskeletal,
            TrustCare_Model_PharmacyDictionary::DTYPE_GENERAL => $suspectedAdrGeneral,
            TrustCare_Model_PharmacyDictionary::DTYPE_ADR_INTERVENTION_TYPE => $adrInterventions,
        );
        
        $this->view->formModel = $frmModel;
        $this->view->patientModel = $patientModel;
        $this->view->pharmacyName = $pharmacyModel->getName();
        $this->view->isPregnant = $isPregnant;
        $this->view->isReceivePrescription = $isReceivePrescription;
        $this->view->isMedErrorScreened = $isMedErrorScreened;
        $this->view->isMedErrorIdentified = $isMedErrorIdentified;
        $this->view->isMedAdhProblemScreened = $isMedAdhProblemScreened;
        $this->view->isMedAdhProblemIdentified = $isMedAdhProblemIdentified;
        $this->view->isMedErrorInterventionProvided = $isMedErrorInterventionProvided;
        $this->view->isAdhInterventionProvided = $isAdhInterventionProvided;
        $this->view->isAdrScreened = $isAdrScreened;
        $this->view->isAdrSymptoms = $isAdrSymptoms;
        $this->view->adrStartDate = $adrStartDate;
        $this->view->adrStopDate = $adrStopDate;
        $this->view->isAdrInterventionProvided = $isAdrInterventionProvided;
        $this->view->dictEntities = $dictEntities;
        
        $this->render('edit');
        return;
    }
    
    public function deleteActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:form", "delete");
    }
    
    public function deleteAction()
    {
        $id = $this->_getParam('id');
        $formModel = TrustCare_Model_FrmCare::find($id);
        if(is_null($formModel)) {
            $this->getLogger()->error(sprintf("'%s' tries to edit unknown frm_care.id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Form")));
            return;
        }
        
        $formModel->delete();
        $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName());
    }
    
    
}

