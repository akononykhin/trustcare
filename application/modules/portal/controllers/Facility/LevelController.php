<?php

class Portal_Facility_LevelController extends ZendX_Controller_Action
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
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.system_dict", "view");
    }
    
    public function listAction()
    {
        $columnsInfo = array(
            'name' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Name"),
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
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.system_dict", "view");
    }
    
    
    public function listLoadAction()
    {
        $select = Zend_Registry::getInstance()->dbAdapter->select()->from("facility_level", array('count(id)'));
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_NUM);
        $result = Zend_Registry::getInstance()->dbAdapter->fetchAll($select);
        $iTotal = $result[0][0];
        
        
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_ASSOC);
        $select = Zend_Registry::getInstance()->dbAdapter->select()
                                                         ->from("facility_level");

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
                    'title' => Zend_Registry::get("Zend_Translate")->_("Edit"),
                    'url' => $this->view->url(array('action' => 'edit', 'id' => $row['id'])),
                    'type' => 'edit'
                ),
                array(
                    'title' => Zend_Registry::get("Zend_Translate")->_("Delete"),
                    'url' => $this->view->url(array('action' => 'delete', 'id' => $row['id'])),
                    'type' => 'delete',
                    'askConfirm' => sprintf(Zend_Registry::get("Zend_Translate")->_("Are you sure you want to delete %s?"), $row['name'])
                ),
            );
            $output['aaData'][] = $row;
        }

                                              
        $this->_helper->json($output);
    }
    
    
    public function createActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.system_dict", "create");
    }
    
    public function createAction()
    {
        $form = $this->_getParametersForm();
        $form->setAction($this->getRequest()->getBaseUrl() . '/' . $this->getRequest()->getModuleName() . '/' . $this->getRequest()->getControllerName() . "/create");
        if($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
                try {
                    $name = $form->getValue('name');
                    
                    
                    $checkModel = TrustCare_Model_FacilityLevel::findByName($name);
                    if(!is_null($checkModel)) {
                        $errorMsg = sprintf(Zend_Registry::get("Zend_Translate")->_("Facility Level '%s' has already been used"), $name);
                        throw new Exception("");
                    }
                    
                    $model = new TrustCare_Model_FacilityLevel();
                    $model->setName($name);
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
        $this->view->form = $form;
        $this->render('general-form', null, true);
        return;
    }

    public function editActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.system_dict", "edit");
    }
    
    public function editAction()
    {
        $form = $this->_getParametersForm();
        $form->setAction($this->getRequest()->getBaseUrl() . '/' . $this->getRequest()->getModuleName() . '/' . $this->getRequest()->getControllerName() . "/edit");
        
        $id = $this->_getParam('id');
        $model = TrustCare_Model_FacilityLevel::find($id);
        if(is_null($model)) {
            $this->getLogger()->error(sprintf("'%s' tries to edit unknown facility type with id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Facility Level")));
            return;
        }
        
        if($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
                try {
                    $name = $form->getValue('name');
                    
                    if($name != $model->getName()) {
                        $checkModel = TrustCare_Model_FacilityLevel::findByName($name);
                        if(!is_null($checkModel)) {
                            $errorMsg = sprintf(Zend_Registry::get("Zend_Translate")->_("Facility Level '%s' has already been used"), $name);
                            throw new Exception("");
                        }
                    }
                    
                    $model->setName($name);
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
            $form->getElement("name")->setValue($model->name);
        }
        
        $this->view->form = $form;
        $this->render('general-form', null, true);
        return;
    }
    

    public function deleteActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.system_dict", "delete");
    }
    
    public function deleteAction()
    {
        $id = $this->_getParam('id');
        $model = TrustCare_Model_FacilityLevel::find($id);
        if(is_null($model)) {
            $this->getLogger()->error(sprintf("'%s' tries to delete unknown facility type with id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Facility Level")));
            return;
        }

        $model->delete();
        
        $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName());
    }
    
    
    
    
    /**
     * @return Zend_Form
     */
    private function _getParametersForm()
    {
        $form = new ZendX_Form();
        $form->setMethod('post');

        $form->addElement('hidden', 'id');
        
        $tabIndex = 1;
        $form->addElement('text', 'name', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Name"),
            'description'   => "",
            'size'          => 40,
            'tabindex'      => $tabIndex++,
            'required'      => true
        ));
                
        $form->addElement('submit', 'send', array(
            'label'     => Zend_Registry::get("Zend_Translate")->_("Save"),
            'tabindex'  => $tabIndex++,
        ));
        
        return $form;
    }
    
}

