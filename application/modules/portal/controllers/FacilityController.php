<?php

class Portal_FacilityController extends ZendX_Controller_Action
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
        $facilityTypeList = array();
        $facilityTypeModel = new TrustCare_Model_FacilityType();
        foreach($facilityTypeModel->fetchAll(array(), "name") as $obj) {
            $facilityTypeList[$obj->getId()] = $obj->getName();
        }

        $facilityLevelList = array();
        $facilityLevelModel = new TrustCare_Model_FacilityLevel();
        foreach($facilityLevelModel->fetchAll(array(), "name") as $obj) {
            $facilityLevelList[$obj->getId()] = $obj->getName();
        }
        
        $columnsInfo = array(
            'name' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Name"),
                'filter' => array(
                    'type' => 'text',
                ),
            ),
            'lga_name' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("LGA"),
                'filter' => array(
                    'type' => 'select',
                    'values' => $this->getLgaList(),
                    'use_keys' => true,
                ),
            ),
            'facility_type_name' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Type"),
                'filter' => array(
                    'type' => 'select',
                    'values' => $facilityTypeList,
                    'use_keys' => true,
                ),
            ),
            'facility_level_name' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Level"),
                'filter' => array(
                    'type' => 'select',
                    'values' => $facilityLevelList,
                    'use_keys' => true,
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
                array(
                    'text' => Zend_Registry::get("Zend_Translate")->_("Import"),
                    'url' => $this->view->url(array('action' => 'import'))
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
        $select = Zend_Registry::getInstance()->dbAdapter->select()->from("facility", array('count(id)'));
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_NUM);
        $result = Zend_Registry::getInstance()->dbAdapter->fetchAll($select);
        $iTotal = $result[0][0];
        
        
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_ASSOC);
        $select = Zend_Registry::getInstance()->dbAdapter->select()
                                                         ->from("facility", array('facility.*'))
                                                         ->joinLeft(array('lga'), 'facility.id_lga = lga.id', array('lga_name' => 'lga.name'))
                                                         ->joinLeft(array('facility_type'), 'facility.id_facility_type = facility_type.id', array('facility_type_name' => 'facility_type.name'))
                                                         ->joinLeft(array('facility_level'), 'facility.id_facility_level = facility_level.id', array('facility_level_name' => 'facility_level.name'));

        $this->processListLoadAjaxRequest($select, array('name' => 'facility.name',
                                                         'lga_name' => 'lga.id',
                                                         'facility_type_name' => 'facility_type.id',
                                                         'facility_level_name' => 'facility_level.id'));
        
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
        $form->setAction($this->getRequest()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . "/create");
        if($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
                try {
                    $name = $form->getValue('name');
                    
                    $model = new TrustCare_Model_Facility();
                    $model->setName($name);
                    $model->setSn($form->getValue('sn'));
                    $model->setIdLga($form->getValue('id_lga'));
                    $model->setIdFacilityType($form->getValue('id_facility_type'));
                    $model->setIdFacilityLevel($form->getValue('id_facility_level'));
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
        $form->setAction($this->getRequest()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . "/edit");
        
        $id = $this->_getParam('id');
        $model = TrustCare_Model_Facility::find($id);
        if(is_null($model)) {
            $this->getLogger()->error(sprintf("'%s' tries to edit unknown facility with id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Facility")));
            return;
        }
        
        if($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
                try {
                    $name = $form->getValue('name');
                    
                    $model->setName($name);
                    $model->setSn($form->getValue('sn'));
                    $model->setIdLga($form->getValue('id_lga'));
                    $model->setIdFacilityType($form->getValue('id_facility_type'));
                    $model->setIdFacilityLevel($form->getValue('id_facility_level'));
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
            $form->getElement("sn")->setValue($model->sn);
            $form->getElement("id_lga")->setValue($model->id_lga);
            $form->getElement("id_facility_type")->setValue($model->id_facility_type);
            $form->getElement("id_facility_level")->setValue($model->id_facility_level);
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
        $model = TrustCare_Model_Facility::find($id);
        if(is_null($model)) {
            $this->getLogger()->error(sprintf("'%s' tries to delete unknown facility with id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Facility")));
            return;
        }

        $model->delete();
        
        $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName());
    }
    
    
    public function importActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.system_dict", "create");
    }
    
    public function importAction()
    {
        $form = $this->_getImportForm();
        $form->setAction($this->getRequest()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . "/import");
        if($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                if(!$form->import_file->receive()) {
                    $form->addError(Zend_Registry::get("Zend_Translate")->_("Upload Error"));
                }
                else {
                    $id_country = $form->getValue("id_country");
                    $states = array();
                    $lgas = array();
                    $types = array();
                    $levels = array();
                    
                    
                    $fileLocation = $form->import_file->getFileName();
                    $separator = $form->getValue("separator");
                    
                    ini_set('auto_detect_line_endings', true);
                    ini_set('memory_limit', "512M");
                    
                    $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
                    try {
                        $fileHandle = fopen($fileLocation,  "r");
                        if (!$fileHandle) {
                            $errorMsg = Zend_Registry::get("Zend_Translate")->_("Can't open uploaded CSV file");
                            throw new Exception("");
                        }

                        $failedRows = array();
                        $warningRows = array();

                        while(($row = fgetcsv($fileHandle, 0, $separator))) {

                            try {
                                if(6 > count($row)) {
                                    throw new Exception(Zend_Registry::get("Zend_Translate")->_("Incorrect number of columns"));
                                }
                                
                                if('#' == $row[0]{0}) {
                                    continue;
                                }
                                
                                $sn = trim($row[0]);
                                $stateName = trim($row[1]);
                                $lgaName = trim($row[2]);
                                $facilityName = trim($row[3]);
                                $type = trim($row[4]);
                                $level = trim($row[5]);

                                /* check State */
                                if(empty($stateName)) {
                                    throw new Exception(sprintf("Empty state name isn't allowed."));
                                }
                                if(array_key_exists($stateName, $states)) {
                                    $id_state = $states[$stateName];
                                }
                                else {
                                    $stateObj = TrustCare_Model_State::findByName($stateName);
                                    if(is_null($stateObj)) {
                                        $stateObj = new TrustCare_Model_State(array(
                                                'name' => $stateName,
                                                'id_country' => $id_country,
                                                'mapperOptions' => array('adapter' => $this->db),
                                        ));
                                        $stateObj->save();
                                    }
                                    $id_state = $stateObj->getId();
                                    $states[$stateName] = $id_state;
                                }

                                /* check LGA */
                                if(empty($lgaName)) {
                                    throw new Exception(sprintf("Empty lga name isn't allowed."));
                                }
                                if(array_key_exists($lgaName, $lgas)) {
                                    $id_lga = $lgas[$lgaName];
                                }
                                else {
                                    $lgaObj = TrustCare_Model_Lga::findByName($lgaName);
                                    if(is_null($lgaObj)) {
                                        $lgaObj = new TrustCare_Model_Lga(array(
                                                'name' => $lgaName,
                                                'id_state' => $id_state,
                                                'mapperOptions' => array('adapter' => $this->db),
                                        ));
                                        $lgaObj->save();
                                    }
                                    $id_lga = $lgaObj->getId();
                                    $lgas[$lgaName] = $id_lga;
                                }
                                
                                /* check Type */
                                $id_facility_type = null;
                                if(!empty($type)) {
                                    if(array_key_exists($type, $types)) {
                                        $id_facility_type = $types[$type];
                                    }
                                    else {
                                        $typeObj = TrustCare_Model_FacilityType::findByName($type);
                                        if(is_null($typeObj)) {
                                            $typeObj = new TrustCare_Model_FacilityType(array(
                                                    'name' => $type,
                                                    'mapperOptions' => array('adapter' => $this->db),
                                            ));
                                            $typeObj->save();
                                        }
                                        $id_facility_type = $typeObj->getId();
                                        $types[$type] = $id_facility_type;
                                    }
                                }
                                
                                /* check Level */
                                $id_facility_level = null;
                                if(!empty($level)) {
                                    if(array_key_exists($level, $levels)) {
                                        $id_facility_level = $levels[$level];
                                    }
                                    else {
                                        $levelObj = TrustCare_Model_FacilityLevel::findByName($level);
                                        if(is_null($levelObj)) {
                                            $levelObj = new TrustCare_Model_FacilityLevel(array(
                                                    'name' => $level,
                                                    'mapperOptions' => array('adapter' => $this->db),
                                            ));
                                            $levelObj->save();
                                        }
                                        $id_facility_level = $levelObj->getId();
                                        $levels[$level] = $id_facility_level;
                                    }
                                }
                                
                                if(empty($facilityName)) {
                                    throw new Exception(sprintf("Empty facility name isn't allowed."));
                                }
                                
                                $model = new TrustCare_Model_Facility(array(
                                    'name' => $facilityName,
                                    'sn' => $sn,
                                    'id_lga' => $id_lga,
                                    'id_facility_type' => $id_facility_type, 
                                    'id_facility_level' => $id_facility_level, 
                                    'mapperOptions' => array('adapter' => $this->db),
                                ));
                                $model->save();
                            }
                            catch(Exception $ex) {
                                $failedRows[] = sprintf("Row: %s (%s)", join($separator, $row), $ex->getMessage());
                            }
                        }
                        fclose($fileHandle);

                        if(count($failedRows) || count($warningRows)) {
                            $this->view->failedRows = $failedRows;
                            $this->view->warningRows = $warningRows;
                            $this->render('import_failed_rows', null, true);
                            return;
                        }
                        else {
                            $this->getRedirector()->gotoSimpleAndExit('list', $this->getRequest()->getControllerName());
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
        }
        $this->view->form = $form;
        $this->render('import');   
        
    }
    
    
    
    /**
     * @return Zend_Form
     */
    private function _getParametersForm()
    {
        $facilityTypeList = array();
        $facilityTypeList[] = '';
        $facilityTypeModel = new TrustCare_Model_FacilityType();
        foreach($facilityTypeModel->fetchAll(array(), "name") as $obj) {
            $facilityTypeList[$obj->getId()] = $obj->getName();
        }

        $facilityLevelList = array();
        $facilityLevelList[] = '';
        $facilityLevelModel = new TrustCare_Model_FacilityLevel();
        foreach($facilityLevelModel->fetchAll(array(), "name") as $obj) {
            $facilityLevelList[$obj->getId()] = $obj->getName();
        }

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
        $form->addElement('text', 'sn', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("S/N"),
                'description'   => "",
                'size'          => 16,
                'tabindex'      => $tabIndex++,
                'required'      => false
        ));
        
        $form->addElement('select', 'id_lga', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("LGA"),
                'required'      => true,
                'multioptions'  => array('' => '') + $this->getLgaList(),
                'tabindex'      => $tabIndex++,
                'description'   => '',
        ));
        
        $form->addElement('select', 'id_facility_type', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Type"),
                'required'      => false,
                'multioptions'  => $facilityTypeList,
                'tabindex'      => $tabIndex++,
                'description'   => '',
        ));
        
        $form->addElement('select', 'id_facility_level', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Level"),
                'required'      => false,
                'multioptions'  => $facilityLevelList,
                'tabindex'      => $tabIndex++,
                'description'   => '',
        ));
        
        
        $form->addElement('submit', 'send', array(
            'label'     => Zend_Registry::get("Zend_Translate")->_("Save"),
            'tabindex'  => $tabIndex++,
        ));
        
        return $form;
    }
    
    
    /**
     * @return Zend_Form
     */
    private function _getImportForm()
    {
        $form = new ZendX_Form();
        $form->setMethod('post');
        $form->setAttrib('enctype', 'multipart/form-data');

        $tabIndex = 1;
        
        $form->addElement('text', 'separator', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Separator"),
            'required'      => true,
            'value'         => ',',
            'tabindex'      => $tabIndex++,
        ));
        
        $maxFileSizeInMBytes = 5;
        $fileElement = $form->createElement('file', 'import_file', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("File"),
            'required'      => true,
            'tabindex'      => $tabIndex++,
        ));
        $fileElement->setMaxFileSize($maxFileSizeInMBytes * 1024 * 1024)
                    ->setDescription(Zend_Registry::get("Zend_Translate")->_("The maximum file size to import : ") . ($fileElement->getMaxFileSize() / (1024*1024)) . "Mb")
                    ->addValidator('Count', false, 1)
                    ->addValidator('Size', false, $maxFileSizeInMBytes * 1024 * 1024);
        $form->addElement($fileElement);

        
        $countryModel = new TrustCare_Model_Country();
        $objs = $countryModel->fetchAll(array(), "name");
        $countryList = array();
        $countryList[''] = '';
        foreach($objs as $obj) {
            $countryList[$obj->getId()] = $obj->getName();
        }
        $form->addElement('select', 'id_country', array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Country"),
                'description'   => Zend_Registry::get("Zend_Translate")->_("Loading facilities are placed in this country"),
                'required'      => true,
                'multioptions'  => $countryList,
                'tabindex'      => $tabIndex++,
        ));
        
        $form->addElement('submit', 'send', array(
            'label'     => Zend_Registry::get("Zend_Translate")->_("Import"),
            'tabindex'  => $tabIndex++,
        ));
        
        return $form;
    }
    
    private function getLgaList()
    {
        $countries = array();
        $states = array();
        $lgaList = array();
        $lgaModel = new TrustCare_Model_Lga();
        foreach ($lgaModel->fetchAll() as $obj) {
            $stateName = '';
            $countryName = '';
        
            $stateId = $obj->getIdState();
            if(!array_key_exists($stateId, $states)) {
                $stateObj = TrustCare_Model_State::find($stateId);
                if(!is_null($stateObj)) {
                    $states[$stateId] = $stateObj->getName();
        
                    $countryId = $stateObj->getIdCountry();
                    if(!array_key_exists($countryId, $countries)) {
                        $countryObj = TrustCare_Model_Country::find($countryId);
                        if(!is_null($countryObj)) {
                            $countries[$countryId] = $countryObj->getName();
                        }
                    }
                }
            }
            $stateName = array_key_exists($stateId, $states) ? $states[$stateId] : '';
            $countryName = array_key_exists($countryId, $countries) ? $countries[$countryId] : '';
        
            $lgaList[$obj->getId()] = sprintf("%s/%s/%s", $countryName, $stateName, $obj->getName());
        }
        asort($lgaList);

        return $lgaList;
    }
}

