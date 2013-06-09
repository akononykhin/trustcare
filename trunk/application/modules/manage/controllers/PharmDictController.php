<?php

class PharmDictController extends ZendX_Controller_Action
{

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $this->getRedirector()->gotoSimpleAndExit("list", $this->getRequest()->getControllerName());
    }
    
    public function typesListActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.pharm_dict", "view");
    }
    
    public function typesListAction()
    {
        $columnsInfo = array(
            'id' => array(
                'title' => 'ID',
                'width' => '10%',
            ),
        	'name' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Name"),
                'filter' => array(
                    'type' => 'text',
                ),
            ),
        );

        $this->view->DataTable = array(
            'serverUrl' => $this->view->url(array('action' => 'types-list-load')),
            'defSortColumn' => 1,
            'defSortDir' => 'asc',
            'columnsInfo' => $columnsInfo,
            'bActionsColumn' => true
        );
        $this->render('list', null, true);
        return;
    }

    
    public function typesListLoadActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.pharm_dict", "view");
    }
    
    
    public function typesListLoadAction()
    {
        $select = Zend_Registry::getInstance()->dbAdapter->select()->from("pharmacy_dictionary_type", array('count(id)'));
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_NUM);
        $result = Zend_Registry::getInstance()->dbAdapter->fetchAll($select);
        $iTotal = $result[0][0];
        
        
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_ASSOC);
        $select = Zend_Registry::getInstance()->dbAdapter->select()->from("pharmacy_dictionary_type");

        $this->processListLoadAjaxRequest($select);
        
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
                    'title' => Zend_Registry::get("Zend_Translate")->_("View"),
                    'url' => $this->view->url(array('action' => 'list', 'type_id' => $row['id'])),
                    'type' => 'view'
                ),
            );
            $output['aaData'][] = $row;
        }

                                              
        $this->_helper->json($output);
    }
    
    
    public function listActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.pharm_dict", "view");
    }
    
    public function listAction()
    {
        $typeId = $this->_getParam('type_id');
        
        $model = TrustCare_Model_PharmacyDictionaryType::find($typeId);
        
        $columnsInfo = array(
        	'is_active' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Is Active"),
                'width' => '5%',
            ),
            'name' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Name"),
                'filter' => array(
                    'type' => 'text',
                ),
            ),
        );

        $this->view->DataTable = array(
            'serverUrl' => $this->view->url(array('action' => 'list-load', 'type_id' => $typeId)),
            'toolbar' => array(
                array(
                    'text' => Zend_Registry::get("Zend_Translate")->_("Create"),
                    'url' => $this->view->url(array('action' => 'create', 'type_id' => $typeId))
                ),
            ),
            'params' => array(
                'type_id' => $typeId,
            ),
        	'defSortColumn' => 2,
            'defSortDir' => 'asc',
            'columnsInfo' => $columnsInfo,
            'bActionsColumn' => true
        );
        
        if(!is_null($model)) {
            $this->view->title = $model->getName();
        }
        $this->render('list', null, true);
        return;
    }

    
    public function listLoadActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.pharm_dict", "view");
    }
    
    
    public function listLoadAction()
    {
        $typeId = $this->_getParam('type_id');
        
        $select = Zend_Registry::getInstance()->dbAdapter->select()->from("pharmacy_dictionary", array('count(id)'))->where('id_pharmacy_dictionary_type=?', $typeId);
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_NUM);
        $result = Zend_Registry::getInstance()->dbAdapter->fetchAll($select);
        $iTotal = $result[0][0];
        
        
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_ASSOC);
        $select = Zend_Registry::getInstance()->dbAdapter->select()
                                                         ->from("pharmacy_dictionary")
                                                         ->where('id_pharmacy_dictionary_type=?', $typeId);
        
        $this->processListLoadAjaxRequest($select);
        
        $rows = Zend_Registry::getInstance()->dbAdapter->selectWithLimit($select->__toString(), $iFilteredTotal);
        
        $output = array(
            "sEcho" => intval($_REQUEST['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );
        foreach ($rows as $row) {
            $row['DT_RowId'] = $row['id'];
            $row['is_active'] = !empty($row['is_active']) ? Zend_Registry::get("Zend_Translate")->_("Yes") : Zend_Registry::get("Zend_Translate")->_("No");
            
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
                	'askConfirm' => sprintf(Zend_Registry::get("Zend_Translate")->_("Are you sure you want to delete %s?"), $row['name']),
                ),
            );
            $output['aaData'][] = $row;
        }

                                              
        $this->_helper->json($output);
    }
    
    
    public function createActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.pharm_dict", "create");
    }
    
    public function createAction()
    {
        $typeId = $this->_getParam('type_id');
        $typeModel = TrustCare_Model_PharmacyDictionaryType::find($typeId);
        
        $form = $this->_getParametersForm($typeId, true);
        $form->setAction($this->getRequest()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . "/create");
        if($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
                try {
                    $name = $form->getValue('name');
                    
                    $model = new TrustCare_Model_PharmacyDictionary();
                    $model->setName($name);
                    $model->setIdPharmacyDictionaryType($typeId);
                    $model->setIsActive(true);
                    $model->save();

                    $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName(), null, array('type_id' => $typeId));
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
        if(!is_null($typeModel)) {
            $this->view->type_name = $typeModel->getName();
        }
        $this->render('form');
        return;
    }

    public function editActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.pharm_dict", "edit");
    }
    
    public function editAction()
    {
        $typeId = $this->_getParam('type_id');
        $typeModel = TrustCare_Model_PharmacyDictionaryType::find($typeId);
        
        $form = $this->_getParametersForm($typeId, false);
        $form->setAction($this->getRequest()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . "/edit");
        
        $id = $this->_getParam('id');
        $model = TrustCare_Model_PharmacyDictionary::find($id);
        if(is_null($model)) {
            $this->getLogger()->error(sprintf("'%s' tries to edit unknown pharmacy_dictionary with id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Pharmacy Dictionary row")));
            return;
        }
        
        if($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
                try {
                    $name = $form->getValue('name');
                    
                    $model->setName($name);
                    $model->setIsActive($form->getValue(is_active));
                    $model->save();
                    
                    $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName(), null, array('type_id' => $typeId));
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
            $form->getElement("name")->setValue($model->name);
            $form->getElement("is_active")->setValue($model->is_active);
        }
        
        $this->view->form = $form;
        if(!is_null($typeModel)) {
            $this->view->type_name = $typeModel->getName();
        }
        $this->render('form');
        return;
    }
    

    public function deleteActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.pharm_dict", "delete");
    }
    
    public function deleteAction()
    {
        $typeId = $this->_getParam('type_id');
        $id = $this->_getParam('id');
        
        $db_options = Zend_Registry::get('dbOptions');
        $db = Zend_Db::factory($db_options['adapter'], $db_options['params']);
        
        $model = TrustCare_Model_PharmacyDictionary::find($id, array('mapperOptions' => array('adapter' => $db)));
        if(is_null($model)) {
            $this->getLogger()->error(sprintf("'%s' tries to delete unknown pharmacy_dictionary with id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Pharmacy Dictionary row")));
            return;
        }

        try {
            try {
                $db->logSQLErrors(false);
                $model->delete();
            }
            catch(Exception $ex1) {
                $db->logSQLErrors(true);
                $model->setIsActive(false);
                $model->save();
            }
            $db->logSQLErrors(true);
        }
        catch(Exception $ex) {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Internal Error")));
            return;
        }

        
        $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName(), null, array('type_id' => $typeId));
    }
    
    
    public function loadArrayForTypeActionAccess()
    {
        return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.pharm_dict", "view");
    }
    
    public function loadArrayForTypeAction()
    {
        $typeId = $this->_getParam('type_id');
        
        $o = new stdClass();
        $o->success = false;
        
        try {
            $rows = array();
            $model = new TrustCare_Model_PharmacyDictionary();
            foreach($model->fetchAll(array(sprintf("id_pharmacy_dictionary_type=%d", $typeId), "is_active=1"), 'name') as $obj) {
                $rows[$obj->getId()] = $obj->getName();
            }
             
            $o->rows = $rows;
            $o->success = true;
        }
        catch(Exception $ex) {
            
        }

                                              
        $this->_helper->json($o);
    }
    
    
    public function createAjaxActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.pharm_dict", "create");
    }
    
    public function createAjaxAction()
    {
        $o = new stdClass();
        $o->success = false;
        $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
        
        try {
            $typeId = $this->_getParam('type_id');
            $name = $this->_getParam('name');
            
            $typeModel = TrustCare_Model_PharmacyDictionaryType::find($typeId);
            if(is_null($typeModel)) {
                throw new Exception(sprintf("'%s' tries to create pharmacy dictionary entity with unknown id_pharmacy_dictionary_type=%s", Zend_Auth::getInstance()->getIdentity(), $typeId));
            }
            
            $model = new TrustCare_Model_PharmacyDictionary();
            $model->setName($name);
            $model->setIdPharmacyDictionaryType($typeId);
            $model->save();

            $o->success = true;
        }
        catch(Exception $ex) {
            $o->error = $errorMsg;
            
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
    private function _getParametersForm($typeId, $isCreate = true)
    {
        $form = new ZendX_Form();
        $form->setMethod('post');

        $form->addElement('hidden', 'id');
        $form->addElement('hidden', 'type_id', array('value' => $typeId));
        
        $tabIndex = 1;
        $form->addElement('text', 'name', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Name"),
            'description'   => "",
            'size'          => 40,
            'tabindex'      => $tabIndex++,
            'required'      => true
        ));
        if(!$isCreate) {
            $form->addElement('checkbox', 'is_active', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Is Active"),
                'tabindex'      => $tabIndex++,
                'checked'      => true
            ));
        }
        
        $form->addElement('submit', 'send', array(
            'label'     => Zend_Registry::get("Zend_Translate")->_("Save"),
            'tabindex'  => $tabIndex++,
        ));
        
        return $form;
    }
    
    
}

