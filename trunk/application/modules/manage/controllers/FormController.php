<?php

class FormController extends ZendX_Controller_Action
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
        $type = $this->_getParam('type');
        if('care' != $type && 'community' != $type) {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Incorrect type of counseling")));
            return;
        }
        
        $columnsInfo = array(
            'id' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("ID"),
                'width' => '5%',
            ),
            'date_of_visit' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Date of Visit"),
                'width' => '15%',
            ),
            'patient_identifier' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Patient ID"),
                'filter' => array(
                    'type' => 'text',
                ),
            ),
            'patient_last_name' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Last Name"),
            ),
            'patient_first_name' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("First Name"),
            ),
        );

        $this->view->DataTable = array(
            'serverUrl' => $this->view->url(array('action' => 'list-load')),
            'params' => array(
                'type' => $type,
            ),
            'toolbar' => array(
                array(
                    'text' => Zend_Registry::get("Zend_Translate")->_("Create"),
                    'url' => $this->view->url(array('action' => 'create', 'type' => $type))
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
        $type = $this->_getParam('type');
        if('care' == $type || 'community' == $type) {
            if('community' == $type) {
                $table = 'frm_community';
            }
            else {
                $table = 'frm_care';
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
                                                                    'date_of_visit' => new Zend_Db_Expr("date_format(date_of_visit, '%Y-%m-%d')"),
                                                                    ))
                                                             ->joinLeft(array('patient'), $table.'.id_patient = patient.id', array('patient_identifier' => 'patient.identifier',
                                                                                                                                  'patient_first_name' => 'patient.first_name',
                                                                                                                                  'patient_last_name' => 'patient.last_name'));

            $this->processListLoadAjaxRequest($select, array('patient_identifier' => 'patient.identifier',
                                                             'patient_first_name' => 'patient.first_name',
                                                             'patient_last_name' => 'patient.last_name'));

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
            $row['date_of_visit'] = $this->convertDateToUserTimezone($row['date_of_visit']);
            
            $row['_row_actions_'] = array(
                array(
                    'title' => Zend_Registry::get("Zend_Translate")->_("View"),
                    'url' => $this->view->url(array('action' => 'view', 'id' => $row['id'], 'type' => $type)),
                    'type' => 'edit'
                ),
                array(
                    'title' => Zend_Registry::get("Zend_Translate")->_("Delete"),
                    'url' => $this->view->url(array('action' => 'delete', 'id' => $row['id'], 'type' => $type)),
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
        $type = $this->_getParam('type');
        if('care' == $type) {
            return $this->_createCareForm();; 
        }
        else if('community' == $type) {
            return $this->_createCommunityForm();; 
        }
        else {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Incorrect type of counseling")));
            return;
        }
    }
    
    private function _createCareForm()
    {
        if($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
                try {

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
        
        $this->view->type = 'care';
        $this->view->allow_create_patient = Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.patient", "create");
        $this->render('create-care');
        return;
    }
    
    
    private function _createCommunityForm()
    {
        
    }
    
}

