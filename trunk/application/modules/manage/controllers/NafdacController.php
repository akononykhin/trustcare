<?php

class NafdacController extends ZendX_Controller_Action
{

    public function init()
    {
        parent::init();
    }


    public function indexAction()
    {
        $this->getRedirector()->gotoSimpleAndExit("create", $this->getRequest()->getControllerName());
    }
    
    
    public function createActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:form", "create");
    }
    
    
    public function createAction()
    {
        $idFrmCare = $this->_getParam('id_frm_care');
        $frmObj = TrustCare_Model_FrmCare::find($idFrmCare);
        if(is_null($idFrmCare)) {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Necessary to choose Pharmaceutical Care form for generating NAFDAC.")));
            return;
        }
        
        $nafdacObj = TrustCare_Model_Nafdac::findByIdFrmCare($idFrmCare);
        if(!is_null($nafdacObj)) {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("NAFDAC form has already been generated for this Pharmaceutical Care form.")));
            return;
        }

        $form = $this->_getParametersForm();
        $form->setAction($this->getRequest()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . "/create");
        if($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
    
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
                $db_options = Zend_Registry::get('dbOptions');
                $db = Zend_Db::factory($db_options['adapter'], $db_options['params']);
                $db->beginTransaction();
                try {
                    $nafdacModel = new TrustCare_Model_Nafdac(
                            array(
                                    'id_frm_care' => $idFrmCare,
                                    'adr_description' => $form->getSubForm("adr")->getValue('adr_description'),
                                    'was_admitted' => $form->getSubForm("adr")->getValue('was_admitted'),
                                    'was_hospitalization_prolonged' => $form->getSubForm("adr")->getValue('was_hospitalization_prolonged'),
                                    'duration_of_admission' => $form->getSubForm("adr")->getValue('duration_of_admission'),
                                    'treatment_of_reaction' => $form->getSubForm("adr")->getValue('treatment_of_reaction'),
                                    'outcome_of_reaction_type' => $form->getSubForm("adr")->getValue('outcome_of_reaction_type'),
                                    'outcome_of_reaction_desc' => $form->getSubForm("adr")->getValue('outcome_of_reaction_desc'),
                                    'drug_brand_name' => $form->getSubForm("drug")->getValue('drug_brand_name'),
                                    'drug_generic_name' => $form->getSubForm("drug")->getValue('drug_generic_name'),
                                    'drug_batch_number' => $form->getSubForm("drug")->getValue('drug_batch_number'),
                                    'drug_nafdac_number' => $form->getSubForm("drug")->getValue('drug_nafdac_number'),
                                    'drug_expiry_name' => $form->getSubForm("drug")->getValue('drug_expiry_name'),
                                    'drug_manufactor' => $form->getSubForm("drug")->getValue('drug_manufactor'),
                                    'drug_indication_for_use' => $form->getSubForm("drug")->getValue('drug_indication_for_use'),
                                    'drug_dosage' => $form->getSubForm("drug")->getValue('drug_dosage'),
                                    'drug_route_of_administration' => $form->getSubForm("drug")->getValue('drug_route_of_administration'),
                                    'drug_date_started' => $form->getSubForm("drug")->getValue('drug_date_started'),
                                    'drug_date_stopped' => $form->getSubForm("drug")->getValue('drug_date_stopped'),
                                    'reporter_name' => $form->getSubForm("reporter")->getValue('reporter_name'),
                                    'reporter_address' => $form->getSubForm("reporter")->getValue('reporter_address'),
                                    'reporter_profession' => $form->getSubForm("reporter")->getValue('reporter_profession'),
                                    'reporter_contact' => $form->getSubForm("reporter")->getValue('reporter_contact'),
                                    'mapperOptions' => array('adapter' => $db)
                            )
                    );
                    $nafdacModel->save();

                    $medicineName = $_REQUEST['medicine_name'];
                    $medicineDosage = $_REQUEST['medicine_dosage'];
                    $medicineRoute = $_REQUEST['medicine_route'];
                    $medicineStarted = $_REQUEST['medicine_started'];
                    $medicineStopped = $_REQUEST['medicine_stopped'];
                    $medicineReason = $_REQUEST['medicine_reason'];
                    
                    foreach($medicineName as $index=>$value) {
                        if(empty($value)) {
                            continue;
                        }
                        $medModel = new TrustCare_Model_NafdacMedicine(
                                array(
                                        'id_nafdac' => $nafdacModel->getId(),
                                        'name' => $value,
                                        'dosage' => $medicineDosage[$index],
                                        'route'   => $medicineRoute[$index],
                                        'started' => $medicineStarted[$index],
                                        'stopped' => $medicineStopped[$index],
                                        'reason' => $medicineReason[$index],
                                        'mapperOptions' => array('adapter' => $db)
                                )
                        );
                        $medModel->save();
                        
                    }

                    $generator = TrustCare_SystemInterface_ReportGenerator_Abstract::factory(TrustCare_SystemInterface_ReportGenerator_Abstract::CODE_NAFDAC);
                    
                    $fileName = $obj = $generator->generate(array(
                                                    'id_frm_care' => $idFrmCare,
                    ));
                    
                    $nafdacModel->setFilename($filename);
                    $nafdacModel->save();
                    
                    $fileReportOutput = sprintf("%s/%s", $generator->reportsDirectory(), $fileName);
                    if(!file_exists($fileReportOutput)) {
                        $errorMsg = Zend_Registry::get("Zend_Translate")->_("Report file not found");
                        throw new Exception(sprintf("Report file '%s' not found", $fileReportOutput));
                    }
                    
                    $this->outputFileAsAttachment($fileReportOutput);
                    return;
                    
                    $db->commit();
                }
                catch(Exception $ex) {
                    $db->rollback();
                    $message = $ex->getMessage();
                    if(!empty($message)) {
                        $this->getLogger()->error($message);
                    }
                }
                $form->addError($errorMsg);
            }
        }
        
        $patientObj = TrustCare_Model_Patient::find($frmObj->getIdPatient());
        if(is_null($patientObj)) {
            $this->getLogger()->error(sprintf("Failed to load patient for frm_card.id=%s", $idFrmCare));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Internal Error")));
            return;
        }
        $pharmObj = TrustCare_Model_Pharmacy::find($frmObj->getIdPharmacy());
        if(is_null($pharmObj)) {
            $this->getLogger()->error(sprintf("Failed to load pharmacy for frm_card.id=%s", $idFrmCare));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Internal Error")));
            return;
        }
        $facilityObj = TrustCare_Model_Facility::find($pharmObj->getIdFacility());
        if(is_null($facilityObj)) {
            $this->getLogger()->error(sprintf("Failed to load facility for pharmacy.id=%s(frm_card.id=%s)", $pharmObj->getId(), $idFrmCare));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Internal Error")));
            return;
        }
        
        $form->getSubForm("adr")->getElement('adr_start_date')->setValue($frmObj->getAdrStartDate());
        $form->getSubForm("adr")->getElement('adr_stop_date')->setValue($frmObj->getAdrStopDate());
        
        $this->view->id_frm_care = $idFrmCare;
        $this->view->patient = $patientObj;
        $this->view->hospital_name = $facilityObj->getName();
        $this->view->form = $form;
        
        $this->render('create');
        return;
    }
    
    
    
    public function viewActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:form", "view");
    }
    
    public function viewAction()
    {
        $type = $this->_getParam('type');
        if('care' == $type) {
            return $this->_viewCareForm();; 
        }
        else if('community' == $type) {
            return $this->_viewCommunityForm();; 
        }
        else {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Incorrect type of counseling")));
            return;
        }
    }
    
    private function _viewCareForm()
    {
        $id = $this->_getParam('id');
        $formModel = TrustCare_Model_FrmCare::find($id);
        if(is_null($formModel)) {
            $this->getLogger()->error(sprintf("'%s' tries to edit unknown frm_card.id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
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
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_card.id=%s", $obj->getIdPharmacyDictionary(), $id));
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
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_card.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $medAdhProblems[] = $dict->getName();
            }
        }

        $adhInterventions = array();
        $model = new TrustCare_Model_FrmCareAdhIntervention();
        foreach($model->fetchAllForFrmCare($formModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_card.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $adhInterventions[] = $dict->getName();
            }
        }

        $adhInterventionOutcomes = array();
        $model = new TrustCare_Model_FrmCareAdhInterventionOutcome();
        foreach($model->fetchAllForFrmCare($formModel->getId()) as $obj) {
            $dict = TrustCare_Model_PharmacyDictionary::find($obj->getIdPharmacyDictionary());
            if(is_null($dict)) {
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_card.id=%s", $obj->getIdPharmacyDictionary(), $id));
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
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_card.id=%s", $obj->getIdPharmacyDictionary(), $id));
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
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_card.id=%s", $obj->getIdPharmacyDictionary(), $id));
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
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_card.id=%s", $obj->getIdPharmacyDictionary(), $id));
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
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_card.id=%s", $obj->getIdPharmacyDictionary(), $id));
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
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_card.id=%s", $obj->getIdPharmacyDictionary(), $id));
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
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_card.id=%s", $obj->getIdPharmacyDictionary(), $id));
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
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_card.id=%s", $obj->getIdPharmacyDictionary(), $id));
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
                $this->getLogger()->error(sprintf("Failed to load pharmacy_dictionary.id=%s for frm_card.id=%s", $obj->getIdPharmacyDictionary(), $id));
            }
            else {
                $adrInterventions[] = $dict->getName();
            }
        }
        
        $this->view->formModel = $formModel;
        $this->view->patientModel = $patientModel;
        $this->view->pharmacyName = $pharmacyModel->getName();
        $this->view->medErrorTypes = $medErrorTypes;
        $this->view->medAdhProblems = $medAdhProblems;
        $this->view->adhInterventions = $adhInterventions;
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
        
        $this->render('view-care');
        return;
    }
    
    private function _viewCommunityForm()
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
        
        $this->render('view-community');
        return;
    }
    
    
    
    /**
     * @return Zend_Form
     */
    private function _getParametersForm()
    {
        $form = new ZendX_Form();
        $form->setMethod('post');
    
        $tabIndex = 1;
        
        
        $adrSubForm = new ZendX_Form_SubForm();
        $adrSubForm->setLegend(Zend_Registry::get("Zend_Translate")->_("Adverse Drug Reaction (ADR)"));
        
        $adrSubForm->addElement('textarea', 'adr_description', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Description"),
                'description'   => "",
                'cols'       => 60,
                'rows'       => 3,
                'tabindex'      => $tabIndex++,
                'required'      => true
        ));
        $adrSubForm->addElement('htmltext', 'adr_start_date', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("DATE Reaction Started"),
                'description'   => "",
        ));
        $adrSubForm->addElement('htmltext', 'adr_stop_date', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("DATE Reaction Stopped"),
                'description'   => "",
        ));
        $adrSubForm->addElement('select', 'outcome_of_reaction_type', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Outcome of Reaction"),
                'required'      => true,
                'tabindex'      => $tabIndex++,
                'multioptions'  => TrustCare_Model_Nafdac::getOutcomeReactionTypes(),
        ));
        $adrSubForm->addElement('text', 'outcome_of_reaction_desc', array(
                'label'         => '',
                'size'          => 64,
                'tabindex'      => $tabIndex++,
                'required'      => false
        ));
        $adrSubForm->addElement('checkbox', 'was_admitted', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Was Patient Admited Due to ADR?"),
                'tabindex'      => $tabIndex++,
                'checked'      => false
        ));
        $adrSubForm->addElement('checkbox', 'was_hospitalization_prolonged', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("If Already Hospitalized, Was it Prolonged Due to ADR?"),
                'tabindex'      => $tabIndex++,
                'checked'      => false
        ));
        $adrSubForm->addElement('text', 'duration_of_admission', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Duration of Admission (days)"),
                'size'          => 8,
                'tabindex'      => $tabIndex++,
                'required'      => false
        ));
        $adrSubForm->addElement('text', 'treatment_of_reaction', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Treatment of Reaction"),
                'size'          => 64,
                'tabindex'      => $tabIndex++,
                'required'      => false
        ));
        
        
        $drugSubForm = new ZendX_Form_SubForm();
        $drugSubForm->setLegend(Zend_Registry::get("Zend_Translate")->_("Suspected Drug"));
        $drugSubForm->addElement('text', 'drug_brand_name', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Brand Name"),
                'size'          => 32,
                'tabindex'      => $tabIndex++,
                'required'      => true
        ));
        $drugSubForm->addElement('text', 'drug_generic_name', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Generic Name"),
                'size'          => 32,
                'tabindex'      => $tabIndex++,
                'required'      => true
        ));
        $drugSubForm->addElement('text', 'drug_batch_number', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Batch No."),
                'size'          => 32,
                'tabindex'      => $tabIndex++,
                'required'      => true
        ));
        $drugSubForm->addElement('text', 'drug_nafdac_number', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("NAFDAC No."),
                'size'          => 32,
                'tabindex'      => $tabIndex++,
                'required'      => true
        ));
        $drugSubForm->addElement('text', 'drug_expiry_name', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Expiry Date"),
                'size'          => 32,
                'tabindex'      => $tabIndex++,
                'required'      => true
        ));
        $drugSubForm->addElement('text', 'drug_manufactor', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Name & Address of Manufacturer"),
                'size'          => 96,
                'tabindex'      => $tabIndex++,
                'required'      => true
        ));
        $drugSubForm->addElement('text', 'drug_indication_for_use', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Indication for Use"),
                'size'          => 32,
                'tabindex'      => $tabIndex++,
                'required'      => true
        ));
        $drugSubForm->addElement('text', 'drug_dosage', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Dosage"),
                'size'          => 32,
                'tabindex'      => $tabIndex++,
                'required'      => true
        ));
        $drugSubForm->addElement('text', 'drug_route_of_administration', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Route of Administration"),
                'size'          => 32,
                'tabindex'      => $tabIndex++,
                'required'      => true
        ));
        $drugSubForm->addElement('text', 'drug_date_started', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Date Started"),
                'size'          => 32,
                'tabindex'      => $tabIndex++,
                'required'      => true
        ));
        $drugSubForm->addElement('text', 'drug_date_stopped', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Date Stopped"),
                'size'          => 32,
                'tabindex'      => $tabIndex++,
                'required'      => true
        ));
        
        $reporterSubForm = new ZendX_Form_SubForm();
        $reporterSubForm->setLegend(Zend_Registry::get("Zend_Translate")->_("Source of Report"));
        $reporterSubForm->addElement('text', 'reporter_name', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Name of Reporter"),
                'size'          => 32,
                'tabindex'      => $tabIndex++,
                'required'      => true
        ));
        $reporterSubForm->addElement('text', 'reporter_address', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Address"),
                'size'          => 64,
                'tabindex'      => $tabIndex++,
                'required'      => true
        ));
        $reporterSubForm->addElement('text', 'reporter_profession', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Profession"),
                'size'          => 32,
                'tabindex'      => $tabIndex++,
                'required'      => true
        ));
        $reporterSubForm->addElement('text', 'reporter_contact', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Tel No/Email"),
                'size'          => 32,
                'tabindex'      => $tabIndex++,
                'required'      => true
        ));
        
        
        $form->addSubForms(array(
                'adr'  => $adrSubForm,
                'drug' => $drugSubForm,
                'reporter' => $reporterSubForm,
        ));
        
        $form->addElement('submit', 'send', array(
                'label'     => Zend_Registry::get("Zend_Translate")->_("Generate"),
        ));

        
        return $form;
    }
    
}

