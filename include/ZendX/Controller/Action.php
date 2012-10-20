<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */

class ZendX_Controller_Action extends Zend_Controller_Action
{
    /**
     * @var Logger
     */
    protected $_logger = null;
    
    /**
     * @var Zend_Controller_Action_Helper_Redirector
     */
    protected $_redirector;
    
    /**
     * @var Zend_Session_Namespace
     */
    protected $_controllerSession;
    
    public function init()
    {
        $this->_redirector = $this->getHelper("Redirector");
        $this->_controllerSession = new Zend_Session_Namespace(get_class($this));
    }
    
    public function preDispatch()
    {
        if(!$this->isAuthenticated()) {
            $this->getHelper("Redirector")->gotoSimpleAndExit("index", "sign");
        }
        if(!$this->isAuthorized()) {
            $this->_forward("message", "error", null, array('message' => Zend_Registry::get("Zend_Translate")->_("Access Denied")));
        }
    }
    
    
    /**
     * Check either the user is authenticated to access the controller or not.
     * If some controller has to be accessed by any user it's necessary to overwrite this method
     *
     * @return bool
     */
    public function isAuthenticated()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            return true;
        }
        else {
            return false;
        }
    }
    
    /**
     * Check either the current request (to the specific action) is authorized or not.
     *
     * @return unknown
     */
    public function isAuthorized() {
        $controllerName = $this->getRequest()->getControllerName();
        $actionName = $this->getRequest()->getActionName();
        
        $actionAccessMethodName = $actionName . "ActionAccess";
        if(method_exists($this, $actionAccessMethodName)) {
            return $this->$actionAccessMethodName();
        }
        
        return true;
    }
    
    /**
     * @return Logger
     */
    public function getLogger()
    {
    	if(is_null($this->_logger)) {
    		$this->_logger = LoggerManager::getLogger(get_class($this));
    	}
        return $this->_logger;
    }
    
    /**
     * 
     * @return Zend_Controller_Action_Helper_Redirector
     */
    public function getRedirector()
    {
        return $this->_redirector;
    }
    
    protected function outputResponseAsAttachment($content, $contentType, $filename)
    {
        $userAgent = $this->getRequest()->getServer('HTTP_USER_AGENT');
        $browser_agent = "";
        if (ereg('OPERA(/| )([0-9].[0-9]{1,2})', strtoupper($userAgent))) {
            $browser_agent = 'OPERA';
        }
        else if (ereg('MSIE ([0-9].[0-9]{1,2})',strtoupper($userAgent))) {
            $browser_agent = 'IE';
        }
        else if (ereg('OMNIWEB/([0-9].[0-9]{1,2})', strtoupper($userAgent))) {
            $browser_agent = 'OMNIWEB';
        }
        else if (ereg('MOZILLA/([0-9].[0-9]{1,2})', strtoupper($userAgent))) {
            $browser_agent = 'MOZILLA';
        }
        else if (ereg('KONQUEROR/([0-9].[0-9]{1,2})', strtoupper($userAgent))) {
            $browser_agent = 'KONQUEROR';
        }
        else {
            $browser_agent = 'OTHER';
        }

        $length = strlen($content);
        $now = gmdate('D, d M Y H:i:s') . ' GMT';

        require_once 'Zend/Controller/Action/HelperBroker.php';
        Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender(true);

        require_once 'Zend/Layout.php';
        $layout = Zend_Layout::getMvcInstance();
        if ($layout instanceof Zend_Layout) {
            $layout->disableLayout();
        }
        
        $this->getResponse()->setHeader('Content-Type', $contentType);
        $this->getResponse()->setHeader('Expires', $now);
        $this->getResponse()->setHeader('Content-Length', $length);
        $this->getResponse()->setHeader('Content-Disposition', sprintf('attachment; filename="%s"', $filename));
        
        if ($browser_agent == 'IE') {
            //$this->getResponse()->setHeader('Content-Disposition', sprintf('inline; filename="%s"', $filename));
            $this->getResponse()->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
            $this->getResponse()->setHeader('Pragma', 'public');
        }
        else {
            $this->getResponse()->setHeader('Pragma', 'no-cache');
        }
        
        echo $content;
        return;
    }
    
    /**
     * 
     * @param string $sessionPostfix
     * @param array $filterElements Definition of filter elements
     * @return array
     */
    protected function prepareFilterValues($sessionPostfix, $filterElements) {
        $filterSession = new Zend_Session_Namespace(get_class($this) . "_filter_" . $sessionPostfix);

        $filterValues = array();
        foreach($filterElements as $filterElement) {
            $name = $filterElement['name'];
            $default = array_key_exists('default', $filterElement) ? $filterElement['default'] : '';
            $filterName = 'filter'.$name;

            $param = $this->getRequest()->getParam($filterName);
            if (!is_null($param)) {
                $filterSession->$name =  $param;
            }
            $filterValues[$name] = isset($filterSession->$name) ? $filterSession->$name : $default;
            $filterSession->$name = $filterValues[$name];
        }
        
        return $filterValues;
    }
    
    /**
     * Processing of standard list-load ajax query.
     * Zend_Db_Select $select object has already been initiated outside. It's necessary to configure WHERE,ORDER,LIMIT parts
     * Enter description here ...
     * @param Zend_Db_Select $select
     */
    protected function processListLoadAjaxRequest(Zend_Db_Select $select, $realColumnNames = array())
    {
        /* Paging */
        if(array_key_exists('iDisplayStart', $_REQUEST) && $_REQUEST['iDisplayLength'] != '-1' ) {
            $select->limit($_REQUEST['iDisplayLength'], $_REQUEST['iDisplayStart']);
        }

        /*
         * Ordering
         */
        $orderByArr = array();
        if($_REQUEST['iSortingCols']) {
            for($i = 0; $i < $_REQUEST['iSortingCols']; $i++) {
                $columnIndex = $_REQUEST['iSortCol_'.$i];
                $sortDirection = strtoupper($_REQUEST['sSortDir_'.$i]) == 'ASC' ? 'asc' : 'desc';

                $colName = $_REQUEST['mDataProp_'.$columnIndex];
                if(array_key_exists($colName, $realColumnNames)) {
                    $colName = $realColumnNames[$colName];
                }
                
                $orderByArr[] = sprintf("%s %s", $colName, $sortDirection);
            }
        }
        if(count($orderByArr)) {
                $select->order($orderByArr);
        }

        if($_REQUEST['iColumns']) {
            for($i = 0; $i < $_REQUEST['iColumns']; $i++) {
                if(!array_key_exists('mDataProp_'.$i, $_REQUEST)) {
                    continue;
                }
                if(!array_key_exists('bSearchable_'.$i, $_REQUEST) || !$_REQUEST['bSearchable_'.$i]) {
                    continue;
                }
                if(!array_key_exists('sSearch_'.$i, $_REQUEST) || empty($_REQUEST['sSearch_'.$i])) {
                    continue;
                }
                $colName = $_REQUEST['mDataProp_'.$i];
                if(array_key_exists($colName, $realColumnNames)) {
                    $colName = $realColumnNames[$colName];
                }
                $value = $_REQUEST['sSearch_'.$i];

                
                $notLike = false;
                if(preg_match("/^!/", $value)) {
                    $value =  preg_replace("/^!/", "", $value);
                    $notLike = true;
                    
                    $operation = preg_match("/%/", $value) ? 'not like' : '!=';
                }
                else {
                    $operation = preg_match("/%/", $value) ? 'like' : '=';
                }

                /* Array search */
                if(preg_match("/;/", $value)) {
                    $dda = array();
                    foreach(preg_split("/;/", $value) as $k=>$v) {
                        $dda[] = sprintf("%s %s %s",
                                    Zend_Registry::getInstance()->dbAdapter->quoteIdentifier($colName),
                                    $operation,
                                    Zend_Registry::getInstance()->dbAdapter->quote($v));
                    }
                    $whereStr = !$notLike ? implode(" OR ",$dda) : implode(" AND ",$dda);
                }
                else {
                    $whereStr = sprintf("%s %s %s",
                                    Zend_Registry::getInstance()->dbAdapter->quoteIdentifier($colName),
                                    $operation,
                                    Zend_Registry::getInstance()->dbAdapter->quote($value));
                }
                $select->where($whereStr);
            }
        }
    }

    /**
     * 
     * @param string $time Time at GMT timezone and YYYY-MM-DD HH:MI:SS format
     */
    protected function convertTimeToUserTimezone($time)
    {
            $zendDate  = new Zend_Date($time.'Z', "yyyy-MM-dd HH:mm:ssZ");
            $clientTZ = Zend_Registry::getInstance()->clientTimeZone;
            $zendDate->setTimezone($clientTZ);
            return $zendDate->toString(Zend_Registry::getInstance()->dateTimeFormat);
    }
}

