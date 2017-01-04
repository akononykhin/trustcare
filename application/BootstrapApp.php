<?php
include_once 'BootstrapMain.php';

class BootstrapApp extends BootstrapMain
{
	protected function _initMain()
	{
        $this->bootstrap('autoload');
		$frontController = Zend_Controller_Front::getInstance();
		$frontController->registerPlugin(new TrustCare_Controller_Plugin_Init());
	}
	
	
    protected function _initHelpers()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->addHelperPath("ZendX/View/Helper", "ZendX_View_Helper");
        $view->addHelperPath("ZendX/View/Helper/Navigation", "ZendX_View_Helper_Navigation");
    }
    
    protected function _initAppRegistry()
    {
        $this->bootstrap('autoload');
        $this->bootstrap('db');
        $this->bootstrap('logging');
        
        Zend_Registry::set("TrustCare_Registry_User", new TrustCare_Registry_User());
    }
    
}

