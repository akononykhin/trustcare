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
        $columnsInfo = array(
            'tadig' => array(
                'title' => 'TADIG',
                'filter' => array(
                    'type' => 'text',
                ),
            ),
            'name' => array(
                'title' => 'Name',
                'filter' => array(
                    'type' => 'text',
                ),
            ),
            'standard' => array(
                'title' => 'Standard',
                'filter' => array(
                    'def_text' => "Search ...",
                    'type' => 'text',
                ),
            ),
            'url_site' => array(
                'title' => 'Site',
                'visible' => false,
                'filter' => array(
                    'def_text' => "Search ...",
                    'type' => 'text',
                ),
            ),
            'url_coverage' => array(
                'title' => 'Coverage',
                'filter' => array(
                    'def_text' => "Search ...",
                    'type' => 'text',
                ),
            ),
        );

        $this->view->DataTable = array(
            'serverUrl' => '/test.php',
            'params' => array(
                'param1' => '1_value',
                'param2' => '2_value',
                'param3' => '3_value',
            ),
            'toolbar' => array(
                array(
                    'text' => 'Create',
                    'url' => '/create.php'
                ),
                array(
                    'text' => 'Import',
                    'url' => '/import.php'
                ),
                array(
                    'text' => 'Export',
                    'url' => '/export.php'
                ),
            ),
            'defSortColumn' => 1,
            'defSortDir' => 'asc',
            'chooseColumnVisibility' => true,
            'columnsInfo' => $columnsInfo,
            'bActionsColumn' => true
        );
        $this->render('list', null, true);
        return;
    }
}

