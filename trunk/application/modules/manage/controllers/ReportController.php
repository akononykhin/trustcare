<?php

class ReportController extends ZendX_Controller_Action
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
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:report", "view");
    }
    
    public function listAction()
    {
        $type = $this->_getParam('type');
        if('care' != $type && 'community' != $type) {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Incorrect type of the report")));
            return;
        }
        
        $columnsInfo = array(
            'id' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("ID"),
                'width' => '5%',
            ),
            'generation_date' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Generation Date"),
                'width' => '15%',
            ),
            'pharmacy_name' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Pharmacy"),
                'filter' => array(
                    'type' => 'text',
                ),
            ),
            'period' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Period"),
                'filter' => array(
                    'type' => 'text',
                ),
            ),
            'user_login' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("User ID"),
                'filter' => array(
                    'type' => 'text',
                ),
            ),
        );

        $this->view->DataTable = array(
            'serverUrl' => $this->view->url(array('action' => 'list-load')),
            'params' => array(
                'type' => $type,
            ),
            'toolbar' => array(
                array(
                    'text' => Zend_Registry::get("Zend_Translate")->_("Generate"),
                    'url' => $this->view->url(array('action' => 'generate', 'type' => $type))
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
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:report", "view");
    }
    
    
    public function listLoadAction()
    {
        $type = $this->_getParam('type');
        if('care' == $type || 'community' == $type) {
            if('community' == $type) {
                $table = 'report_community';
            }
            else {
                $table = 'report_care';
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
																	'generation_date' => new Zend_Db_Expr(sprintf("date_format(%s.generation_date, '%%Y-%%m-%%d %%H:%%i:%%s')", $table)),
                                                                    $table.'.period'
                                                                    ))
                                                             ->joinLeft(array('user'), $table.'.id_user = user.id', array('user_login' => 'user.login'))
                                                             ->joinLeft(array('pharmacy'), $table.'.id_pharmacy = pharmacy.id', array('pharmacy_name' => 'pharmacy.name'));
                                                             
            $this->processListLoadAjaxRequest($select, array(
            			'user_login' => 'user.login',
                        'pharmacy_name' => 'pharmacy.name'
            ));

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
            $row['generation_date'] = $this->convertTimeToUserTimezone($row['generation_date']);
            $period = $row['period'];
            if(preg_match("/^(\d{4})(\d{2})$/", $period, $matches)) {
                $row['period'] = $matches[1].'-'.$matches[2];
            }

                        
            $row['_row_actions_'] = array(
                array(
                    'title' => Zend_Registry::get("Zend_Translate")->_("View"),
                    'url' => $this->view->url(array('action' => 'view', 'id' => $row['id'], 'type' => $type)),
                    'type' => 'view'
                ),
                array(
                    'title' => Zend_Registry::get("Zend_Translate")->_("Delete"),
                    'url' => $this->view->url(array('action' => 'delete', 'id' => $row['id'], 'type' => $type)),
                	'type' => 'delete',
                	'askConfirm' => sprintf(Zend_Registry::get("Zend_Translate")->_("Are you sure you want to delete report %s generated %s?"), $row['id'], $row['generation_date']),
                ),
            );
            $output['aaData'][] = $row;
        }

                                              
        $this->_helper->json($output);
    }
    
    
    public function generateActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:report", "create");
    }
    
    public function generateAction()
    {
        $type = $this->_getParam('type');
        if('care' == $type) {
            return $this->_generateCareReport(); 
        }
        else if('community' == $type) {
            return $this->_generateCommunityReport(); 
        }
        else {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Incorrect type of report")));
            return;
        }
    }
    
    private function _generateCareReport()
    {
        $form = $this->_getGenerateCareReportForm();
        $form->setAction($this->getRequest()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . "/generate/type/" . $this->_getParam('type'));
        
        if($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
                try {
                    $idPharmacy = $form->getValue('id_pharmacy');
                    $period = $form->getValue('period');
                    if(!preg_match("/^(\d{4})-(\d{2})$/", $period, $matches)) {
                        throw new Exception(sprintf("Incorrect period=%s for generating report.", $period));
                    }
                    $month = $matches[2];
                    $year = $matches[1];
                    $male_younger_15 = $form->getValue('male_younger_15');
                    $female_younger_15 = $form->getValue('female_younger_15');
                    $male_from_15 = $form->getValue('male_from_15');
                    $female_from_15 = $form->getValue('female_from_15');
                    $drugs = $form->getValue('drugs');
                    
                    $generator = TrustCare_SystemInterface_ReportGenerator_Abstract::factory(TrustCare_SystemInterface_ReportGenerator_Abstract::CODE_CARE);
                    
                    $obj = $generator->generate(array(
                                'id_user' => Zend_Registry::get("TrustCare_Registry_User")->getUser()->getId(),
                                'id_pharmacy' => $idPharmacy,
                                'year' => $year,
                                'month' => $month,
                                'month_index' => sprintf("%04s%02s", $year, $month),
                                'male_younger_15' => $male_younger_15,
                                'female_younger_15' => $female_younger_15,
                                'male_from_15' => $male_from_15,
                                'female_from_15' => $female_from_15,
                                'drugs' => $drugs
                    ));
                    $fileReportOutput = sprintf("%s/%s", $generator->reportsDirectory(), $obj->getFilename());
                    
                    if(!file_exists($fileReportOutput)) {
                        $errorMsg = Zend_Registry::get("Zend_Translate")->_("Report file not found");
                        throw new Exception(sprintf("Report file '%s' not found", $fileReportOutput));
                    }

                    $this->outputFileAsAttachment($fileReportOutput);
                    return;
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
    
    
    private function _generateCommunityReport()
    {
        $form = $this->_getGenerateCommunityReportForm();
        $form->setAction($this->getRequest()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . "/generate/type/" . $this->_getParam('type'));
        
        if($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
                try {
                    $idPharmacy = $form->getValue('id_pharmacy');
                    $period = $form->getValue('period');
                    if(!preg_match("/^(\d{4})-(\d{2})$/", $period, $matches)) {
                        throw new Exception(sprintf("Incorrect period=%s for generating report.", $period));
                    }
                    $month = $matches[2];
                    $year = $matches[1];
                    
                    $generator = TrustCare_SystemInterface_ReportGenerator_Abstract::factory(TrustCare_SystemInterface_ReportGenerator_Abstract::CODE_COMMUNITY);
                    
                    $obj = $generator->generate(array(
                                'id_user' => Zend_Registry::get("TrustCare_Registry_User")->getUser()->getId(),
                                'id_pharmacy' => $idPharmacy,
                                'year' => $year,
                                'month' => $month,
                    ));
                    $fileReportOutput = sprintf("%s/%s", $generator->reportsDirectory(), $obj->getFilename());
                    
                    if(!file_exists($fileReportOutput)) {
                        $errorMsg = Zend_Registry::get("Zend_Translate")->_("Report file not found");
                        throw new Exception(sprintf("Report file '%s' not found", $fileReportOutput));
                    }

                    $this->outputFileAsAttachment($fileReportOutput);
                    return;
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
    
    
    public function viewActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:report", "view");
    }
    
    public function viewAction()
    {
        $type = $this->_getParam('type');
        if('care' == $type) {
            return $this->_viewCareReport();; 
        }
        else if('community' == $type) {
            return $this->_viewCommunityReport();; 
        }
        else {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Incorrect type of counseling")));
            return;
        }
    }
    
    private function _viewCareReport()
    {
        $id = $this->_getParam('id');
        $reportModel = TrustCare_Model_ReportCare::find($id);
        if(is_null($reportModel)) {
            $this->getLogger()->error(sprintf("'%s' tries to view unknown report_care.id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Report")));
            return;
        }

        $pharmacyModel = TrustCare_Model_Pharmacy::find($reportModel->getIdPharmacy());
        if(is_null($pharmacyModel)) {
            $this->getLogger()->error(sprintf("Failed to load pharmacy.id=%s specified for report_care.id=%s", $reportModel->getIdPharmacy(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Internal Error")));
            return;
        }

        $authorModel = TrustCare_Model_User::find($reportModel->getIdUser());
        if(is_null($pharmacyModel)) {
            $this->getLogger()->error(sprintf("Failed to load user.id=%s specified for report_care.id=%s", $reportModel->getIdUser(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Internal Error")));
            return;
        }
        
        $this->view->type = $this->_getParam('type');
        $this->view->id = $this->_getParam('id');
        $this->view->reportModel = $reportModel;
        $this->view->pharmacyName = $pharmacyModel->getName();
        $this->view->authorName = $authorModel->getLogin();
        $this->render('view-care');
        return;
    }
    
    private function _viewCommunityReport()
    {
        $id = $this->_getParam('id');
        $reportModel = TrustCare_Model_ReportCommunity::find($id);
        if(is_null($reportModel)) {
            $this->getLogger()->error(sprintf("'%s' tries to view unknown report_community.id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown FReport")));
            return;
        }
        
        $pharmacyModel = TrustCare_Model_Pharmacy::find($reportModel->getIdPharmacy());
        if(is_null($pharmacyModel)) {
            $this->getLogger()->error(sprintf("Failed to load pharmacy.id=%s specified for report_community.id=%s", $reportModel->getIdPharmacy(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Internal Error")));
            return;
        }

        $authorModel = TrustCare_Model_User::find($reportModel->getIdUser());
        if(is_null($pharmacyModel)) {
            $this->getLogger()->error(sprintf("Failed to load user.id=%s specified for report_community.id=%s", $reportModel->getIdUser(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Internal Error")));
            return;
        }
        
        $this->view->type = $this->_getParam('type');
        $this->view->id = $this->_getParam('id');
        $this->view->reportModel = $reportModel;
        $this->view->pharmacyName = $pharmacyModel->getName();
        $this->view->authorName = $authorModel->getLogin();
        $this->render('view-community');
        return;
    }
    
    
    
    public function loadReportActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:report", "view");
    }
    
    public function loadReportAction()
    {
        $type = $this->_getParam('type');
        if('care' == $type) {
            return $this->_loadCareReport();; 
        }
        else if('community' == $type) {
            return $this->_loadCommunityReport();; 
        }
        else {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Incorrect type of counseling")));
            return;
        }
    }
    
    
    private function _loadCareReport()
    {
        $id = $this->_getParam('id');
        $model = TrustCare_Model_ReportCare::find($id);
        if(is_null($model)) {
            $this->getLogger()->error(sprintf("'%s' tries to load unknown report_care.id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Report")));
            return;
        }

        $fileReportOutput = sprintf("%s/%s", TrustCare_SystemInterface_ReportGenerator_Abstract::reportsDirectory(), $model->getFilename());

        if(!file_exists($fileReportOutput)) {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Report file not found")));
            return;
        }

        $this->outputFileAsAttachment($fileReportOutput);
        return;
    }
    
    
    private function _loadCommunityReport()
    {
        $id = $this->_getParam('id');
        $model = TrustCare_Model_ReportCommunity::find($id);
        if(is_null($model)) {
            $this->getLogger()->error(sprintf("'%s' tries to load unknown report_community.id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Report")));
            return;
        }

        $fileReportOutput = sprintf("%s/%s", TrustCare_SystemInterface_ReportGenerator_Abstract::reportsDirectory(), $model->getFilename());

        if(!file_exists($fileReportOutput)) {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Report file not found")));
            return;
        }

        $this->outputFileAsAttachment($fileReportOutput);
        return;
    }
    
    public function deleteActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:report", "delete");
    }
    
    public function deleteAction()
    {
        $type = $this->_getParam('type');
        if('care' == $type) {
            return $this->_deleteCareReport(); 
        }
        else if('community' == $type) {
            return $this->_deleteCommunityReport(); 
        }
        else {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Incorrect type of counseling")));
            return;
        }
    }
    
    private function _deleteCareReport()
    {
        $id = $this->_getParam('id');
        $reportModel = TrustCare_Model_ReportCare::find($id);
        if(is_null($reportModel)) {
            $this->getLogger()->error(sprintf("'%s' tries to delete unknown report_care.id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Report")));
            return;
        }
        $fileName = $reportModel->getFilename();
        
        $reportModel->delete();
        TrustCare_SystemInterface_ReportGenerator_Abstract::removeReportFile($fileName);
        
        $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName(), null, array('type' => $this->_getParam('type')));
    }
    
    private function _deleteCommunityReport()
    {
        $id = $this->_getParam('id');
        $reportModel = TrustCare_Model_ReportCommunity::find($id);
        if(is_null($reportModel)) {
            $this->getLogger()->error(sprintf("'%s' tries to delete unknown report_community.id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Report")));
            return;
        }
        $fileName = $reportModel->getFilename();
        
        $reportModel->delete();
        TrustCare_SystemInterface_ReportGenerator_Abstract::removeReportFile($fileName);
        
        $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName(), null, array('type' => $this->_getParam('type')));
    }

    
    /**
     * @return Zend_Form
     */
    private function _getGenerateCareReportForm()
    {
        $pharmacyList = array();
        $pharmacyList[''] = '';
        $model = new TrustCare_Model_Pharmacy();
        foreach($model->fetchAll(array("is_active!=0"), 'name') as $obj) {
            $pharmacyList[$obj->getId()] = $obj->getName();
        }
        
        $periodList = array();
        for($i = 0; $i <= 11; $i++) {
            $time = gmmktime(0, 0, 0, gmdate("m") - $i, gmdate("d"), gmdate("Y"));
            $periodList[gmdate("Y-m", $time)] = gmdate("Y-m", $time);
        }
        
        $numberValidator = new Zend_Validate_Regex('/^\d+$/');
        $numberValidator->setMessage(Zend_Registry::get("Zend_Translate")->_("Necessary to enter positive value"));
        
        
        $form = new ZendX_Form();
        $form->setMethod('post');

        $form->addElement('hidden', 'id');
        
        $tabIndex = 1;
        $form->addElement('select', 'id_pharmacy', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Pharmacy"),
            'tabindex'      => $tabIndex++,
            'required'      => true,
            'multioptions'  => $pharmacyList,
        ));
        $form->addElement('select', 'period', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Period"),
            'tabindex'      => $tabIndex++,
            'required'      => true,
            'value'         => gmdate("Y-m", gmmktime(0, 0, 0, gmdate("m"), gmdate("d"), gmdate("Y"))),
            'multioptions'  => $periodList,
        ));
        $form->addElement('text', 'male_younger_15', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("The number of male (< 15 years) who received prescriptions"),
            'description'   => "",
            'tabindex'      => $tabIndex++,
            'size'			=> 5,
            'required'      => true,
            'validators'    => array($numberValidator)
        ));
        $form->addElement('text', 'female_younger_15', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("The number of female (< 15 years) who received prescriptions"),
            'description'   => "",
            'tabindex'      => $tabIndex++,
            'size'			=> 5,
        	'required'      => true,
            'validators'    => array($numberValidator)
        ));
        $form->addElement('text', 'male_from_15', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("The number of male (>= 15 years) who received prescriptions"),
            'description'   => "",
            'tabindex'      => $tabIndex++,
            'size'			=> 5,
        	'required'      => true,
            'validators'    => array($numberValidator)
        ));
        $form->addElement('text', 'female_from_15', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("The number of female (>= 15 years) who received prescriptions"),
            'description'   => "",
            'tabindex'      => $tabIndex++,
            'size'			=> 5,
        	'required'      => true,
            'validators'    => array($numberValidator)
        ));
        $form->addElement('text', 'drugs', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("The number of drugs dispensed in the reporting month"),
            'description'   => "",
            'tabindex'      => $tabIndex++,
            'size'			=> 5,
        	'required'      => true,
            'validators'    => array($numberValidator)
        ));
        
        
        $form->addElement('submit', 'send', array(
            'label'     => Zend_Registry::get("Zend_Translate")->_("Generate"),
            'tabindex'  => $tabIndex++,
        ));
        
        return $form;
    }
    
    /**
     * @return Zend_Form
     */
    private function _getGenerateCommunityReportForm()
    {
        $pharmacyList = array();
        $pharmacyList[''] = '';
        $model = new TrustCare_Model_Pharmacy();
        foreach($model->fetchAll(array("is_active!=0"), 'name') as $obj) {
            $pharmacyList[$obj->getId()] = $obj->getName();
        }
        
        $periodList = array();
        for($i = 0; $i <= 11; $i++) {
            $time = gmmktime(0, 0, 0, gmdate("m") - $i, gmdate("d"), gmdate("Y"));
            $periodList[gmdate("Y-m", $time)] = gmdate("Y-m", $time);
        }
        
        $form = new ZendX_Form();
        $form->setMethod('post');

        $form->addElement('hidden', 'id');
        
        $tabIndex = 1;
        $form->addElement('select', 'id_pharmacy', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Pharmacy"),
            'tabindex'      => $tabIndex++,
            'required'      => true,
            'multioptions'  => $pharmacyList,
        ));
        $form->addElement('select', 'period', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Period"),
            'tabindex'      => $tabIndex++,
            'required'      => true,
            'value'         => gmdate("Y-m", gmmktime(0, 0, 0, gmdate("m"), gmdate("d"), gmdate("Y"))),
            'multioptions'  => $periodList,
        ));
        
        
        $form->addElement('submit', 'send', array(
            'label'     => Zend_Registry::get("Zend_Translate")->_("Generate"),
            'tabindex'  => $tabIndex++,
        ));
        
        return $form;
    }
}

