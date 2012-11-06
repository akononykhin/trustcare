<?php

class ReportController extends ZendX_Controller_Action
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
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:report", "view");
    }
    
    public function listAction()
    {
        $type = $this->_getParam('type');
        if('care' != $type && 'community' != $type) {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Incorrect type of the report")));
            return;
        }
        
        $columnsInfo = array(
            'id' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("ID"),
                'width' => '5%',
            ),
            'generation_date' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Generation Date"),
                'width' => '15%',
            ),
            'period' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Period"),
                'filter' => array(
                    'type' => 'text',
                ),
            ),
            'user_login' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("User ID"),
                'filter' => array(
                    'type' => 'text',
                ),
            ),
        );

        $this->view->DataTable = array(
            'serverUrl' => $this->view->url(array('action' => 'list-load')),
            'params' => array(
                'type' => $type,
            ),
            'toolbar' => array(
                array(
                    'text' => Zend_Registry::get("Zend_Translate")->_("Generate"),
                    'url' => $this->view->url(array('action' => 'generate', 'type' => $type))
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
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:report", "view");
    }
    
    
    public function listLoadAction()
    {
        $type = $this->_getParam('type');
        if('care' == $type || 'community' == $type) {
            if('community' == $type) {
                $table = 'report_community';
            }
            else {
                $table = 'report_care';
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
																	'generation_date' => new Zend_Db_Expr(sprintf("date_format(%s.generation_date, '%%Y-%%m-%%d %%H:%%i:%%s')", $table)),
                                                                    $table.'.period'
                                                                    ))
                                                             ->joinLeft(array('user'), $table.'.id_user = user.id', array('user_login' => 'user.login'));
                                                             
            $this->processListLoadAjaxRequest($select, array('user_login' => 'user.login'));

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
            $row['generation_date'] = $this->convertTimeToUserTimezone($row['generation_date']);
            $period = $row['period'];
            if(preg_match("/^(\d{4})(\d{2})$/", $period, $matches)) {
                $row['period'] = $matches[1].'-'.$matches[2];
            }

                        
            $row['_row_actions_'] = array(
                array(
                    'title' => Zend_Registry::get("Zend_Translate")->_("View"),
                    'url' => $this->view->url(array('action' => 'view', 'id' => $row['id'], 'type' => $type)),
                    'type' => 'view'
                ),
                array(
                    'title' => Zend_Registry::get("Zend_Translate")->_("Delete"),
                    'url' => $this->view->url(array('action' => 'delete', 'id' => $row['id'], 'type' => $type)),
                	'type' => 'delete',
                	'askConfirm' => sprintf(Zend_Registry::get("Zend_Translate")->_("Are you sure you want to delete report %s generated %s?"), $row['id'], $row['generation_date']),
                ),
            );
            $output['aaData'][] = $row;
        }

                                              
        $this->_helper->json($output);
    }
    
    
    public function generateActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:report", "create");
    }
    
    public function generateAction()
    {
        $type = $this->_getParam('type');
        if('care' == $type) {
            return $this->_generateCareReport(); 
        }
        else if('community' == $type) {
            return $this->_createCommunityForm(); 
        }
        else {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Incorrect type of report")));
            return;
        }
    }
    
    private function _generateCareReport()
    {
        $form = $this->_getGenerateCareReportForm();
        $form->setAction($this->getRequest()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . "/generate/type/" . $this->_getParam('type'));
        
        if($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
                try {
                    $idPharmacy = $form->getValue('id_pharmacy');
                    $period = $form->getValue('period');
                    if(!preg_match("/^(\d{4})-(\d{2})$/", $period, $matches)) {
                        throw new Exception(sprintf("Incorrect period=%s for generating report.", $period));
                    }
                    $month = $matches[2];
                    $year = $matches[1];
                    $male_younger_15 = $form->getValue('male_younger_15');
                    $female_younger_15 = $form->getValue('female_younger_15');
                    $male_from_15 = $form->getValue('male_from_15');
                    $female_from_15 = $form->getValue('female_from_15');
                    $drugs = $form->getValue('drugs');
                    
                    $generator = TrustCare_SystemInterface_ReportGenerator_Abstract::factory(TrustCare_SystemInterface_ReportGenerator_Abstract::CODE_CARE);
                    
                    $obj = $generator->generate(array(
                                'id_user' => Zend_Registry::get("TrustCare_Registry_User")->getUser()->getId(),
                                'id_pharmacy' => $idPharmacy,
                                'year' => $year,
                                'month' => $month,
                                'male_younger_15' => $male_younger_15,
                                'female_younger_15' => $female_younger_15,
                                'male_from_15' => $male_from_15,
                                'female_from_15' => $female_from_15,
                                'drugs' => $drugs
                    ));
                    $fileReportOutput = sprintf("%s/%s", $generator->reportsDirectory(), $obj->getFilename());
                    
                    if(!file_exists($fileReportOutput)) {
                        $errorMsg = Zend_Registry::get("Zend_Translate")->_("Report file not found");
                        throw new Exception(sprintf("Report file '%s' not found", $fileReportOutput));
                    }

                    $this->outputFileAsAttachment($fileReportOutput);
                    return;
                }
                catch(Exception $ex) {
                    $message = $ex->getMessage();
                    if(!empty($message)) {
                        $this->getLogger()->error($message);
                    }
                }
                $form->addError($errorMsg);
            }
        }
                
        $this->view->form = $form;
        $this->render('general-form', null, true);
        return;
    }
    
    
    private function _createCommunityForm()
    {
        if($this->getRequest()->isPost()) {
            $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
            
            $db_options = Zend_Registry::get('dbOptions');
            $db = Zend_Db::factory($db_options['adapter'], $db_options['params']);
            $db->beginTransaction();
            try {
                $idPharmacy = $this->_getParam('id_pharmacy');
                $idPatient = $this->_getParam('id_patient');
                $dateOfVisit = $this->_getParam('date_of_visit');
                $isReferredIn = $this->_getParam('is_referred_in');
                $isReferredOut = $this->_getParam('is_referred_out');
                $isReferralCompleted = $this->_getParam('is_referral_completed');
                $isHivRiskAssesmentDone = $this->_getParam('is_hiv_risk_assesment_done');
                $referredInList = $this->_getParam('referred_in');
                if(!$isReferredIn) {
                    $referredInList = array();
                }
                $referredOutList = $this->_getParam('referred_out');
                if(!$isReferredOut) {
                    $referredOutList = array();
                }
                $isHtcDone = $this->_getParam('is_htc_done');
                $htcResultId = $this->_getParam('htc_result_id');
                if(!$isHtcDone) {
                    $htcResultId = null;
                }
                $isClientReceivedHtc = $this->_getParam('is_client_received_htc');
                $isHtcDoneInCurrentPharmacy = $this->_getParam('is_htc_done_in_current_pharmacy');
                $isPalliativeServicesToPlwha = $this->_getParam('is_palliative_services_to_plwha');
                $palliativeCareTypeList = $this->_getParam('palliative_care_type');
                if(!$isPalliativeServicesToPlwha) {
                    $palliativeCareTypeList = array();
                }
                $isStiServices = $this->_getParam('is_sti_services');
                $stiTypeList = $this->_getParam('sti_type');
                if(!$isStiServices) {
                    $stiTypeList = array();
                }
                $isReproductiveHealthServices = $this->_getParam('is_reproductive_health_services');
                $reproductiveHealthTypeList = $this->_getParam('reproductive_health_type');
                if(!$isReproductiveHealthServices) {
                    $reproductiveHealthTypeList = array();
                }
                $isTuberculosisServices =  $this->_getParam('is_tuberculosis_services');
                $tuberculosisTypeList = $this->_getParam('tuberculosis_type');
                if(!$isTuberculosisServices) {
                    $tuberculosisTypeList = array();
                }
                $isOvcServices = $this->_getParam('is_ovc_services');
                $ovcTypeList = $this->_getParam('ovc_type');
                if(!$isOvcServices) {
                    $ovcTypeList = array();
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
                
                $frmModel = new TrustCare_Model_ReportCommunity(
                    array(
                        'id_pharmacy' => $idPharmacy,
                		'id_patient' => $idPatient,
                		'date_of_visit' => $dateOfVisit,
                		'is_first_visit_to_pharmacy' => TrustCare_Model_ReportCommunity::isFirstVisitOfPatientToPharmacy($idPatient, $idPharmacy),
                		'is_referred_in' => $isReferredIn,
                		'is_referred_out' => $isReferredOut,
                		'is_referral_completed' => $isReferralCompleted,
                		'is_hiv_risk_assesment_done' => $isHivRiskAssesmentDone,
                		'is_htc_done' => $isHtcDone,
                        'htc_result_id' => $htcResultId,
                		'is_client_received_htc' => $isClientReceivedHtc,
                		'is_htc_done_in_current_pharmacy' => $isHtcDoneInCurrentPharmacy,
                		'is_palliative_services_to_plwha' => $isPalliativeServicesToPlwha,
                		'is_sti_services' => $isStiServices,
                		'is_reproductive_health_services' => $isReproductiveHealthServices,
                		'is_tuberculosis_services' => $isTuberculosisServices,
                		'is_ovc_services' => $isOvcServices,
                		'is_patient_younger_15' => $isPatientYounger15,
                		'is_patient_male' => $patientModel->getIsMale(),
                    	'mapperOptions' => array('adapter' => $db)
                    )
                );
                $frmModel->save();
                
                foreach($referredInList  as $dictId) {
                    $model = new TrustCare_Model_ReportCommunityReferredIn(
                        array(
          					'id_frm_community' => $frmModel->getId(),
           					'id_pharmacy_dictionary' => $dictId,
                           	'mapperOptions' => array('adapter' => $db)
                        )
                    );
                    $model->save();
                }
                
                foreach($referredOutList  as $dictId) {
                    $model = new TrustCare_Model_ReportCommunityReferredOut(
                        array(
          					'id_frm_community' => $frmModel->getId(),
           					'id_pharmacy_dictionary' => $dictId,
                           	'mapperOptions' => array('adapter' => $db)
                        )
                    );
                    $model->save();
                }
                
                foreach($palliativeCareTypeList  as $dictId) {
                    $model = new TrustCare_Model_ReportCommunityPalliativeCareType(
                        array(
          					'id_frm_community' => $frmModel->getId(),
           					'id_pharmacy_dictionary' => $dictId,
                           	'mapperOptions' => array('adapter' => $db)
                        )
                    );
                    $model->save();
                }
                
                foreach($stiTypeList  as $dictId) {
                    $model = new TrustCare_Model_ReportCommunityStiType(
                        array(
          					'id_frm_community' => $frmModel->getId(),
           					'id_pharmacy_dictionary' => $dictId,
                           	'mapperOptions' => array('adapter' => $db)
                        )
                    );
                    $model->save();
                }
                
                foreach($reproductiveHealthTypeList  as $dictId) {
                    $model = new TrustCare_Model_ReportCommunityReproductiveHealthType(
                        array(
          					'id_frm_community' => $frmModel->getId(),
           					'id_pharmacy_dictionary' => $dictId,
                           	'mapperOptions' => array('adapter' => $db)
                        )
                    );
                    $model->save();
                }
                
                foreach($tuberculosisTypeList  as $dictId) {
                    $model = new TrustCare_Model_ReportCommunityTuberculosisType(
                        array(
          					'id_frm_community' => $frmModel->getId(),
           					'id_pharmacy_dictionary' => $dictId,
                           	'mapperOptions' => array('adapter' => $db)
                        )
                    );
                    $model->save();
                }
                
                foreach($ovcTypeList  as $dictId) {
                    $model = new TrustCare_Model_ReportCommunityOvcType(
                        array(
          					'id_frm_community' => $frmModel->getId(),
           					'id_pharmacy_dictionary' => $dictId,
                           	'mapperOptions' => array('adapter' => $db)
                        )
                    );
                    $model->save();
                }
                
                $db->commit();
                $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName(), null, array('type' => $this->_getParam('type')));
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
            $isReferredIn = true;
            $referredInList = array();
            $isReferredOut = true;
            $referredOutList = array();
            $isReferralCompleted = false;
            $isHivRiskAssesmentDone = false;
            $isHtcDone = false;
            $htcResultId = null;
            $isClientReceivedHtc = false;
            $isHtcDoneInCurrentPharmacy = false;
            $isPalliativeServicesToPlwha = false;
            $palliativeCareTypeList = array();
            $isStiServices = false;
            $stiTypeList = array();
            $isReproductiveHealthServices = false;
            $reproductiveHealthTypeList = array();
            $isTuberculosisServices = false;
            $tuberculosisTypeList = array();
            $isOvcServices = false;
            $ovcTypeList = array();
        }
        
        $dictEntities = array(
            TrustCare_Model_PharmacyDictionary::DTYPE_REFERRED_IN => $referredInList,
            TrustCare_Model_PharmacyDictionary::DTYPE_REFERRED_OUT => $referredOutList,
            TrustCare_Model_PharmacyDictionary::DTYPE_HTC_RESULT => array($htcResultId),
            TrustCare_Model_PharmacyDictionary::DTYPE_PALLIATIVE_CARE_TYPE => $palliativeCareTypeList,
            TrustCare_Model_PharmacyDictionary::DTYPE_STI_TYPE => $stiTypeList,
            TrustCare_Model_PharmacyDictionary::DTYPE_REPRODUCTIVE_HEALTH_TYPE => $reproductiveHealthTypeList,
            TrustCare_Model_PharmacyDictionary::DTYPE_TUBERCULOSIS_TYPE => $tuberculosisTypeList,
            TrustCare_Model_PharmacyDictionary::DTYPE_OVC_TYPE => $ovcTypeList,
        );
        
        $pharmaciesList = array();
        $pharmModel = new TrustCare_Model_Pharmacy();
        foreach($pharmModel->fetchAll("is_active != 0", "name") as $obj) {
            $pharmaciesList[$obj->getId()] = $obj->getName();
        }    
            
        $this->view->type = 'community';
        $this->view->allow_create_patient = Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.patient", "create");
        
        $this->view->idPharmacy = $idPharmacy;
        $this->view->pharmacies = $pharmaciesList;
        $this->view->id_patient = $idPatient;
        $this->view->dateOfVisit = $dateOfVisit;
        $this->view->isReferredIn = $isReferredIn;
        $this->view->isReferredOut = $isReferredOut;
        $this->view->isReferralCompleted = $isReferralCompleted;
        $this->view->isHivRiskAssesmentDone = $isHivRiskAssesmentDone;
        $this->view->isHtcDone = $isHtcDone;
        $this->view->isClientReceivedHtc = $isClientReceivedHtc;
        $this->view->isHtcDoneInCurrentPharmacy = $isHtcDoneInCurrentPharmacy;
        $this->view->isPalliativeServicesToPlwha = $isPalliativeServicesToPlwha;
        $this->view->isStiServices = $isStiServices;
        $this->view->isReproductiveHealthServices = $isReproductiveHealthServices;
        $this->view->isTuberculosisServices = $isTuberculosisServices;
        $this->view->isOvcServices = $isOvcServices;
        $this->view->dictEntities = $dictEntities;
        
        $this->render('create-community');
        return;
    }
    
    
    public function viewActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:report", "view");
    }
    
    public function viewAction()
    {
        $type = $this->_getParam('type');
        if('care' == $type) {
            return $this->_viewCareReport();; 
        }
        else if('community' == $type) {
            return $this->_viewCommunityReport();; 
        }
        else {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Incorrect type of counseling")));
            return;
        }
    }
    
    private function _viewCareReport()
    {
        $id = $this->_getParam('id');
        $reportModel = TrustCare_Model_ReportCare::find($id);
        if(is_null($reportModel)) {
            $this->getLogger()->error(sprintf("'%s' tries to view unknown report_care.id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Report")));
            return;
        }

        $pharmacyModel = TrustCare_Model_Pharmacy::find($reportModel->getIdPharmacy());
        if(is_null($pharmacyModel)) {
            $this->getLogger()->error(sprintf("Failed to load pharmacy.id=%s specified for report_care.id=%s", $reportModel->getIdPharmacy(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Internal Error")));
            return;
        }

        $authorModel = TrustCare_Model_User::find($reportModel->getIdUser());
        if(is_null($pharmacyModel)) {
            $this->getLogger()->error(sprintf("Failed to load user.id=%s specified for report_care.id=%s", $reportModel->getIdUser(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Internal Error")));
            return;
        }
        
        $this->view->type = $this->_getParam('type');
        $this->view->id = $this->_getParam('id');
        $this->view->reportModel = $reportModel;
        $this->view->pharmacyName = $pharmacyModel->getName();
        $this->view->authorName = $authorModel->getLogin();
        $this->render('view-care');
        return;
    }
    
    private function _viewCommunityReport()
    {
        $id = $this->_getParam('id');
        $reportModel = TrustCare_Model_ReportCommunity::find($id);
        if(is_null($reportModel)) {
            $this->getLogger()->error(sprintf("'%s' tries to edit unknown frm_community.id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Form")));
            return;
        }
        
        $pharmacyModel = TrustCare_Model_Pharmacy::find($reportModel->getIdPharmacy());
        if(is_null($pharmacyModel)) {
            $this->getLogger()->error(sprintf("Failed to load pharmacy.id=%s specified for frm_community.id=%s", $reportModel->getIdPharmacy(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Internal Error")));
            return;
        }
        
        $referredInList = array();
        $model = new TrustCare_Model_ReportCommunityReferredIn();
        foreach($model->fetchAllForFrmCommunity($reportModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_community.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $referredInList[] = $dict->getName();
            }
        }
        
        $referredOutList = array();
        $model = new TrustCare_Model_ReportCommunityReferredOut();
        foreach($model->fetchAllForFrmCommunity($reportModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_community.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $referredOutList[] = $dict->getName();
            }
        }
        
        $htcResultName = '';
        $dict = TrustCare_Model_PharmacyDictionary::find($reportModel->getHtcResultId());
        if(!is_null($dict)) {
            $htcResultName = $dict->getName();
        }
        
        $palliativeCareTypeList = array();
        $model = new TrustCare_Model_ReportCommunityPalliativeCareType();
        foreach($model->fetchAllForFrmCommunity($reportModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_community.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $palliativeCareTypeList[] = $dict->getName();
            }
        }
        
        $stiTypeList = array();
        $model = new TrustCare_Model_ReportCommunityStiType();
        foreach($model->fetchAllForFrmCommunity($reportModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_community.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $stiTypeList[] = $dict->getName();
            }
        }
        
        $reproductiveHealthTypeList = array();
        $model = new TrustCare_Model_ReportCommunityReproductiveHealthType();
        foreach($model->fetchAllForFrmCommunity($reportModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_community.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $reproductiveHealthTypeList[] = $dict->getName();
            }
        }
        
        $tuberculosisTypeList = array();
        $model = new TrustCare_Model_ReportCommunityTuberculosisType();
        foreach($model->fetchAllForFrmCommunity($reportModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_community.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $tuberculosisTypeList[] = $dict->getName();
            }
        }
        
        $ovcTypeList = array();
        $model = new TrustCare_Model_ReportCommunityOvcType();
        foreach($model->fetchAllForFrmCommunity($reportModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_community.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $ovcTypeList[] = $dict->getName();
            }
        }
        
        $this->view->reportModel = $reportModel;
        $this->view->patientModel = $patientModel;
        $this->view->pharmacyName = $pharmacyModel->getName();
        $this->view->referredInList = $referredInList;
        $this->view->referredOutList = $referredOutList;
        $this->view->htcResultName = $htcResultName;
        $this->view->palliativeCareTypeList = $palliativeCareTypeList;
        $this->view->stiTypeList = $stiTypeList;
        $this->view->reproductiveHealthTypeList = $reproductiveHealthTypeList;
        $this->view->tuberculosisTypeList = $tuberculosisTypeList;
        $this->view->ovcTypeList = $ovcTypeList;
        
        $this->render('view-community');
        return;
    }
    
    
    
    public function loadReportActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:report", "view");
    }
    
    public function loadReportAction()
    {
        $type = $this->_getParam('type');
        if('care' == $type) {
            return $this->_loadCareReport();; 
        }
        else if('community' == $type) {
            return $this->_loadCommunityReport();; 
        }
        else {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Incorrect type of counseling")));
            return;
        }
    }
    
    
    private function _loadCareReport()
    {
        $id = $this->_getParam('id');
        $model = TrustCare_Model_ReportCare::find($id);
        if(is_null($model)) {
            $this->getLogger()->error(sprintf("'%s' tries to load unknown report_care.id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Report")));
            return;
        }

        $fileReportOutput = sprintf("%s/%s", TrustCare_SystemInterface_ReportGenerator_Abstract::reportsDirectory(), $model->getFilename());

        if(!file_exists($fileReportOutput)) {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Report file not found")));
            return;
        }

        $this->outputFileAsAttachment($fileReportOutput);
        return;
    }
    
    
    public function deleteActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:report", "delete");
    }
    
    public function deleteAction()
    {
        $type = $this->_getParam('type');
        if('care' == $type) {
            return $this->_deleteCareReport(); 
        }
        else if('community' == $type) {
            return $this->_deleteCommunityReport(); 
        }
        else {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Incorrect type of counseling")));
            return;
        }
    }
    
    private function _deleteCareReport()
    {
        $id = $this->_getParam('id');
        $reportModel = TrustCare_Model_ReportCare::find($id);
        if(is_null($reportModel)) {
            $this->getLogger()->error(sprintf("'%s' tries to delete unknown report_care.id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Report")));
            return;
        }
        $fileName = $reportModel->getFilename();
        
        $reportModel->delete();
        TrustCare_SystemInterface_ReportGenerator_Abstract::removeReportFile($fileName);
        
        $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName(), null, array('type' => $this->_getParam('type')));
    }
    
    private function _deleteCommunityReport()
    {
        $id = $this->_getParam('id');
        $reportModel = TrustCare_Model_ReportCommunity::find($id);
        if(is_null($reportModel)) {
            $this->getLogger()->error(sprintf("'%s' tries to delete unknown report_community.id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Report")));
            return;
        }
        
        $reportModel->delete();
        $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName(), null, array('type' => $this->_getParam('type')));
    }

    
    /**
     * @return Zend_Form
     */
    private function _getGenerateCareReportForm()
    {
        $pharmacyList = array();
        $pharmacyList[''] = '';
        $model = new TrustCare_Model_Pharmacy();
        foreach($model->fetchAll(array("is_active!=0"), 'name') as $obj) {
            $pharmacyList[$obj->getId()] = $obj->getName();
        }
        
        $periodList = array();
        for($i = 0; $i <= 11; $i++) {
            $time = gmmktime(0, 0, 0, gmdate("m") - $i, gmdate("d"), gmdate("Y"));
            $periodList[gmdate("Y-m", $time)] = gmdate("Y-m", $time);
        }
        
        $numberValidator = new Zend_Validate_Regex('/^\d+$/');
        $numberValidator->setMessage(Zend_Registry::get("Zend_Translate")->_("Necessary to enter positive value"));
        
        
        $form = new ZendX_Form();
        $form->setMethod('post');

        $form->addElement('hidden', 'id');
        
        $tabIndex = 1;
        $form->addElement('select', 'id_pharmacy', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Pharmacy"),
            'tabindex'      => $tabIndex++,
            'required'      => true,
            'multioptions'  => $pharmacyList,
        ));
        $form->addElement('select', 'period', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Period"),
            'tabindex'      => $tabIndex++,
            'required'      => true,
            'value'         => gmdate("Y-m", gmmktime(0, 0, 0, gmdate("m"), gmdate("d"), gmdate("Y"))),
            'multioptions'  => $periodList,
        ));
        $form->addElement('text', 'male_younger_15', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("The number of male (< 15 years) who received prescriptions"),
            'description'   => "",
            'tabindex'      => $tabIndex++,
            'size'			=> 5,
            'required'      => true,
            'validators'    => array($numberValidator)
        ));
        $form->addElement('text', 'female_younger_15', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("The number of female (< 15 years) who received prescriptions"),
            'description'   => "",
            'tabindex'      => $tabIndex++,
            'size'			=> 5,
        	'required'      => true,
            'validators'    => array($numberValidator)
        ));
        $form->addElement('text', 'male_from_15', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("The number of male (>= 15 years) who received prescriptions"),
            'description'   => "",
            'tabindex'      => $tabIndex++,
            'size'			=> 5,
        	'required'      => true,
            'validators'    => array($numberValidator)
        ));
        $form->addElement('text', 'female_from_15', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("The number of female (>= 15 years) who received prescriptions"),
            'description'   => "",
            'tabindex'      => $tabIndex++,
            'size'			=> 5,
        	'required'      => true,
            'validators'    => array($numberValidator)
        ));
        $form->addElement('text', 'drugs', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("The number of drugs dispensed in the reporting month"),
            'description'   => "",
            'tabindex'      => $tabIndex++,
            'size'			=> 5,
        	'required'      => true,
            'validators'    => array($numberValidator)
        ));
        
        
        $form->addElement('submit', 'send', array(
            'label'     => Zend_Registry::get("Zend_Translate")->_("Generate"),
            'tabindex'  => $tabIndex++,
        ));
        
        return $form;
    }
}

