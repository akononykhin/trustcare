<?php
/**
 * 
 * Alexey Kononykhin
 * akononyhin@list.ru
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

}

