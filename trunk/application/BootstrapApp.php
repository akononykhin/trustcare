<?php
include_once 'BootstrapMain.php';

class BootstrapApp extends BootstrapMain
{
    protected function _initSession()
    {
        Zend_Session::start();
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('TrustCare_Manage_Zend_Auth'));
    }
        
    protected function _initLocale()
    {
        if (!empty($_GET['lang'])) {
            $lang = $_GET['lang'];
        }
        else if (!empty($_COOKIE['lang'])) {
            $lang = $_COOKIE['lang'];
        }
        else  {
            $lang = new Zend_Locale("auto");
        }
        $locale = new Zend_Locale($lang);
        
        $cache = Zend_Cache::factory('Core',
                                     'File',
                                      array(
                                        'lifetime' => 120,
                                        'automatic_serialization' => true
                                      ),
                                      array());
        Zend_Translate::setCache($cache);
        
        $translate = new ZendX_Translate(
                            'gettext',
                            APPLICATION_PATH . '/language/manage',
                            $locale,
                            array('scan' => Zend_Translate::LOCALE_DIRECTORY));
                            
        if (!$translate->isAvailable($locale->getLanguage())) {
            $locale = new Zend_Locale('en');
        }

        Zend_Registry::set('Zend_Locale', $locale);
        Zend_Registry::set('Zend_Translate', $translate);

        if (!empty($_GET['lang'])) {
            setcookie('lang', $locale->getLanguage());
        }

    }

    protected function _initDoctype()
    {
        $this->bootstrap('view');
        $this->bootstrap('Locale');
        
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8');
        $view->headTitle(Zend_Registry::get("Zend_Translate")->_("Reports"));
    }
    
    protected function _initHelpers()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->addHelperPath("ZendX/View/Helper", "ZendX_View_Helper");
    }
    
    protected function _initAppRegistry()
    {
        $this->bootstrap('autoload');
        $this->bootstrap('db');
        $this->bootstrap('logging');
        
        Zend_Registry::set("TrustCare_Registry_User", new TrustCare_Registry_User());
    }
    
    protected function _initAcl()
    {
        $acl = new Zend_Acl();
        
        $acl->addRole(new Zend_Acl_Role('pharmacy_manager'));
        $acl->addRole(new Zend_Acl_Role('pharmacist'));
        
        $acl->add(new Zend_Acl_Resource('resource:form'));
        $acl->add(new Zend_Acl_Resource('resource:report'));
        $acl->add(new Zend_Acl_Resource('resource:admin.system_dict'));
        $acl->add(new Zend_Acl_Resource('resource:admin.pharm_dict'));
        $acl->add(new Zend_Acl_Resource('resource:admin.user'));
        $acl->add(new Zend_Acl_Resource('resource:admin.pharmacy'));        
        $acl->add(new Zend_Acl_Resource('resource:admin.patient'));        
        $acl->add(new Zend_Acl_Resource('resource:admin.system_logs'));
        
        $acl->allow('pharmacy_manager');
        
        $acl->allow('pharmacist');
        $acl->deny('pharmacist', 'resource:admin.system_dict');
        $acl->deny('pharmacist', 'resource:admin.user');
        $acl->deny('pharmacist', 'resource:admin.pharmacy');
        $acl->deny('pharmacist', 'resource:admin.system_logs');
        
        Zend_Registry::set('Zend_Acl', $acl);
    }
    
    protected function _initNavigation()
    {
        $this->bootstrap('acl');
        $this->bootstrap('registry');
        
        $pages = array(
            array(
                'label'         => "CDR",
                'uri'           => "",
                'pages'         => array(
                    array(
                        'label'         => Zend_Registry::get("Zend_Translate")->_("SMS"),
                        'controller'    => 'sms',
                        'action'        => 'list',
                        'resource'      => 'resource:admin.cdr',
                        'privilege'     => 'view',
                    ),
                    array(
                        'label'         => Zend_Registry::get("Zend_Translate")->_("Voice"),
                        'controller'    => 'voice',
                        'action'        => 'list',
                        'resource'      => 'resource:admin.cdr',
                        'privilege'     => 'view',
                    ),
                    array(
                        'label'         => 'Raw CDR',
                        'controller'    => 'voice',
                        'action'        => 'list-raw',
                        'resource'      => 'resource:admin.cdr',
                        'privilege'     => 'view',
                    ),
                ),
            ),
            array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Reports"),
                'uri'           => "",
                'resource'      => 'resource:admin.report',
                'privilege'     => 'view',
                'pages'         => array(
                    array(
                        'label'         => Zend_Registry::get("Zend_Translate")->_("Generate"),
                        'controller'    => 'report',
                        'action'        => 'generate',
                        'resource'      => 'resource:admin.report',
                        'privilege'     => 'create',
                    ),
                    array(
                        'label'         => 'ICONNECT',
                        'controller'    => 'report',
                        'action'        => 'list',
                        'params'        => array('type' => 'iconnect'),
                        'resource'      => 'resource:admin.report.iconnect',
                        'privilege'     => 'view',
                    ),
                    array(
                        'label'         => 'OPTIROAM',
                        'controller'    => 'report',
                        'action'        => 'list',
                        'params'        => array('type' => 'optiroam'),
                        'resource'      => 'resource:admin.report.optiroam',
                        'privilege'     => 'view',
                    ),
                ),
            ),
            array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Prefixes"),
                'uri'           => "",
                'resource'      => 'resource:admin.prefix',
                'pages'         => array(
                    array(
                        'label'         => Zend_Registry::get("Zend_Translate")->_("Moscow for iConnect"),
                        'controller'    => 'prefixes',
                        'action'        => 'moscow-list',
                        'resource'      => 'resource:admin.prefix',
                        'privilege'     => 'view',
                        'pages'         => array(
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Create"),
                                'controller'    => 'prefixes',
                                'action'        => 'moscow-create',
                                'visible'       => false,
                                'resource'      => 'resource:admin.prefix',
                                'privilege'     => 'create'
                            ),
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("View"),
                                'controller'    => 'prefixes',
                                'action'        => 'moscow-view',
                                'visible'       => false,
                                'resource'      => 'resource:admin.prefix',
                                'privilege'     => 'view'
                            ),
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Edit"),
                                'controller'    => 'prefixes',
                                'action'        => 'moscow-edit',
                                'visible'       => false,
                                'resource'      => 'resource:admin.prefix',
                                'privilege'     => 'edit'
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("System"),
                'uri'           => "",
                'resource'      => 'resource:admin',
                'pages'         => array(
                    array(
                        'label'         => Zend_Registry::get("Zend_Translate")->_("Administration"),
                        'uri'           => "",
                        'resource'      => 'resource:admin',
                        'pages'         => array(
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Users"),
                                'controller'    => 'admin',
                                'action'        => 'users',
                                'resource'      => 'resource:admin.user',
                                'privilege'     => 'view',
                                'pages'         => array(
                                    array(
                                        'label'         => Zend_Registry::get("Zend_Translate")->_("Create"),
                                        'controller'    => 'admin',
                                        'action'        => 'newuser',
                                        'visible'       => false,
                                        'resource'      => 'resource:admin.user',
                                        'privilege'     => 'create'
                                    ),
                                    array(
                                        'label'         => Zend_Registry::get("Zend_Translate")->_("Edit"),
                                        'controller'    => 'admin',
                                        'action'        => 'edituser',
                                        'visible'       => false,
                                        'resource'      => 'resource:admin.user',
                                        'privilege'     => 'edit'
                                    ),
                                )
                            ),
                        ),
                    ),
                    array(
                        'label'         => Zend_Registry::get("Zend_Translate")->_("Logs"),
                        'uri'           => "",
                        'resource'      => 'resource:admin.log',
                        'privilege'     => 'view',
                        'pages'         => array(
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Access"),
                                'controller'    => 'log',
                                'action'        => 'access',
                            ),
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Objects"),
                                'controller'    => 'log',
                                'action'        => 'objects',
                            ),
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("System"),
                                'controller'    => 'log',
                                'action'        => 'system',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Settings"),
                'controller'    => 'index',
                'action'        => 'settings',
                'pages'         => array(
                    array(
                        'label'         => Zend_Registry::get("Zend_Translate")->_("Edit"),
                        'controller'    => 'index',
                        'action'        => 'settings',
                        'visible'       => false,
                    ),
                ),
            ),
            array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Logout"),
                'controller'    => 'sign',
                'action'        => 'logout',
            ),
        );
        
        $container = new Zend_Navigation($pages);
        Zend_Registry::set('Zend_Navigation', $container);
        
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl(Zend_Registry::get("Zend_Acl"));
        $role = 'guest';
        $modelAdmin = Zend_Registry::get("TrustCare_Registry_User")->getAdmin();
        if(!is_null($modelAdmin)) {
            $role = $modelAdmin->role;
        }
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole((string)$role);
    }
}

