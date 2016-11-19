<?php

class Portal_PatientController extends ZendX_Controller_Action
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
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.patient", "view");
    }
    
    public function listAction()
    {
        $columnsInfo = array(
            'identifier' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Client ID"),
                'filter' => array(
                    'type' => 'text',
                ),
            ),
            'is_active' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Is Active"),
            ),
            'first_name' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("First Name"),
                'filter' => array(
                    'type' => 'text',
                ),
            ),
            'last_name' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Last Name"),
                'filter' => array(
                    'type' => 'text',
                ),
            ),
            'address' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Address"),
                'filter' => array(
                    'type' => 'text',
                ),
            ),
            'country_name' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Country"),
                'filter' => array(
                    'type' => 'text',
                ),
            ),
            'state_name' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("State"),
                'filter' => array(
                    'type' => 'text',
                ),
            ),
			'physician_name' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Physician"),
                'filter' => array(
                    'type' => 'text',
                ),
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
            'defSortDir' => 'asc',
            'columnsInfo' => $columnsInfo,
            'bActionsColumn' => true
        );
        $this->render('list', null, true);
        return;
    }

    
    public function listLoadActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.patient", "view");
    }
    
    
    public function listLoadAction()
    {
        $select = Zend_Registry::getInstance()->dbAdapter->select()->from("patient", array('count(id)'));
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_NUM);
        $result = Zend_Registry::getInstance()->dbAdapter->fetchAll($select);
        $iTotal = $result[0][0];
        
        
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_ASSOC);
        $select = Zend_Registry::getInstance()->dbAdapter->select()
                                                         ->from("patient",
                                                                array('patient.*'))
                                                         ->joinLeft(array('physician'), 'patient.id_physician = physician.id', array('physician_name' => 'physician.identifier'))
                                                         ->joinLeft(array('country'), 'patient.id_country = country.id', array('country_name' => 'country.name'))
                                                         ->joinLeft(array('state'), 'patient.id_state = state.id', array('state_name' => 'state.name'));
                                                                 
        $this->processListLoadAjaxRequest($select, array(
                                                    'address' => 'patient.address',
        											'country_name' => 'country.name',
        											'state_name' => 'state.name',
        											'physician_name' => 'physician.identifier',
        											'identifier' => 'patient.identifier',
        											'first_name' => 'patient.first_name',
        											'last_name' => 'patient.last_name',
        ));
                                                                 
        $rows = Zend_Registry::getInstance()->dbAdapter->selectWithLimit($select->__toString(), $iFilteredTotal);
        
        $output = array(
            "sEcho" => intval($_REQUEST['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );
        foreach ($rows as $row) {
            $row['DT_RowId'] = $row['id'];
            $row['is_active'] = !empty($row['is_active']) ? Zend_Registry::get("Zend_Translate")->_("Yes") : '';
            
            $row['_row_actions_'] = array(
                array(
                    'title' => Zend_Registry::get("Zend_Translate")->_("Edit"),
                    'url' => $this->view->url(array('action' => 'edit', 'id' => $row['id'])),
                    'type' => 'edit'
                ),
                array(
                    'title' => Zend_Registry::get("Zend_Translate")->_("Delete"),
                    'url' => $this->view->url(array('action' => 'delete', 'id' => $row['id'])),
                    'type' => 'delete',
                    'askConfirm' => sprintf(Zend_Registry::get("Zend_Translate")->_("Are you sure you want to delete %s (%s)?"), $row['last_name'], $row['identifier'])
                ),
            );
            $output['aaData'][] = $row;
        }

                                              
        $this->_helper->json($output);
    }
    
    
    public function createActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.patient", "create");
    }
    
    public function createAction()
    {
        $form = $this->_getParametersForm();
        $form->setAction($this->getRequest()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . "/create");
        
        $forDialog = $this->_getParam('for_dialog');
        if(!empty($forDialog)) {
            $this->_helper->layout()->disableLayout();
            $form->addElement('hidden', 'for_dialog', array('value' => 1));
        }
        
        if($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
                try {
                    $identifier = $form->getValue('identifier');
                    
                    $checkModel = TrustCare_Model_Patient::findByIdentifier($identifier);
                    if(!is_null($checkModel)) {
                        $errorMsg = sprintf(Zend_Registry::get("Zend_Translate")->_("Identifier %s has already been used"), $identifier);
                        throw new Exception("");
                    }
                    
                    $model = new TrustCare_Model_Patient();
                    $model->setIdentifier($form->getValue('identifier'));
                    $model->setIsActive($form->getValue("is_active"));
                    $model->setFirstName($form->getValue("first_name"));
                    $model->setLastName($form->getValue("last_name"));
                    $model->setIdPhysician($form->getValue("id_physician"));
                    $model->setIdCountry($form->getValue("id_country"));
                    $model->setIdState($form->getValue("id_state"));
                    $model->setCity($form->getValue("city"));
                    $model->setAddress($form->getValue("address"));
                    $model->setZip($form->getValue("zip"));
                    $model->setPhone($form->getValue("phone"));
                    $model->setIsMale($form->getValue("is_male"));
                    $model->setBirthdate($form->getValue("birthdate"));
                    $model->save();

                    if(empty($forDialog)) {
                        $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName());
                    }
                    else {
                        $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Patient Created"), 'for_dialog' => 1));
                        return;
                    }
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
        $this->render('form');
        return;
    }

    public function editActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.patient", "edit");
    }
    
    public function editAction()
    {
        $id = $this->_getParam('id');
        $model = TrustCare_Model_Patient::find($id);
        if(is_null($model)) {
            $this->getLogger()->error(sprintf("'%s' tries to edit unknown patient with id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Patient")));
            return;
        }
        
        $form = $this->_getParametersForm();
        $form->setAction($this->getRequest()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . "/edit");
        
        if($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
                try {
                    $identifier = $form->getValue('identifier');
                    
                    if($identifier != $model->getIdentifier()) {
                        $checkModel = TrustCare_Model_Patient::findByIdentifier($identifier);
                        if(!is_null($checkModel)) {
                            $errorMsg = sprintf(Zend_Registry::get("Zend_Translate")->_("Patient %s has already been used"), $identifier);
                            throw new Exception("");
                        }
                    }
                    
                    $model->setIdentifier($form->getValue('identifier'));
                    $model->setIsActive($form->getValue("is_active"));
                    $model->setFirstName($form->getValue("first_name"));
                    $model->setLastName($form->getValue("last_name"));
                    $model->setIdPhysician($form->getValue("id_physician"));
                    $model->setIdCountry($form->getValue("id_country"));
                    $model->setIdState($form->getValue("id_state"));
                    $model->setCity($form->getValue("city"));
                    $model->setAddress($form->getValue("address"));
                    $model->setZip($form->getValue("zip"));
                    $model->setPhone($form->getValue("phone"));
                    $model->setIsMale($form->getValue("is_male"));
                    $model->setBirthdate($form->getValue("birthdate"));
                    $model->save();
                                        
                    $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName());
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
        else {
            $form->getElement("id")->setValue($id);
            $form->getElement("identifier")->setValue($model->identifier);
            $form->getElement("is_active")->setValue($model->is_active);
            $form->getElement("first_name")->setValue($model->first_name);
            $form->getElement("last_name")->setValue($model->last_name);
            $form->getElement("id_country")->setValue($model->id_country);
            $form->getElement("id_state")->setValue($model->id_state);
            $form->getElement("city")->setValue($model->city);
            $form->getElement("address")->setValue($model->address);
            $form->getElement("zip")->setValue($model->zip);
            $form->getElement("phone")->setValue($model->phone);
            $form->getElement("birthdate")->setValue($model->birthdate);
            $form->getElement("is_male")->setValue($model->is_male);
            
            $physicianModel = TrustCare_Model_Physician::find($model->id_physician);
            if(!is_null($physicianModel)) {
                $form->getElement("id_physician")->setValue($model->id_physician);
                $form->getElement("physician_name")->setValue($physicianModel->showNameAs());
            }
        }
        
        $this->view->form = $form;
        $this->render('form');
        return;
    }
    

    public function deleteActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.patient", "delete");
    }
    
    public function deleteAction()
    {
        $id = $this->_getParam('id');
        $model = TrustCare_Model_Patient::find($id);
        if(is_null($model)) {
            $this->getLogger()->error(sprintf("'%s' tries to delete unknown patient with id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Patient")));
            return;
        }
        
        if(TrustCare_Model_FrmCare::getNumberOfFormsForPatient($id)) {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Forbidden to delete the patient used for generating Pharmaceutical Care forms.")));
            return;
        }
        
        if(TrustCare_Model_FrmCommunity::getNumberOfFormsForPatient($id)) {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Forbidden to delete the patient used for generating Community Pharmacy forms.")));
            return;
        }
        
        $model->delete();
        
        $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName());
    }
    
    
    public function loadArrayOfActiveActionAccess()
    {
        return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.patient", "view");
    }
    
    public function loadArrayOfActiveAction()
    {
        $o = array();
    
        try {
            $filteredBy = $this->_getParam('term');
    
            $model = new TrustCare_Model_Patient();
            $objs = $model->fetchAllFilteredBy($filteredBy);
            foreach($objs as $obj) {
                $o[] = array(
                    'label' => $obj->showNameAs(),
                    'value' => $obj->getId(),
                );
            }
        }
        catch(Exception $ex) {
            $exMessage = $ex->getMessage();
            if(!empty($exMessage)) {
                $this->getLogger()->error($exMessage);
            }
        }
                                              
        $this->_helper->json($o);
    }
    
    
    public function checkIsMaleActionAccess()
    {
        return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.patient", "view");
    }
    
    public function checkIsMaleAction()
    {
        $o = new stdClass();
        $o->success = false;
        
        try {
            $id = $this->_getParam('id');
            $model = TrustCare_Model_Patient::find($id);
            $isMale = ($model && $model->getIsMale()) ? 1 : 0; 
            
            $o->success = true;
            $o->is_male = $isMale;
        }
        catch(Exception $ex) {
        }
                                              
        $this->_helper->json($o);
    }
    
    /**
     * @return Zend_Form
     */
    private function _getParametersForm()
    {
        $genderList = array(1 => 'Male', 0 => 'Female');
        
        $countryList = array();
        $countryList[''] = '';
        $model = new TrustCare_Model_Country();
        foreach ($model->fetchAll() as $obj) {
            $countryList[$obj->getId()] = $obj->getName();
        }
        
        $stateList = array();
        $stateList[''] = '';
        $model = new TrustCare_Model_State();
        foreach ($model->fetchAll() as $obj) {
            $stateList[$obj->getId()] = $obj->getName();
        }
        
        
        $dateValidator = new Zend_Validate_Date('yyyy-MM-dd');
        $dateValidator->setMessage(Zend_Registry::get("Zend_Translate")->_("Incorrect date"));
        
        
        $form = new ZendX_Form();
        $form->setMethod('post');

        $form->addElement('hidden', 'id');
        $form->addElement('hidden', 'id_physician', array('id' => 'id_physician'));
        
        $tabIndex = 2000;
        $form->addElement('text', 'identifier', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Client ID"),
            'description'   => "",
            'size'          => 16,
            'tabindex'      => $tabIndex++,
            'required'      => true
        ));
        $form->addElement('checkbox', 'is_active', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Is Active"),
            'tabindex'      => $tabIndex++,
            'checked'      => true
        ));
        $form->addElement('select', 'is_male', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Gender"),
            'tabindex'      => $tabIndex++,
            'required'      => false,
            'multioptions'  => $genderList,
            'description'   => '',
        ));
        $form->addElement('text', 'first_name', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("First Name"),
            'description'   => "",
            'required'      => true,
            'size'          => 32,
            'tabindex'      => $tabIndex++,
        ));
        $form->addElement('text', 'last_name', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Last Name"),
            'description'   => "",
            'required'      => true,
            'size'          => 32,
            'tabindex'      => $tabIndex++,
        ));
        $form->addElement('text', 'physician_name', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Physician"),
            'id'			=> 'physician_name',
            'text_htmlsuf'  => sprintf("<a id='link-add-physician' href='#'>%s</a>", Zend_Registry::get("Zend_Translate")->_("Add")),
            'tabindex'      => $tabIndex++,
            'required'      => false,
            'size'          => 32,
            'description'   => '',
        ));
        $form->addElement('text', 'birthdate', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Date of Birth"),
            'id'			=> 'id_birthdate',
            'description'   => "",
            'required'      => true,
        	'validators'    => array($dateValidator),
        	'size'          => 10,
            'tabindex'      => $tabIndex++,
        ));
        $form->addElement('select', 'id_country', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Country"),
            'tabindex'      => $tabIndex++,
            'required'      => false,
            'multioptions'  => $countryList,
            'description'   => '',
        ));
        $form->addElement('select', 'id_state', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("State"),
            'tabindex'      => $tabIndex++,
            'required'      => false,
            'multioptions'  => $stateList,
            'description'   => '',
        ));
        $form->addElement('text', 'city', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("City"),
            'description'   => "",
            'size'          => 16,
            'tabindex'      => $tabIndex++,
            'required'      => false
        ));
        $form->addElement('text', 'address', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Address"),
            'description'   => "",
            'size'          => 32,
            'tabindex'      => $tabIndex++,
            'required'      => false
        ));
        $form->addElement('text', 'zip', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Zip"),
            'description'   => "",
            'size'          => 16,
            'tabindex'      => $tabIndex++,
            'required'      => false
        ));
        $form->addElement('text', 'phone', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Phone"),
            'description'   => "",
            'size'          => 16,
            'tabindex'      => $tabIndex++,
            'required'      => false
        ));
        
        
        $form->addElement('submit', 'send', array(
            'label'     => Zend_Registry::get("Zend_Translate")->_("Save"),
            'tabindex'  => $tabIndex++,
        ));
        
        return $form;
    }
    
}

