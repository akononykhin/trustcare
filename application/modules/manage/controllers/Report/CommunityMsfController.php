<?php

class Report_CommunityMsfController extends ZendX_Controller_Action
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
            'toolbar' => array(
                array(
                    'text' => Zend_Registry::get("Zend_Translate")->_("Generate"),
                    'url' => $this->view->url(array('action' => 'generate'))
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
        $select = Zend_Registry::getInstance()->dbAdapter->select()->from('report_community', array('count(id)'));
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_NUM);
        $result = Zend_Registry::getInstance()->dbAdapter->fetchAll($select);
        $iTotal = $result[0][0];


        $pharmacyIds = array_keys(Zend_Registry::get("TrustCare_Registry_User")->getListOfAvailablePharmacies());
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_ASSOC);
        $select = Zend_Registry::getInstance()->dbAdapter->select()
        ->from('report_community',
            array(
                'report_community.id',
                'generation_date' => new Zend_Db_Expr("date_format(report_community.generation_date, '%Y-%m-%d %H:%i:%s')"),
                'report_community.period'
            ))
            ->joinLeft(array('user'), 'report_community.id_user = user.id', array('user_login' => 'user.login'))
            ->joinLeft(array('pharmacy'), 'report_community.id_pharmacy = pharmacy.id', array('pharmacy_name' => 'pharmacy.name'))
            ->where(sprintf("report_community.id_pharmacy in (%s)", join(",", $pharmacyIds)));
        
        $this->processListLoadAjaxRequest($select, array(
            'user_login' => 'user.login',
            'pharmacy_name' => 'pharmacy.name'
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
            $row['generation_date'] = $this->convertTimeToUserTimezone($row['generation_date']);
            $period = $row['period'];
            if(preg_match("/^(\d{4})(\d{2})$/", $period, $matches)) {
                $row['period'] = $matches[1].'-'.$matches[2];
            }

                        
            $row['_row_actions_'] = array(
                array(
                    'title' => Zend_Registry::get("Zend_Translate")->_("View"),
                    'url' => $this->view->url(array('action' => 'view', 'id' => $row['id'])),
                    'type' => 'view'
                ),
                array(
                    'title' => Zend_Registry::get("Zend_Translate")->_("Delete"),
                    'url' => $this->view->url(array('action' => 'delete', 'id' => $row['id'])),
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
        $form = $this->_getGenerateReportForm();
        $form->setAction($this->getRequest()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . "/generate");
        
        if($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
                try {
                    $idPharmacy = $form->getValue('id_pharmacy');
                    if(!array_key_exists($idPharmacy, Zend_Registry::get("TrustCare_Registry_User")->getListOfAvailablePharmacies())) {
                        $errorMsg = Zend_Registry::get("Zend_Translate")->_("Access Denied");
                        throw new Exception('');
                    }
                    
                    $period = $form->getValue('period');
                    $format = $form->getValue('format');
                    
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
                                'month_index' => sprintf("%04s%02s", $year, $month),
                    ), $format);
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
        $id = $this->_getParam('id');
        $reportModel = TrustCare_Model_ReportCommunity::find($id);
        if(is_null($reportModel)) {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Report")));
            return;
        }
        
        $availablePharmacies = Zend_Registry::get("TrustCare_Registry_User")->getListOfAvailablePharmacies();
        if(!array_key_exists($reportModel->getIdPharmacy(), $availablePharmacies)) {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Access Denied")));
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
        
        $this->view->id = $this->_getParam('id');
        $this->view->reportModel = $reportModel;
        $this->view->pharmacyName = $pharmacyModel->getName();
        $this->view->authorName = $authorModel->getLogin();
        $this->render('view');
        return;
    }
    
    public function loadReportActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:report", "view");
    }
    
    public function loadReportAction()
    {
        $id = $this->_getParam('id');
        $model = TrustCare_Model_ReportCommunity::find($id);
        if(is_null($model)) {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Report")));
            return;
        }
        
        $availablePharmacies = Zend_Registry::get("TrustCare_Registry_User")->getListOfAvailablePharmacies();
        if(!array_key_exists($model->getIdPharmacy(), $availablePharmacies)) {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Access Denied")));
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
        $id = $this->_getParam('id');
        $reportModel = TrustCare_Model_ReportCommunity::find($id);
        if(is_null($reportModel)) {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Report")));
            return;
        }
        
        $availablePharmacies = Zend_Registry::get("TrustCare_Registry_User")->getListOfAvailablePharmacies();
        if(!array_key_exists($reportModel->getIdPharmacy(), $availablePharmacies)) {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Access Denied")));
            return;
        }
        
        $fileName = $reportModel->getFilename();
        
        $reportModel->delete();
        TrustCare_SystemInterface_ReportGenerator_Abstract::removeReportFile($fileName);
        
        $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName());
    }
    
    /**
     * @return Zend_Form
     */
    private function _getGenerateReportForm()
    {
        $pharmacyList = array('' => '') + Zend_Registry::get("TrustCare_Registry_User")->getListOfAvailablePharmacies();
                
        $periodList = array();
        for($i = 0; $i <= 11; $i++) {
            $time = gmmktime(0, 0, 0, gmdate("m") - $i, gmdate("d"), gmdate("Y"));
            $periodList[gmdate("Y-m", $time)] = gmdate("Y-m", $time);
        }
        
        $formatList = array(
            'PDF' => 'PDF',
            'HTML' => 'HTML',
            'XLS' => 'Excel',
        );
        
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
        $form->addElement('select', 'format', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Format"),
            'tabindex'      => $tabIndex++,
            'required'      => true,
            'value'         => 'PDF',
            'multioptions'  => $formatList,
        ));
        
        
        $form->addElement('submit', 'send', array(
            'label'     => Zend_Registry::get("Zend_Translate")->_("Generate"),
            'tabindex'  => $tabIndex++,
        ));
        
        return $form;
    }
}

