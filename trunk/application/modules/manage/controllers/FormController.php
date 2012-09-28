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
            'engine' => array(
                'title' => 'Engine',
                'sortable' => true,
                'visible' => true,
                'filter' => array(
                    'def_text' => "Search engine ...",
                    'type' => 'select',
                    'values' => array('AAA', 'Gecko', 'Trident', 'KHTML', 'Misc', 'Presto', 'Webkit', 'Tasman')
                ),
            ),
            'browser' => array(
                'title' => 'Browser',
                'sortable' => true,
                'visible' => true,
                'filter' => array(
                    'def_text' => "Search browser ...",
                    'type' => 'text',
                ),
            ),
            'platform' => array(
                'title' => 'Platform',
                'sortable' => true,
                'visible' => true,
            ),
            'version' => array(
                'title' => 'Engine version',
                'sortable' => true,
                'visible' => false,
                'filter' => array(
                    'type' => 'text',
                ),
            ),
            'grade' => array(
                'title' => 'CSS grade',
                'sortable' => false,
                'visible' => true,
                'filter' => array(
                ),
            ),
            );

        $this->view->chooseColumnVisibility = true;
        $this->view->columnsInfo = $columnsInfo;
        $this->render('list');
        return;
    }
}

