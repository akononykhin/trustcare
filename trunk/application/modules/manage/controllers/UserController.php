<?php

class UserController extends ZendX_Controller_Action
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
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.user", "view");
    }
    
    public function listAction()
    {
        $columnsInfo = array(
            'login' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("User ID"),
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
            'pharmacy_name' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Pharmacy"),
            ),
            'role' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Role"),
            ),
            'is_active' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Is Active"),
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
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.user", "view");
    }
    
    
    public function listLoadAction()
    {
        $user = Zend_Registry::get("TrustCare_Registry_User")->getUser();
        $pharmacyModel = TrustCare_Model_Pharmacy::find($user->getIdPharmacy());
        
        $select = Zend_Registry::getInstance()->dbAdapter->select()->from("user", array('count(id)'));
        if(!is_null($pharmacyModel)) {
            $select->where('id_pharmacy=?', $pharmacyModel->getId());
        }
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_NUM);
        $result = Zend_Registry::getInstance()->dbAdapter->fetchAll($select);
        $iTotal = $result[0][0];
        
        
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_ASSOC);
        $select = Zend_Registry::getInstance()->dbAdapter->select()
                                                         ->from("user",
                                                                array('user.*'))
                                                         ->joinLeft(array('pharmacy'), 'user.id_pharmacy = pharmacy.id', array('pharmacy_name' => 'pharmacy.name'));
        if(!is_null($pharmacyModel)) {
            $select->where('user.id_pharmacy=?', $pharmacyModel->getId());
        }
                                                         
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
                    'askConfirm' => sprintf(Zend_Registry::get("Zend_Translate")->_("Are you sure you want to delete %s?"), $row['login'])
                ),
            );
            $output['aaData'][] = $row;
        }

                                              
        $this->_helper->json($output);
    }
    
    
    public function createActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.user", "create");
    }
    
    public function createAction()
    {
        $form = $this->_getParametersForm(true);
        $form->setAction($this->getRequest()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . "/create");
        if($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
                try {
                    $password = $form->getValue('password');
                    $c_password = $form->getValue('confirm_password');
                    
                    if(!empty($password) && $password != $c_password) {
                        $errorMsg = Zend_Registry::get("Zend_Translate")->_("Passwords not match");
                        throw new Exception("");
                    }
                    
                    $model = new TrustCare_Model_User();
                    $model->setLogin($form->getValue('login'));
                    $model->setPassword(md5($password));
                    $model->setIsActive($form->getValue("is_active"));
                    $model->setFirstName($form->getValue("first_name"));
                    $model->setLastName($form->getValue("last_name"));
                    $model->setRole($form->getValue("role"));
                    $model->setIdPharmacy($form->getValue("id_pharmacy"));
                    $model->setIdCountry($form->getValue("id_country"));
                    $model->setIdState($form->getValue("id_state"));
                    $model->setCity($form->getValue("city"));
                    $model->setAddress($form->getValue("address"));
                    $model->setZip($form->getValue("zip"));
                    $model->setPhone($form->getValue("phone"));
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
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.user", "edit");
    }
    
    public function editAction()
    {
        $form = $this->_getParametersForm();
        $form->setAction($this->getRequest()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . "/edit");
        $form->getElement("login")->setAttrib('readonly', true);
        
        $id = $this->_getParam('id');
        $model = TrustCare_Model_User::find($id);
        if(is_null($model)) {
            $this->getLogger()->error(sprintf("'%s' tries to edit unknown user with id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown User")));
            return;
        }
        
        if($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
                try {
                    $password = $form->getValue('password');
                    $c_password = $form->getValue('confirm_password');
                    
                    if(!empty($password) && $password != $c_password) {
                        $errorMsg = Zend_Registry::get("Zend_Translate")->_("Passwords not match");
                        throw new Exception("");
                    }
                    
                    $model->setLogin($form->getValue('login'));
                    if(!empty($password)) {
                        $model->setPassword(md5($password));
                    }
                    $model->setIsActive($form->getValue("is_active"));
                    $model->setFirstName($form->getValue("first_name"));
                    $model->setLastName($form->getValue("last_name"));
                    $model->setRole($form->getValue("role"));
                    $model->setIdPharmacy($form->getValue("id_pharmacy"));
                    $model->setIdCountry($form->getValue("id_country"));
                    $model->setIdState($form->getValue("id_state"));
                    $model->setCity($form->getValue("city"));
                    $model->setAddress($form->getValue("address"));
                    $model->setZip($form->getValue("zip"));
                    $model->setPhone($form->getValue("phone"));
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
            $form->getElement("login")->setValue($model->login);
            $form->getElement("is_active")->setValue($model->is_active);
            $form->getElement("first_name")->setValue($model->first_name);
            $form->getElement("last_name")->setValue($model->last_name);
            $form->getElement("role")->setValue($model->role);
            $form->getElement("id_pharmacy")->setValue($model->id_pharmacy);
            $form->getElement("id_country")->setValue($model->id_country);
            $form->getElement("id_state")->setValue($model->id_state);
            $form->getElement("city")->setValue($model->city);
            $form->getElement("address")->setValue($model->address);
            $form->getElement("zip")->setValue($model->zip);
            $form->getElement("phone")->setValue($model->phone);
        }
        
        $this->view->form = $form;
        $this->render('general-form', null, true);
        return;
    }
    

    public function deleteActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.user", "delete");
    }
    
    public function deleteAction()
    {
        $id = $this->_getParam('id');
        $model = TrustCare_Model_User::find($id);
        if(is_null($model)) {
            $this->getLogger()->error(sprintf("'%s' tries to delete unknown user with id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown User")));
            return;
        }

        $model->delete();
        
        $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName());
    }
    
    
    
    
    /**
     * @return Zend_Form
     */
    private function _getParametersForm($isCreate = false)
    {
        $user = Zend_Registry::get("TrustCare_Registry_User")->getUser();
        $pharmacyModel = TrustCare_Model_Pharmacy::find($user->getIdPharmacy());
        
        $rolesList = array('' => '',
        				   'pharmacy_manager' => 'pharmacy_manager',
                           'pharmacist' => 'pharmacist');
        
        $pharmacyList = array();
        $pharmacyList[''] = '';
        $model = new TrustCare_Model_Pharmacy();
        foreach ($model->fetchAll() as $obj) {
            if(!is_null($pharmacyModel) && $pharmacyModel->getId() != $obj->getId()) {
                continue;
            }
            $pharmacyList[$obj->getId()] = $obj->getName();
        }
        
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
        
        
        $form = new ZendX_Form();
        $form->setMethod('post');

        $form->addElement('hidden', 'id');
        
        $tabIndex = 1;
        $form->addElement('text', 'login', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("User ID"),
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
        $form->addElement('password', 'password', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Password"),
            'description'   => "",
            'tabindex'      => $tabIndex++,
            'required'		=> $isCreate
        ));
        
        $form->addElement('password', 'confirm_password', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Confirm Password"),
            'description'   => "",
            'tabindex'      => $tabIndex++,
        ));
        $form->addElement('text', 'first_name', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("First Name"),
            'description'   => "",
            'size'          => 32,
            'tabindex'      => $tabIndex++,
        ));
        $form->addElement('text', 'last_name', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Last Name"),
            'description'   => "",
            'size'          => 32,
            'tabindex'      => $tabIndex++,
        ));
        $form->addElement('select', 'role', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Role"),
            'tabindex'      => $tabIndex++,
            'required'      => true,
            'multioptions'  => $rolesList,
            'description'   => '',
        ));
        $form->addElement('select', 'id_pharmacy', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Pharmacy"),
            'tabindex'      => $tabIndex++,
            'required'      => true,
            'multioptions'  => $pharmacyList,
            'description'   => '',
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

