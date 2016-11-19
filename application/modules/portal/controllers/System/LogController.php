<?php

class Portal_System_LogController extends ZendX_Controller_Action
{

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $this->getRedirector()->gotoSimpleAndExit("access", $this->getRequest()->getControllerName());
    }

    
    public function errorsActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.system_log", 'view');
    }
    
    public function errorsAction()
    {
        $columnsInfo = array(
            'timestamp' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Date"),
            ),
            'level' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Level"),
                'filter' => array(
                    'type' => 'text',
                ),
            ),
            'message' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Message"),
                'filter' => array(
                    'type' => 'text',
                ),
            ),
        );

        $this->view->DataTable = array(
            'serverUrl' => $this->view->url(array('action' => 'errors-load')),
            'defSortColumn' => 1,
            'defSortDir' => 'desc',
            'columnsInfo' => $columnsInfo,
            'bActionsColumn' => true
        );
        $this->render('list', null, true);
        return;
    }
    
    
    public function errorsLoadActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.system_log", 'view');
    }
    
    public function errorsLoadAction()
    {
        $select = Zend_Registry::getInstance()->dbAdapter->select()->from("log4php", array('count(id)'));
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_NUM);
        $result = Zend_Registry::getInstance()->dbAdapter->fetchAll($select);
        $iTotal = $result[0][0];
        
        
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_ASSOC);
        $select = Zend_Registry::getInstance()->dbAdapter->select()->from("log4php", array('id',
                                                                                           'timestamp' => new Zend_Db_Expr("date_format(timestamp, '%Y-%m-%d %H:%i:%s')"),
                                                                                           'logger',
                                                                                           'level',
                                                                                           'message',
                                                                                           'thread',
                                                                                           'file'));

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
            $row['timestamp'] = $this->convertTimeToUserTimezone($row['timestamp']);
            $row['message'] = preg_replace("/\t/", "&nbsp;&nbsp;&nbsp;&nbsp;", nl2br($row['message']));
            
            $row['_row_actions_'] = array(
                array(
                    'title' => Zend_Registry::get("Zend_Translate")->_("View"),
                    'url' => $this->view->url(array('action' => 'error-view', 'id' => $row['id'])),
                    'type' => 'view'
                ),
            );
            $output['aaData'][] = $row;
        }

                                              
        $this->_helper->json($output);
    }
    
    
    public function errorViewActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.system_log", 'view');
    }
    
    
    public function errorViewAction()
    {
        $id = $this->_getParam('id');
        
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_ASSOC);
        $select = Zend_Registry::getInstance()->dbAdapter->select()->from("log4php", array('id',
                                                                                           'timestamp' => new Zend_Db_Expr("date_format(timestamp, '%Y-%m-%d %H:%i:%s')"),
                                                                                           'logger',
                                                                                           'level',
                                                                                           'message',
                                                                                           'thread',
                                                                                           'file'));
        $select->where('id=?', $id);
        $result = Zend_Registry::getInstance()->dbAdapter->fetchAll($select);
        if (0 == count($result)) {
            $this->getLogger()->error(sprintf("'%s' tries to view unknown log4php entity with id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Record")));
            return;
        }
        $row = $result[0];

        $row['timestamp'] = $this->convertTimeToUserTimezone($row['timestamp']);
        $this->view->info = $row;
    }

    public function objectsActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.system_log", 'view');
    }
    
    
    public function objectsAction()
    {
        $columnsInfo = array(
            'timestamp' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Date"),
                'width' => '15%',
            ),
            'author' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Author"),
                'width' => '10%',
            ),
            'object_name' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Object"),
                'width' => '10%',
                'filter' => array(
                    'type' => 'text',
                ),
            ),
            'key_info' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Key"),
                'width' => '10%',
                'filter' => array(
                    'type' => 'text',
                ),
            ),
            'action' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Action"),
                'filter' => array(
                    'type' => 'text',
                ),
            ),
        );

        $this->view->DataTable = array(
            'serverUrl' => $this->view->url(array('action' => 'objects-load')),
            'defSortColumn' => 1,
            'defSortDir' => 'desc',
            'columnsInfo' => $columnsInfo,
            'bActionsColumn' => true
        );
        $this->render('list', null, true);
        return;
    }
    
    public function objectsLoadActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.system_log", 'view');
    }
    
    public function objectsLoadAction()
    {
        $select = Zend_Registry::getInstance()->dbAdapter->select()->from("log_objects", array('count(id)'));
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_NUM);
        $result = Zend_Registry::getInstance()->dbAdapter->fetchAll($select);
        $iTotal = $result[0][0];
        
        
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_ASSOC);
        $select = Zend_Registry::getInstance()->dbAdapter->select()->from("log_objects", array('id',
                                                                                           'timestamp' => new Zend_Db_Expr("date_format(timestamp, '%Y-%m-%d %H:%i:%s')"),
                                                                                           'author',
                                                                                           'object_name',
                                                                                           'key_info',
                                                                                           'action'));

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
            $row['timestamp'] = $this->convertTimeToUserTimezone($row['timestamp']);
            $row['action'] = preg_replace("/\t/", "&nbsp;&nbsp;&nbsp;&nbsp;", nl2br($row['action']));
            
            $row['_row_actions_'] = array(
                array(
                    'title' => Zend_Registry::get("Zend_Translate")->_("View"),
                    'url' => $this->view->url(array('action' => 'object-view', 'id' => $row['id'])),
                    'type' => 'view'
                ),
            );
            $output['aaData'][] = $row;
        }

                                              
        $this->_helper->json($output);
    }
    
    public function objectViewActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.system_log", 'view');
    }
    
    public function objectViewAction()
    {
        $id = $this->_getParam('id');
        $model = TrustCare_Model_LogObjects::find($id);
        if(is_null($model)) {
            $this->getLogger()->error(sprintf("'%s' tries to view unknown log_objects entity with id='%s'", Zend_Auth::getInstance()->getIdentity(), $id));
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Unknown Record")));
            return;
        }
        
        $model->timestamp = $this->convertTimeToUserTimezone($model->timestamp);
        $this->view->model = $model;
    }
    
    public function accessActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.system_log", 'view');
    }
    
    
    public function accessAction()
    {
        $columnsInfo = array(
            'time' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Date"),
                'width' => '15%',
            ),
            'author' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("User"),
                'filter' => array(
                    'type' => 'text',
                ),
            ),
            'ip' => array(
                'title' => 'IP',
                'filter' => array(
                    'type' => 'text',
                ),
            ),
            'action' => array(
                'title' => Zend_Registry::get("Zend_Translate")->_("Action"),
            ),
        );

        $this->view->DataTable = array(
            'serverUrl' => $this->view->url(array('action' => 'access-load')),
            'defSortColumn' => 0,
            'defSortDir' => 'desc',
            'columnsInfo' => $columnsInfo,
            'bActionsColumn' => false
        );
        $this->render('list', null, true);
        return;
    }
    
    public function accessLoadActionAccess()
    {
       return Zend_Registry::get("Zend_Acl")->isAllowed(Zend_Registry::get("TrustCare_Registry_User")->getUser()->role, "resource:admin.system_log", 'view');
    }
    
    public function accessLoadAction()
    {
        $select = Zend_Registry::getInstance()->dbAdapter->select()->from("log_access", array('count(id)'));
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_NUM);
        $result = Zend_Registry::getInstance()->dbAdapter->fetchAll($select);
        $iTotal = $result[0][0];
        
        
        Zend_Registry::getInstance()->dbAdapter->setFetchMode(Zend_Db::FETCH_ASSOC);
        $select = Zend_Registry::getInstance()->dbAdapter->select()->from("log_access", array('id',
                                                                                           'time' => new Zend_Db_Expr("date_format(time, '%Y-%m-%d %H:%i:%s')"),
                                                                                           'author',
                                                                                           'ip',
                                                                                           'action'));

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
            $row['time'] = $this->convertTimeToUserTimezone($row['time']);
            $row['action'] = preg_replace("/\t/", "&nbsp;&nbsp;&nbsp;&nbsp;", nl2br($row['action']));
            
            $output['aaData'][] = $row;
        }

                                              
        $this->_helper->json($output);
    }

    
    
}

