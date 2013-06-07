<?php

class PhysicianController extends ZendX_Controller_Action
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
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.physician", "view");
    }
    
    public function listAction()
    {
        $columnsInfo = array(
            'identifier' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Physician ID"),
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
            'state_name' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("State"),
                'filter' => array(
                    'type' => 'text',
                ),
            ),
            'lga_name' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("LGA"),
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
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.physician", "view");
    }
    
    
    public function listLoadAction()
    {
        $select = Zend_Registry::getInstance()->dbAdapter->select()->from("physician", array('count(id)'));
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_NUM);
        $result = Zend_Registry::getInstance()->dbAdapter->fetchAll($select);
        $iTotal = $result[0][0];
        
        
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_ASSOC);
        $select = Zend_Registry::getInstance()->dbAdapter->select()
                                                         ->from("physician",
                                                                array('physician.*'))
                                                         ->joinLeft(array('lga'), 'physician.id_lga = lga.id', array('lga_name' => 'lga.name'))
                                                         ->joinLeft(array('country'), 'physician.id_country = country.id', array('country_name' => 'country.name'))
                                                         ->joinLeft(array('state'), 'physician.id_state = state.id', array('state_name' => 'state.name'))
                                                         ->joinLeft(array('facility'), 'physician.id_facility = facility.id', array('facility_name' => 'facility.name'));
                                                         
                                                         
        $this->processListLoadAjaxRequest($select, array('country_name' => 'country.name', 'state_name' => 'state.name', 'lga_name' => 'lga.name', 'facility_name' => 'facility.name'));
        
        $rows = Zend_Registry::getInstance()->dbAdapter->selectWithLimit($select->__toString(), $iFilteredTotal);
        
        $output = array(
            "sEcho" => intval($_REQUEST['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );
        foreach ($rows as $row) {
            $row['DT_RowId'] = $row['id'];
            
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
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.physician", "create");
    }
    
    public function createAction()
    {
        $form = $this->_getParametersForm(true);
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
                    
                    $checkModel = TrustCare_Model_Physician::findByIdentifier($identifier);
                    if(!is_null($checkModel)) {
                        $errorMsg = sprintf(Zend_Registry::get("Zend_Translate")->_("Identifier %s has already been used"), $identifier);
                        throw new Exception("");
                    }
                    
                    $model = new TrustCare_Model_Physician();
                    $model->setIdentifier($form->getValue('identifier'));
                    $model->setFirstName($form->getValue('first_name'));
                    $model->setLastName($form->getValue('last_name'));
                    $model->setIdCountry($form->getValue("id_country"));
                    $model->setIdState($form->getValue("id_state"));
                    $model->setAddress($form->getValue("address"));
                    $model->setIdLga($form->getValue("id_lga"));
                    $model->setIdFacility($form->getValue("id_facility"));
                    $model->save();

                    if(empty($forDialog)) {
                        $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName());
                    }
                    else {
                        $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Physician Created"), 'for_dialog' => 1));
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
        $this->render('general-form', null, true);
        return;
    }

    public function editActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.physician", "edit");
    }
    
    public function editAction()
    {
        $form = $this->_getParametersForm();
        $form->setAction($this->getRequest()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . "/edit");
        
        $id = $this->_getParam('id');
        $model = TrustCare_Model_Physician::find($id);
        if(is_null($model)) {
            $this->getLogger()->error(sprintf("'%s' tries to edit unknown physician with id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Physician")));
            return;
        }
        
        if($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
                try {
                    $identifier = $form->getValue('identifier');
                    
                    if($identifier != $model->getIdentifier()) {
                        $checkModel = TrustCare_Model_Physician::findByIdentifier($identifier);
                        if(!is_null($checkModel)) {
                            $errorMsg = sprintf(Zend_Registry::get("Zend_Translate")->_("Physician %s has already been used"), $identifier);
                            throw new Exception("");
                        }
                    }
                    
                    $model->setIdentifier($form->getValue('identifier'));
                    $model->setFirstName($form->getValue('first_name'));
                    $model->setLastName($form->getValue('last_name'));
                    $model->setIdCountry($form->getValue("id_country"));
                    $model->setIdState($form->getValue("id_state"));
                    $model->setAddress($form->getValue("address"));
                    $model->setIdLga($form->getValue("id_lga"));
                    $model->setIdFacility($form->getValue("id_facility"));
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
            $form->getElement("first_name")->setValue($model->first_name);
            $form->getElement("last_name")->setValue($model->last_name);
            $form->getElement("id_country")->setValue($model->id_country);
            $form->getElement("id_state")->setValue($model->id_state);
            $form->getElement("address")->setValue($model->address);
            $form->getElement("id_lga")->setValue($model->id_lga);
            $form->getElement("id_facility")->setValue($model->id_facility);
        }
        
        $this->view->form = $form;
        $this->render('general-form', null, true);
        return;
    }
    

    public function deleteActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.physician", "delete");
    }
    
    public function deleteAction()
    {
        $id = $this->_getParam('id');
        $model = TrustCare_Model_Physician::find($id);
        if(is_null($model)) {
            $this->getLogger()->error(sprintf("'%s' tries to delete unknown physician with id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Physician")));
            return;
        }

        $model->delete();
        
        $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName());
    }
    
    
    
    public function getListAjaxActionAccess()
    {
        return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.physician", "view");
    }
    
    public function getListAjaxAction()
    {
        $o = array();
    
        try {
            $filteredBy = $this->_getParam('term');
    
            $model = new TrustCare_Model_Physician();
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
    
    
    /**
     * @return Zend_Form
     */
    private function _getParametersForm()
    {
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
        
        $lgaList = array();
        $lgaList[''] = '';
        $model = new TrustCare_Model_Lga();
        foreach ($model->fetchAll() as $obj) {
            $lgaList[$obj->getId()] = $obj->getName();
        }
        
        $facilityList = array();
        $facilityList[''] = '';
        $model = new TrustCare_Model_Facility();
        foreach ($model->fetchAll() as $obj) {
            $facilityList[$obj->getId()] = $obj->getName();
        }
        
        
        $form = new ZendX_Form();
        $form->setMethod('post');

        $form->addElement('hidden', 'id');
        
        $tabIndex = 3000;
        $form->addElement('text', 'identifier', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Physycian ID"),
            'description'   => "",
            'size'          => 8,
            'tabindex'      => $tabIndex++,
            'required'      => true
        ));
        $form->addElement('text', 'first_name', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("First Name"),
            'description'   => "",
            'size'          => 32,
            'tabindex'      => $tabIndex++,
            'required'      => false
        ));
        $form->addElement('text', 'last_name', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Last Name"),
            'description'   => "",
            'size'          => 32,
            'tabindex'      => $tabIndex++,
            'required'      => false
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
        $form->addElement('text', 'address', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Address"),
            'description'   => "",
            'size'          => 32,
            'tabindex'      => $tabIndex++,
            'required'      => false
        ));
        $form->addElement('select', 'id_lga', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("LGA"),
            'tabindex'      => $tabIndex++,
            'required'      => false,
            'multioptions'  => $lgaList,
            'description'   => '',
        ));
        $form->addElement('select', 'id_facility', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Facility"),
            'tabindex'      => $tabIndex++,
            'required'      => false,
            'multioptions'  => $facilityList,
            'description'   => '',
        ));
        
        
        $form->addElement('submit', 'send', array(
            'label'     => Zend_Registry::get("Zend_Translate")->_("Save"),
            'tabindex'  => $tabIndex++,
        ));
        
        return $form;
    }
    
}

