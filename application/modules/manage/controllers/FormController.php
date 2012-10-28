<?php

class FormController extends ZendX_Controller_Action
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
        $type = $this->_getParam('type');
        if('care' != $type && 'community' != $type) {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Incorrect type of counseling")));
            return;
        }
        
        $columnsInfo = array(
            'id' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("ID"),
                'width' => '5%',
            ),
            'date_of_visit' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Date of Visit"),
                'width' => '15%',
            ),
            'patient_identifier' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Patient ID"),
                'filter' => array(
                    'type' => 'text',
                ),
            ),
            'patient_last_name' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Last Name"),
            ),
            'patient_first_name' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("First Name"),
            ),
        );

        $this->view->DataTable = array(
            'serverUrl' => $this->view->url(array('action' => 'list-load')),
            'params' => array(
                'type' => $type,
            ),
            'toolbar' => array(
                array(
                    'text' => Zend_Registry::get("Zend_Translate")->_("Create"),
                    'url' => $this->view->url(array('action' => 'create', 'type' => $type))
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
        $type = $this->_getParam('type');
        if('care' == $type || 'community' == $type) {
            if('community' == $type) {
                $table = 'frm_community';
            }
            else {
                $table = 'frm_care';
            }
            
            $select = Zend_Registry::getInstance()->dbAdapter->select()->from($table, array('count(id)'));
            Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_NUM);
            $result = Zend_Registry::getInstance()->dbAdapter->fetchAll($select);
            $iTotal = $result[0][0];


            Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_ASSOC);
            $select = Zend_Registry::getInstance()->dbAdapter->select()
                                                             ->from($table,
                                                                array(
                                                                    $table.'.id',
                                                                    'date_of_visit' => new Zend_Db_Expr("date_format(date_of_visit, '%Y-%m-%d')"),
                                                                    ))
                                                             ->joinLeft(array('patient'), $table.'.id_patient = patient.id', array('patient_identifier' => 'patient.identifier',
                                                                                                                                  'patient_first_name' => 'patient.first_name',
                                                                                                                                  'patient_last_name' => 'patient.last_name'));

            $this->processListLoadAjaxRequest($select, array('patient_identifier' => 'patient.identifier',
                                                             'patient_first_name' => 'patient.first_name',
                                                             'patient_last_name' => 'patient.last_name'));

            $rows = Zend_Registry::getInstance()->dbAdapter->selectWithLimit($select->__toString(), $iFilteredTotal);
        }
                
        
        $output = array(
            "sEcho" => intval($_REQUEST['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );
        foreach ($rows as $row) {
            $row['DT_RowId'] = $row['id'];
            $row['date_of_visit'] = $this->convertDateToUserTimezone($row['date_of_visit']);
            
            $row['_row_actions_'] = array(
                array(
                    'title' => Zend_Registry::get("Zend_Translate")->_("View"),
                    'url' => $this->view->url(array('action' => 'view', 'id' => $row['id'], 'type' => $type)),
                    'type' => 'edit'
                ),
                array(
                    'title' => Zend_Registry::get("Zend_Translate")->_("Delete"),
                    'url' => $this->view->url(array('action' => 'delete', 'id' => $row['id'], 'type' => $type)),
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
        $type = $this->_getParam('type');
        if('care' == $type) {
            return $this->_createCareForm();; 
        }
        else if('community' == $type) {
            return $this->_createCommunityForm();; 
        }
        else {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Incorrect type of counseling")));
            return;
        }
    }
    
    private function _createCareForm()
    {
        if($this->getRequest()->isPost()) {
            $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
            
            $db_options = Zend_Registry::get('dbOptions');
            $db = Zend_Db::factory($db_options['adapter'], $db_options['params']);
            $db->beginTransaction();
            try {
                $idPatient = $this->_getParam('id_patient');
                if(empty($idPatient)) {
                    $errorMsg = Zend_Registry::get("Zend_Translate")->_("Necessary to choose patient");
                    throw new Exception('');
                }
                $dateOfVisit = $this->_getParam('date_of_visit');
                if(empty($dateOfVisit)) {
                    $errorMsg = Zend_Registry::get("Zend_Translate")->_("Necessary to enter date of visit");
                    throw new Exception('');
                }
                
                $patientModel = TrustCare_Model_Patient::find($idPatient);
                if(is_null($patientModel)) {
                    throw new Exception(sprintf("Trying to create form for unknown patient.id=%s", $idPatient));
                }
                $isPregnant = $this->_getParam('is_pregnant');
                $isReceivePrescription = $this->_getParam('is_receive_prescription');
                $isMedErrorScreened = $this->_getParam('is_med_error_screened');
                $isMedErrorIdentified = $this->_getParam('is_med_error_identified');
                $isMedAdhProblemScreened = $this->_getParam('is_med_adh_problem_screened');
                $isMedAdhProblemIdentified = $this->_getParam('is_med_adh_problem_identified');
                $isAdhInterventionProvided = $this->_getParam('is_adh_intervention_provided');
                $isAdrScreened = $this->_getParam('is_adr_screened');
                $isAdrSymptoms = $this->_getParam('is_adr_symptoms');
                $adrSeverityId = $this->_getParam('adr_severity_id');
                $adrStartDate = $this->_getParam('adr_start_date');
                $adrStopDate = $this->_getParam('adr_stop_date');
                
                $frmModel = new TrustCare_Model_FrmCare(
                    array(
                		'id_patient' => $idPatient,
                		'date_of_visit' => $dateOfVisit.' 00:00:00',
                		'is_pregnant' => $isPregnant,
                		'is_receive_prescription' => $isReceivePrescription,
                		'is_med_error_screened' => $isMedErrorScreened,
                		'is_med_error_identified' => $isMedErrorIdentified,
                		'is_med_adh_problem_screened' => $isMedAdhProblemScreened,
                		'is_med_adh_problem_identified' => $isMedAdhProblemIdentified,
                		'is_adh_intervention_provided' => $isAdhInterventionProvided,
                		'is_adr_screened' => $isAdrScreened,
                		'is_adr_symptoms' => $isAdrSymptoms,
                		'adr_severity_id' => $adrSeverityId,
                'is_adr_intervention_provided' => true,
                'is_nafdac_adr_filled' => false,
                'is_patient_younger_15' => true,
                'is_patient_male' => false,

                    	'mapperOptions' => array('adapter' => $db)
                    )
                );
                if(!empty($adrStartDate)) {
                    $frmModel->setAdrStartDate($adrStartDate . ' 00:00:00');
                }
                if(!empty($adrStopDate)) {
                    $frmModel->setAdrStopDate($adrStopDate . ' 00:00:00');
                }
                $frmModel->save();
                
                $medErrorTypes = $this->_getParam('med_error_type');
                if(!$isMedErrorIdentified) {
                    $medErrorTypes = array();
                }
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
                
                $medAdhProblems = $this->_getParam('med_adh_problem');
                if(!$isMedAdhProblemIdentified) {
                    $medAdhProblems = array();
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
                
                $adhInterventions = $this->_getParam('adh_intervention');
                if(!$isAdhInterventionProvided) {
                    $adhInterventions = array();
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
                
                $adhInterventionOutcomes = $this->_getParam('adh_intervention_outcome');
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
                
                throw new Exception('');

                $db->commit();
                $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName());
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
            $isAdhInterventionProvided = true;
            $adhInterventions = array();
            $adhInterventionOutcomes = array();
            $isAdrScreened = true;
            $isAdrSymptoms = false;
            $adrSeverityId = null;
            $adrStartDate = '';
            $adrStopDate = '';
        }
        
        $dictEntities = array(
            TrustCare_Model_PharmacyDictionary::DTYPE_MEDICATION_ERROR_TYPE => $medErrorTypes,
            TrustCare_Model_PharmacyDictionary::DTYPE_MEDICATION_ADH_PROBLEM => $medAdhProblems,
            TrustCare_Model_PharmacyDictionary::DTYPE_ADH_INTERVENTION_PROVIDED => $adhInterventions,
            TrustCare_Model_PharmacyDictionary::DTYPE_ADH_INTERVENTION_OUTCOME => $adhInterventionOutcomes,
            TrustCare_Model_PharmacyDictionary::DTYPE_ADR_SEVERITY_GRADE => array($adrSeverityId),
            );
        
        $this->view->type = 'care';
        $this->view->allow_create_patient = Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.patient", "create");
        
        $this->view->id_patient = $idPatient;
        $this->view->dateOfVisit = $dateOfVisit;
        $this->view->isPregnant = $isPregnant;
        $this->view->isReceivePrescription = $isReceivePrescription;
        $this->view->isMedErrorScreened = $isMedErrorScreened;
        $this->view->isMedErrorIdentified = $isMedErrorIdentified;
        $this->view->isMedAdhProblemScreened = $isMedAdhProblemScreened;
        $this->view->isMedAdhProblemIdentified = $isMedAdhProblemIdentified;
        $this->view->isAdhInterventionProvided = $isAdhInterventionProvided;
        $this->view->isAdrScreened = $isAdrScreened;
        $this->view->isAdrSymptoms = $isAdrSymptoms;
        $this->view->adrStartDate = $adrStartDate;
        $this->view->adrStopDate = $adrStopDate;
        $this->view->dictEntities = $dictEntities;
        
        $this->render('create-care');
        return;
    }
    
    
    private function _createCommunityForm()
    {
        
    }
    
}

