<?php

class Form_CommunityController extends ZendX_Controller_Action
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
                'width' => '20%',
            ),
            'patient_first_name' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("First Name"),
                'width' => '20%',
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
        $select = Zend_Registry::getInstance()->dbAdapter->select()->from('frm_community', array('count(id)'));
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_NUM);
        $result = Zend_Registry::getInstance()->dbAdapter->fetchAll($select);
        $iTotal = $result[0][0];


        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_ASSOC);
        $select = Zend_Registry::getInstance()->dbAdapter->select()
                                                         ->from('frm_community',
                                                            array(
                                                                'frm_community.id',
                                                                'date_of_visit' => new Zend_Db_Expr("date_format(date_of_visit, '%Y-%m-%d')"),
                                                                ))
                                                         ->joinLeft(array('patient'), 'frm_community.id_patient = patient.id', array('patient_identifier' => 'patient.identifier',
                                                                                                                                     'patient_first_name' => 'patient.first_name',
                                                                                                                                     'patient_last_name' => 'patient.last_name'))
                                                         ->joinLeft(array('pharmacy'), 'frm_community.id_pharmacy = pharmacy.id', array('pharmacy_name' => 'pharmacy.name'));
                                                             
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
            //$row['date_of_visit'] = $this->convertDateToUserTimezone($row['date_of_visit']); /* We don't convert date before saving so it's not necessary to convert it back */
            
            $row['_row_actions_'] = array(
                array(
                    'title' => Zend_Registry::get("Zend_Translate")->_("View"),
                    'url' => $this->view->url(array('action' => 'view', 'id' => $row['id'])),
                    'type' => 'view'
                ),
                array(
                    'title' => Zend_Registry::get("Zend_Translate")->_("Edit"),
                    'url' => $this->view->url(array('action' => 'edit', 'id' => $row['id'])),
                    'type' => 'edit'
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
                
                $frmModel = new TrustCare_Model_FrmCommunity(
                    array(
                        'id_pharmacy' => $idPharmacy,
                		'id_patient' => $idPatient,
                		'date_of_visit' => $dateOfVisit,
                		'is_first_visit_to_pharmacy' => TrustCare_Model_FrmCommunity::isFirstVisitOfPatientToPharmacy($idPatient, $idPharmacy),
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
                    $model = new TrustCare_Model_FrmCommunityReferredIn(
                        array(
          					'id_frm_community' => $frmModel->getId(),
           					'id_pharmacy_dictionary' => $dictId,
                           	'mapperOptions' => array('adapter' => $db)
                        )
                    );
                    $model->save();
                }
                
                foreach($referredOutList  as $dictId) {
                    $model = new TrustCare_Model_FrmCommunityReferredOut(
                        array(
          					'id_frm_community' => $frmModel->getId(),
           					'id_pharmacy_dictionary' => $dictId,
                           	'mapperOptions' => array('adapter' => $db)
                        )
                    );
                    $model->save();
                }
                
                foreach($palliativeCareTypeList  as $dictId) {
                    $model = new TrustCare_Model_FrmCommunityPalliativeCareType(
                        array(
          					'id_frm_community' => $frmModel->getId(),
           					'id_pharmacy_dictionary' => $dictId,
                           	'mapperOptions' => array('adapter' => $db)
                        )
                    );
                    $model->save();
                }
                
                foreach($stiTypeList  as $dictId) {
                    $model = new TrustCare_Model_FrmCommunityStiType(
                        array(
          					'id_frm_community' => $frmModel->getId(),
           					'id_pharmacy_dictionary' => $dictId,
                           	'mapperOptions' => array('adapter' => $db)
                        )
                    );
                    $model->save();
                }
                
                foreach($reproductiveHealthTypeList  as $dictId) {
                    $model = new TrustCare_Model_FrmCommunityReproductiveHealthType(
                        array(
          					'id_frm_community' => $frmModel->getId(),
           					'id_pharmacy_dictionary' => $dictId,
                           	'mapperOptions' => array('adapter' => $db)
                        )
                    );
                    $model->save();
                }
                
                foreach($tuberculosisTypeList  as $dictId) {
                    $model = new TrustCare_Model_FrmCommunityTuberculosisType(
                        array(
          					'id_frm_community' => $frmModel->getId(),
           					'id_pharmacy_dictionary' => $dictId,
                           	'mapperOptions' => array('adapter' => $db)
                        )
                    );
                    $model->save();
                }
                
                foreach($ovcTypeList  as $dictId) {
                    $model = new TrustCare_Model_FrmCommunityOvcType(
                        array(
          					'id_frm_community' => $frmModel->getId(),
           					'id_pharmacy_dictionary' => $dictId,
                           	'mapperOptions' => array('adapter' => $db)
                        )
                    );
                    $model->save();
                }
                
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
            
        $this->view->allow_create_patient = Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.patient", "create");
        $this->view->allow_create_pharmacy = Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.pharmacy", "create");
                
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
        $formModel = TrustCare_Model_FrmCommunity::find($id);
        if(is_null($formModel)) {
            $this->getLogger()->error(sprintf("'%s' tries to edit unknown frm_community.id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Form")));
            return;
        }
        
        $patientModel = TrustCare_Model_Patient::find($formModel->getIdPatient());
        if(is_null($patientModel)) {
            $this->getLogger()->error(sprintf("Failed to load patient.id=%s specified for frm_community.id=%s", $formModel->getIdPatient(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Internal Error")));
            return;
        }

        
        $pharmacyModel = TrustCare_Model_Pharmacy::find($formModel->getIdPharmacy());
        if(is_null($pharmacyModel)) {
            $this->getLogger()->error(sprintf("Failed to load pharmacy.id=%s specified for frm_community.id=%s", $formModel->getIdPharmacy(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Internal Error")));
            return;
        }
        
        $referredInList = array();
        $model = new TrustCare_Model_FrmCommunityReferredIn();
        foreach($model->fetchAllForFrmCommunity($formModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_community.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $referredInList[] = $dict->getName();
            }
        }
        
        $referredOutList = array();
        $model = new TrustCare_Model_FrmCommunityReferredOut();
        foreach($model->fetchAllForFrmCommunity($formModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_community.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $referredOutList[] = $dict->getName();
            }
        }
        
        $htcResultName = '';
        $dict = TrustCare_Model_PharmacyDictionary::find($formModel->getHtcResultId());
        if(!is_null($dict)) {
            $htcResultName = $dict->getName();
        }
        
        $palliativeCareTypeList = array();
        $model = new TrustCare_Model_FrmCommunityPalliativeCareType();
        foreach($model->fetchAllForFrmCommunity($formModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_community.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $palliativeCareTypeList[] = $dict->getName();
            }
        }
        
        $stiTypeList = array();
        $model = new TrustCare_Model_FrmCommunityStiType();
        foreach($model->fetchAllForFrmCommunity($formModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_community.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $stiTypeList[] = $dict->getName();
            }
        }
        
        $reproductiveHealthTypeList = array();
        $model = new TrustCare_Model_FrmCommunityReproductiveHealthType();
        foreach($model->fetchAllForFrmCommunity($formModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_community.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $reproductiveHealthTypeList[] = $dict->getName();
            }
        }
        
        $tuberculosisTypeList = array();
        $model = new TrustCare_Model_FrmCommunityTuberculosisType();
        foreach($model->fetchAllForFrmCommunity($formModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_community.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $tuberculosisTypeList[] = $dict->getName();
            }
        }
        
        $ovcTypeList = array();
        $model = new TrustCare_Model_FrmCommunityOvcType();
        foreach($model->fetchAllForFrmCommunity($formModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_community.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $ovcTypeList[] = $dict->getName();
            }
        }
        
        $this->view->formModel = $formModel;
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
        
        $this->render('view');
        return;
    }
    
    public function deleteActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:form", "delete");
    }
    
    public function deleteAction()
    {
        $id = $this->_getParam('id');
        $formModel = TrustCare_Model_FrmCommunity::find($id);
        if(is_null($formModel)) {
            $this->getLogger()->error(sprintf("'%s' tries to edit unknown frm_community.id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Form")));
            return;
        }
        
        $formModel->delete();
        $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName());
    }
}

