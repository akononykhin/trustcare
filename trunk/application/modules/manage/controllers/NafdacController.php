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

        $form = $this->_getParametersForm();
        $form->setAction($this->getRequest()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . "/create");
        if($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
    
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
                try {
/*                    
                    $name = $form->getValue('name');
                    $iso_3166 = $form->getValue('iso_3166');
    
                    $checkModel = TrustCare_Model_Country::findByIso($iso_3166);
                    if(!is_null($checkModel)) {
                        $errorMsg = sprintf(Zend_Registry::get("Zend_Translate")->_("ISO code %s has already been used"), $iso_3166);
                        throw new Exception("");
                    }
    
                    $model = new TrustCare_Model_Country();
                    $model->setName($name);
                    $model->setIso3166($iso_3166);
                    $model->save();
    
                    $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName());
*/                    
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
        
        $patientObj = TrustCare_Model_Patient::find($frmObj->getIdPatient());
        if(is_null($patientObj)) {
            $this->getLogger()->error(sprintf("Failed to load patient for frm_card.id=%s", $idFrmCare));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Internal Error")));
            return;
        }
        
        $form->getSubForm("adr")->getElement('adr_start_date')->setValue($frmObj->getAdrStartDate());
        $form->getSubForm("adr")->getElement('adr_stop_date')->setValue($frmObj->getAdrStopDate());
        
        $this->view->id_frm_care = $idFrmCare;
        $this->view->patient = $patientObj;
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
    
    public function deleteActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:form", "delete");
    }
    
    public function deleteAction()
    {
        $type = $this->_getParam('type');
        if('care' == $type) {
            return $this->_deleteCareForm(); 
        }
        else if('community' == $type) {
            return $this->_deleteCommunityForm(); 
        }
        else {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Incorrect type of counseling")));
            return;
        }
    }
    
    private function _deleteCareForm()
    {
        $id = $this->_getParam('id');
        $formModel = TrustCare_Model_FrmCare::find($id);
        if(is_null($formModel)) {
            $this->getLogger()->error(sprintf("'%s' tries to edit unknown frm_card.id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Form")));
            return;
        }
        
        $formModel->delete();
        $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName(), null, array('type' => $this->_getParam('type')));
    }
    
    private function _deleteCommunityForm()
    {
        $id = $this->_getParam('id');
        $formModel = TrustCare_Model_FrmCommunity::find($id);
        if(is_null($formModel)) {
            $this->getLogger()->error(sprintf("'%s' tries to edit unknown frm_community.id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Form")));
            return;
        }
        
        $formModel->delete();
        $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName(), null, array('type' => $this->_getParam('type')));
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
        
        
        $form->addSubForms(array(
                'adr'  => $adrSubForm,
                'drug' => $drugSubForm,
        ));
        
        $form->addElement('submit', 'send', array(
                'label'     => Zend_Registry::get("Zend_Translate")->_("Generate"),
        ));
        
        return $form;
    }
    
}

