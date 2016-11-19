<?php

class Portal_NafdacController extends ZendX_Controller_Action
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
            'patient_identifier' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Patient ID"),
                'filter' => array(
                    'type' => 'text',
                ),
                'width' => '35%',
            ),
            'pharmacy_name' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Pharmacy"),
                'filter' => array(
                    'type' => 'text',
                ),
                'width' => '35%',
            ),
        );
    
        $this->view->DataTable = array(
            'serverUrl' => $this->view->url(array('action' => 'list-load')),
            'toolbar' => array(
                array(
                    'text' => Zend_Registry::get("Zend_Translate")->_("Generate"),
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
        $select = Zend_Registry::getInstance()->dbAdapter->select()->from('nafdac', array('count(id)'));
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_NUM);
        $result = Zend_Registry::getInstance()->dbAdapter->fetchAll($select);
        $iTotal = $result[0][0];
    
    
        $pharmacyIds = array_keys(Zend_Registry::get("TrustCare_Registry_User")->getListOfAvailablePharmacies());
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_ASSOC);
        $select = Zend_Registry::getInstance()->dbAdapter->select()
                                                         ->from('nafdac',
                                                             array(
                                                                 'nafdac.id',
                                                                 'date_of_visit' => new Zend_Db_Expr("date_format(nafdac.date_of_visit, '%Y-%m-%d')"),
                                                                 'generation_date' => new Zend_Db_Expr("date_format(nafdac.generation_date, '%Y-%m-%d %H:%i:%s')")
                                                             ))
                                                         ->joinLeft(array('patient'), 'nafdac.id_patient = patient.id', array('patient_identifier' => 'patient.identifier'))
                                                         ->joinLeft(array('pharmacy'), 'nafdac.id_pharmacy = pharmacy.id', array('pharmacy_name' => 'pharmacy.name'))
                                                         ->where(sprintf("nafdac.id_pharmacy in (%s)", join(",", $pharmacyIds)));
        
        $this->processListLoadAjaxRequest($select, array('pharmacy_name' => 'pharmacy.name',
                                                         'patient_identifier' => 'patient.identifier'));
    
        $rows = Zend_Registry::getInstance()->dbAdapter->selectWithLimit($select->__toString(), $iFilteredTotal);
    
    
        $output = array(
            "sEcho" => intval($_REQUEST['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );
        foreach ($rows as $row) {
            $row['DT_RowId'] = $row['id'];
            $row['generation_date'] = $this->convertTimeToUserTimezone($row['generation_date'], Zend_Registry::getInstance()->dateFormat); 
            $row['date_of_visit'] = $this->showDateAtSpecifiedFormat($row['date_of_visit']);
            
            $row['_row_actions_'] = array(
                array(
                    'title' => Zend_Registry::get("Zend_Translate")->_("View"),
                    'url' => $this->view->url(array('action' => 'view', 'id' => $row['id'])),
                    'type' => 'view'
                ),
                array(
                    'title' => Zend_Registry::get("Zend_Translate")->_("Download"),
                    'url' => $this->view->url(array('action' => 'download', 'id' => $row['id'])),
                    'type' => 'download'
                ),
                array(
                    'title' => Zend_Registry::get("Zend_Translate")->_("Delete"),
                    'url' => $this->view->url(array('action' => 'delete', 'id' => $row['id'])),
                    'type' => 'delete',
                    'askConfirm' => sprintf(Zend_Registry::get("Zend_Translate")->_("Are you sure you want to delete report %s generated %s?"), $row['id'], $row['date_of_visit']),
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
        $idFrmCare = $this->_getParam('id_frm_care');
        $idFrmCommunity = $this->_getParam('id_frm_community');
        $patientId = $this->_getParam('id_patient');
        
        $form = $this->_getParametersForm();
        $form->setAction($this->getRequest()->getBaseUrl() . '/' . $this->getRequest()->getModuleName() . '/' . $this->getRequest()->getControllerName() . "/create");
        if($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
                
                $db_options = Zend_Registry::get('dbOptions');
                $db = Zend_Db::factory($db_options['adapter'], $db_options['params']);
                $db->beginTransaction();
                $transactionStarted = true;
                try {
                    $idPharmacy = $form->getSubForm("patient")->getValue('id_pharmacy');
                    if(!array_key_exists($idPharmacy, Zend_Registry::get("TrustCare_Registry_User")->getListOfAvailablePharmacies())) {
                        $errorMsg = Zend_Registry::get("Zend_Translate")->_("Access Denied");
                        throw new Exception('');
                    }
                    
                    
                    $nafdacModel = new TrustCare_Model_Nafdac(
                            array(
                                    'id_user' => Zend_Registry::get("TrustCare_Registry_User")->getUser()->getId(),
                                    'id_patient' => $patientId,
                                    'generation_date' => ZendX_Db_Table_Abstract::LABEL_NOW,
                                    'id_pharmacy' => $idPharmacy,
                                    'date_of_visit' => $form->getSubForm("patient")->getValue('date_of_visit'),
                                    'adr_start_date' => $form->getSubForm("adr")->getValue('adr_start_date'),
                                    'adr_stop_date' => $form->getSubForm("adr")->getValue('adr_stop_date'),
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
                    
                    $frmCare = TrustCare_Model_FrmCare::find($idFrmCare, array('mapperOptions' => array('adapter' => $db)));
                    $frmCommunity = TrustCare_Model_FrmCommunity::find($idFrmCommunity, array('mapperOptions' => array('adapter' => $db)));
                    
                    if(!is_null($frmCare)) {
                        $frmCare->setIdNafdac($nafdacModel->getId());
                        $frmCare->save();
                    }
                    else if(!is_null($frmCommunity)) {
                        $frmCommunity->setIdNafdac($nafdacModel->getId());
                        $frmCommunity->save();                        
                    }
                    
                    $db->commit();
                    
                    $transactionStarted = false;
                    
                    $generator = TrustCare_SystemInterface_ReportGenerator_Abstract::factory(TrustCare_SystemInterface_ReportGenerator_Abstract::CODE_NAFDAC);
                    
                    $fileName = $generator->generate(array('id' => $nafdacModel->getId()));
                    
                    $nafdacModel->setFilename($fileName);
                    $nafdacModel->save();
                    
                    $fileReportOutput = sprintf("%s/%s", $generator->reportsDirectory(), $fileName);
                    if(!file_exists($fileReportOutput)) {
                        $errorMsg = Zend_Registry::get("Zend_Translate")->_("Report file not found");
                        throw new Exception(sprintf("Report file '%s' not found", $fileReportOutput));
                    }
                    
                    $this->outputFileAsAttachment($fileReportOutput);
                    return;
                    
                }
                catch(Exception $ex) {
                    if($transactionStarted) {
                        $db->rollback();
                    }
                    $message = $ex->getMessage();
                    if(!empty($message)) {
                        $this->getLogger()->error($message);
                    }
                }
                $form->addError($errorMsg);
            }
        }
        else {
            $frmCare = TrustCare_Model_FrmCare::find($idFrmCare);
            $frmCommunity = TrustCare_Model_FrmCommunity::find($idFrmCommunity);
            if(!is_null($frmCare)) {
                $patientObj = TrustCare_Model_Patient::find($frmCare->getIdPatient());
                if(!is_null($patientObj)) {
                    $patientName = $patientObj->showNameAs();
                    $patientId = $patientObj->getId();
                }
                $pharmObj = TrustCare_Model_Pharmacy::find($frmCare->getIdPharmacy());
                if(!is_null($pharmObj)) {
                    $pharmacyId = $pharmObj->getId();
                }
                $dateOfVisit = $frmCare->getDateOfVisit();
                
                $adrStartDate = $frmCare->getAdrStartDate();
                $adrStopDate = $frmCare->getAdrStopDate();
            }
            else if(!is_null($frmCommunity)) {
                $patientObj = TrustCare_Model_Patient::find($frmCommunity->getIdPatient());
                if(!is_null($patientObj)) {
                    $patientName = $patientObj->showNameAs();
                    $patientId = $patientObj->getId();
                }
                $pharmObj = TrustCare_Model_Pharmacy::find($frmCommunity->getIdPharmacy());
                if(!is_null($pharmObj)) {
                    $pharmacyId = $pharmObj->getId();
                }
                $dateOfVisit = $frmCommunity->getDateOfVisit();
                
                $adrStartDate = $frmCommunity->getAdrStartDate();
                $adrStopDate = $frmCommunity->getAdrStopDate();
            }
            
            $form->getSubForm("patient")->getElement('patient_name')->setValue($patientName);
            $form->getSubForm("patient")->getElement('id_pharmacy')->setValue($pharmacyId);
            $form->getSubForm("patient")->getElement('date_of_visit')->setValue($dateOfVisit);
            
            $form->getSubForm("adr")->getElement('adr_start_date')->setValue($adrStartDate);
            $form->getSubForm("adr")->getElement('adr_stop_date')->setValue($adrStopDate);
            
            $user = Zend_Registry::get("TrustCare_Registry_User")->getUser();
            $form->getSubForm("reporter")->getElement('reporter_name')->setValue(sprintf("%s %s", $user->getLastName(), $user->getFirstName()));
            $form->getSubForm("reporter")->getElement('reporter_contact')->setValue($user->getPhone());
        }
        
        $this->view->form = $form;
        $this->view->id_frm_care = $idFrmCare;
        $this->view->id_frm_community = $idFrmCommunity;
        $this->view->id_patient = $patientId;
        
        $this->render('create');
        return;
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
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown NAFDAC")));
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

    
    public function viewActionAccess()
    {
        return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:form", "view");
    }
    
    
    public function viewAction()
    {
        $id = $this->_getParam('id');
    
        $model = TrustCare_Model_Nafdac::find($id);
        if(is_null($model)) {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown NAFDAC")));
            return;
        }
        
        $availablePharmacies = Zend_Registry::get("TrustCare_Registry_User")->getListOfAvailablePharmacies();
        if(!array_key_exists($model->getIdPharmacy(), $availablePharmacies)) {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Access Denied")));
            return;
        }
        
        $patientModel = TrustCare_Model_Patient::find($model->getIdPatient());
        if(is_null($patientModel)) {
            $this->getLogger()->error(sprintf("Failed to load patient.id=%s specified for nafdac.id=%s", $model->getIdPatient(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Internal Error")));
            return;
        }
        
        
        $pharmacyModel = TrustCare_Model_Pharmacy::find($model->getIdPharmacy());
        if(is_null($pharmacyModel)) {
            $this->getLogger()->error(sprintf("Failed to load pharmacy.id=%s specified for nafdac.id=%s", $model->getIdPharmacy(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Internal Error")));
            return;
        }
        
        
        $medicines = array();
        $model1 = new TrustCare_Model_NafdacMedicine();
        foreach($model1->fetchAllByIdNafdac($model->getId()) as $obj) {
            $medicines[] = $obj;
        }
        
        $reactionOutcomes = TrustCare_Model_Nafdac::getOutcomeReactionTypes();
        $outcomeOfReaction = array_key_exists($model->getOutcomeOfReactionType(), $reactionOutcomes) ? $reactionOutcomes[$model->getOutcomeOfReactionType()] : '';
        
        $this->view->model = $model;
        $this->view->patientModel = $patientModel;
        $this->view->pharmacyName = $pharmacyModel->getName();
        $this->view->medicines = $medicines;
        $this->view->outcomeOfReaction = $outcomeOfReaction;
        
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
    
        $model = TrustCare_Model_Nafdac::find($id);
        if(is_null($model)) {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown NAFDAC")));
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
        if(file_exists($fileReportOutput) && is_file($fileReportOutput)) {
            unlink($fileReportOutput);
        }
        $model->delete();
    
        $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName());
    }

    
    
    public function regenerateActionAccess()
    {
        return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:form", "edit");
    }
    
    
    public function regenerateAction()
    {
        $id = $this->_getParam('id');
    
        $model = TrustCare_Model_Nafdac::find($id);
        if(is_null($model)) {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown NAFDAC")));
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
        if(file_exists($fileReportOutput) && is_file($fileReportOutput)) {
            unlink($fileReportOutput);
        }

        $fileName = $generator->generate(array('id' => $model->getId()));
        
        $model->setFilename($fileName);
        $model->save();
        
        $fileReportOutput = sprintf("%s/%s", $generator->reportsDirectory(), $fileName);
        if(!file_exists($fileReportOutput)) {
            $errorMsg = Zend_Registry::get("Zend_Translate")->_("Report file not found");
            throw new Exception(sprintf("Report file '%s' not found", $fileReportOutput));
        }
        
        $this->outputFileAsAttachment($fileReportOutput);
        return;
    }
    
    
    public function _substrFilter($value, $start = 0, $length = null)
    {
        if(empty($value)) {
            return $value;
        }
        return substr($value, $start, $length);
    }
    
    
    /**
     * @return Zend_Form
     */
    private function _getParametersForm()
    {
        $pharmacyList = array('' => '') + Zend_Registry::get("TrustCare_Registry_User")->getListOfAvailablePharmacies();
        
        $dateValidator = new Zend_Validate_Date('yyyy-MM-dd');
        $dateValidator->setMessage(Zend_Registry::get("Zend_Translate")->_("Incorrect date"));
        
        
        $form = new ZendX_Form();
        $form->setMethod('post');
        
        $tabIndex = 1;
        
        $patientSubForm = new ZendX_Form_SubForm();
        $patientSubForm->setLegend(Zend_Registry::get("Zend_Translate")->_("Patient's Details"));
        
        $patientSubForm->addElement('text', 'patient_name', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Patient"),
            'id'            => 'patient_name',
            'tabindex'      => $tabIndex++,
            'required'      => true,
            'size'          => 32,
            'description'   => '',
        ));
        $patientSubForm->addElement('select', 'id_pharmacy', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Pharmacy"),
            'tabindex'      => $tabIndex++,
            'required'      => true,
            'multioptions'  => $pharmacyList,
        ));
        $patientSubForm->addElement('text', 'date_of_visit', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Date of Visit"),
            'id'            => 'date_of_visit',
            'description'   => "",
            'required'      => true,
            'validators'    => array($dateValidator),
            'size'          => 10,
            'tabindex'      => $tabIndex++,
        ));
        
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
        $adrSubForm->addElement('text', 'adr_start_date', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("DATE Reaction Started"),
            'id'            => 'adr_start_date',
            'description'   => "",
            'required'      => false,
            'validators'    => array($dateValidator),
            'size'          => 10,
            'tabindex'      => $tabIndex++,
        ));
        $adrSubForm->addElement('text', 'adr_stop_date', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("DATE Reaction Stopped"),
            'id'            => 'adr_stop_date',
            'description'   => "",
            'required'      => false,
            'validators'    => array($dateValidator),
            'size'          => 10,
            'tabindex'      => $tabIndex++,
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
        $adrSubForm->addElement('radio', 'was_admitted', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Was Patient Admited Due to ADR?"),
                'tabindex'      => $tabIndex++,
                'multioptions'  => array(true => 'Yes', false => 'No'),
                'value'      => false
        ));
        $adrSubForm->addElement('radio', 'was_hospitalization_prolonged', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("If Already Hospitalized, Was it Prolonged Due to ADR?"),
                'tabindex'      => $tabIndex++,
                'multioptions'  => array(true => 'Yes', false => 'No'),
                'value'      => false
        ));
        $adrSubForm->addElement('text', 'duration_of_admission', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Duration of Admission (days)"),
                'size'          => 8,
                'maxlength'     => 8,
                'tabindex'      => $tabIndex++,
                'required'      => false,
                'filters'       => array(
                    new Zend_Filter_Callback(array($this, '_substrFilter'), array(0, 8)),
                    new Zend_Filter_StringTrim(),
                ),
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
                'patient'  => $patientSubForm,
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

