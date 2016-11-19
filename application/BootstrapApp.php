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
                            APPLICATION_PATH . '/language/portal',
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

    
    protected function _initDatetimeSettings()
    {
        $this->bootstrap('Locale');

        if('en' == Zend_Registry::get('Zend_Locale')) {
            $dateTimeFormat = "yyyy-MM-dd HH:mm:ss";
            $dateFormat = "yyyy-MM-dd";
        }
        else {
            $dateTimeFormat = Zend_Locale_Data::getContent(Zend_Registry::get('Zend_Locale'), 'dateTime');
            $dateFormat = Zend_Locale_Data::getContent(Zend_Registry::get('Zend_Locale'), 'date');
        }
        Zend_Registry::set('dateTimeFormat', $dateTimeFormat);
        Zend_Registry::set('dateFormat', $dateFormat);
        
        $tzOffset = array_key_exists('tz_offset', $_COOKIE) ? (int)$_COOKIE['tz_offset'] : 0;
        
        $zone = "Etc/GMT";
        $zone .= ($tzOffset < 0) ? "+" : "-";
        $zone .= (int)abs($tzOffset / 60);      

        Zend_Registry::set('clientTimeZoneOffset', $tzOffset);
        Zend_Registry::set('clientTimeZone', $zone);
    }
    
    protected function _initDoctype()
    {
        $this->bootstrap('view');
        $this->bootstrap('Locale');
        
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8');
        $view->headTitle(Zend_Registry::get("Zend_Translate")->_("TrustCare Application"));
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
    
    protected function _initAcl()
    {
        $acl = new Zend_Acl();
        
        $acl->addRole(new Zend_Acl_Role('pharmacy_manager'));
        $acl->addRole(new Zend_Acl_Role('pharmacist'));
        $acl->addRole(new Zend_Acl_Role('guest'));
        
        $acl->add(new Zend_Acl_Resource('resource:form'));
        $acl->add(new Zend_Acl_Resource('resource:report'));
        $acl->add(new Zend_Acl_Resource('resource:admin'));
        $acl->add(new Zend_Acl_Resource('resource:admin.system_dict'));
        $acl->add(new Zend_Acl_Resource('resource:admin.pharm_dict'));
        $acl->add(new Zend_Acl_Resource('resource:admin.user'));
        $acl->add(new Zend_Acl_Resource('resource:admin.pharmacy'));        
        $acl->add(new Zend_Acl_Resource('resource:admin.physician'));        
        $acl->add(new Zend_Acl_Resource('resource:admin.patient'));        
        $acl->add(new Zend_Acl_Resource('resource:admin.system_log'));
        
        $acl->allow('pharmacy_manager');
        
        $acl->allow('pharmacist');
        $acl->deny('pharmacist', 'resource:admin.system_dict');
        $acl->deny('pharmacist', 'resource:admin.user');
        $acl->deny('pharmacist', 'resource:admin.pharmacy');
        $acl->deny('pharmacist', 'resource:admin.physician');
        $acl->deny('pharmacist', 'resource:admin.system_log');
        
        Zend_Registry::set('Zend_Acl', $acl);
    }
    
    protected function _initNavigation()
    {
        $this->bootstrap('acl');
        $this->bootstrap('registry');
        
        $pages = array(
            array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Counseling"),
                'uri'           => "",
                'resource'      => 'resource:form',
            	'pages'         => array(
                    array(
                        'label'         => Zend_Registry::get("Zend_Translate")->_("Pharmaceutical Care"),
                		'module'        => 'portal',
                    	'controller'    => 'form_care',
                        'action'        => 'list',
                    	'resource'      => 'resource:form',
                        'privilege'     => 'view',
                        'pages'         => array(
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Create"),
                				'module'        => 'portal',
                            	'controller'    => 'form_care',
                                'action'        => 'create',
                                'visible'       => false,
                    			'resource'      => 'resource:form',
                                'privilege'     => 'create'
                            ),
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("View"),
                				'module'        => 'portal',
                            	'controller'    => 'form_care',
                                'action'        => 'view',
                                'visible'       => false,
                    			'resource'      => 'resource:form',
                                'privilege'     => 'view'
                            ),
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Edit"),
                				'module'        => 'portal',
                            	'controller'    => 'form_care',
                                'action'        => 'edit',
                                'visible'       => false,
                    			'resource'      => 'resource:form',
                                'privilege'     => 'view'
                            ),
                        ),
                    ),
                    array(
                        'label'         => Zend_Registry::get("Zend_Translate")->_("Community Pharmacy"),
                		'module'        => 'portal',
                    	'controller'    => 'form_community',
                        'action'        => 'list',
                    	'resource'      => 'resource:form',
                        'privilege'     => 'view',
                        'pages'         => array(
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Create"),
                				'module'        => 'portal',
                            	'controller'    => 'form_community',
                                'action'        => 'create',
                                'visible'       => false,
                    			'resource'      => 'resource:form',
                                'privilege'     => 'create'
                            ),
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("View"),
                				'module'        => 'portal',
                            	'controller'    => 'form_community',
                                'action'        => 'view',
                                'visible'       => false,
                    			'resource'      => 'resource:form',
                                'privilege'     => 'view'
                            ),
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Edit"),
                				'module'        => 'portal',
                            	'controller'    => 'form_community',
                                'action'        => 'edit',
                                'visible'       => false,
                    			'resource'      => 'resource:form',
                                'privilege'     => 'edit'
                            ),
                        ),
                    ),
                    
                ),
            ),
            array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("NAFDAC"),
                'module'        => 'portal',
            	'controller'    => 'nafdac',
                'action'        => 'list',
                'resource'      => 'resource:form',
                'privilege'     => 'view',
                'pages'         => array(
                    array(
                        'label'         => Zend_Registry::get("Zend_Translate")->_("Create"),
                		'module'        => 'portal',
                    	'controller'    => 'nafdac',
                        'action'        => 'create',
                        'visible'       => false,
                        'resource'      => 'resource:form',
                        'privilege'     => 'create'
                    ),
                    array(
                        'label'         => Zend_Registry::get("Zend_Translate")->_("View"),
                		'module'        => 'portal',
                    	'controller'    => 'nafdac',
                        'action'        => 'view',
                        'visible'       => false,
                        'resource'      => 'resource:form',
                        'privilege'     => 'view'
                    ),
                ),
            ),
            
            array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Reports"),
                'uri'           => "",
                'resource'      => 'resource:report',
            	'pages'         => array(
                    array(
                        'label'         => Zend_Registry::get("Zend_Translate")->_("Pharmaceutical Care"),
                		'module'        => 'portal',
                    	'controller'    => 'report_care',
                        'action'        => 'list',
                    	'resource'      => 'resource:report',
                        'privilege'     => 'view',
                        'pages'         => array(
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Generate"),
                				'module'        => 'portal',
                            	'controller'    => 'report_care',
                                'action'        => 'generate',
                                'visible'       => false,
                    			'resource'      => 'resource:report',
                                'privilege'     => 'create'
                            ),
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("View"),
                				'module'        => 'portal',
                            	'controller'    => 'report_care',
                                'action'        => 'view',
                                'visible'       => false,
                    			'resource'      => 'resource:report',
                                'privilege'     => 'view'
                            ),
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Load"),
                				'module'        => 'portal',
                            	'controller'    => 'report_care',
                                'action'        => 'load',
                                'visible'       => false,
                    			'resource'      => 'resource:report',
                                'privilege'     => 'view'
                            ),
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Edit"),
                				'module'        => 'portal',
                            	'controller'    => 'report_care',
                                'action'        => 'create',
                                'visible'       => false,
                    			'resource'      => 'resource:report',
                                'privilege'     => 'edit'
                            ),
                        ),
                    ),
                    array(
                        'label'         => Zend_Registry::get("Zend_Translate")->_("Community MSF"),
                		'module'        => 'portal',
                    	'controller'    => 'report_community-msf',
                        'action'        => 'list',
                    	'resource'      => 'resource:report',
                        'privilege'     => 'view',
                        'pages'         => array(
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Generate"),
                				'module'        => 'portal',
                            	'controller'    => 'report_community-msf',
                                'action'        => 'generate',
                                'visible'       => false,
                    			'resource'      => 'resource:report',
                                'privilege'     => 'create'
                            ),
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("View"),
                				'module'        => 'portal',
                            	'controller'    => 'report_community-msf',
                                'action'        => 'view',
                                'visible'       => false,
                    			'resource'      => 'resource:report',
                                'privilege'     => 'view'
                            ),
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Load"),
                				'module'        => 'portal',
                            	'controller'    => 'report_community-msf',
                                'action'        => 'load',
                                'visible'       => false,
                    			'resource'      => 'resource:report',
                                'privilege'     => 'view'
                            ),
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Edit"),
                				'module'        => 'portal',
                            	'controller'    => 'report_community-msf',
                                'action'        => 'create',
                                'visible'       => false,
                    			'resource'      => 'resource:report',
                                'privilege'     => 'edit'
                            ),
                        ),
                    ),
            	    array(
            	        'label'         => Zend_Registry::get("Zend_Translate")->_("Community Services Register"),
                		'module'        => 'portal',
            	    	'controller'    => 'report_community-services',
            	        'action'        => 'list',
            	        'resource'      => 'resource:report',
            	        'privilege'     => 'view',
            	        'pages'         => array(
            	            array(
            	                'label'         => Zend_Registry::get("Zend_Translate")->_("Generate"),
                				'module'        => 'portal',
            	            	'controller'    => 'report_community-services',
            	                'action'        => 'generate',
            	                'visible'       => false,
            	                'resource'      => 'resource:report',
            	                'privilege'     => 'create'
            	            ),
            	            array(
            	                'label'         => Zend_Registry::get("Zend_Translate")->_("View"),
                				'module'        => 'portal',
            	            	'controller'    => 'report_community-services',
            	                'action'        => 'view',
            	                'visible'       => false,
            	                'resource'      => 'resource:report',
            	                'privilege'     => 'view'
            	            ),
            	            array(
            	                'label'         => Zend_Registry::get("Zend_Translate")->_("Load"),
                				'module'        => 'portal',
            	            	'controller'    => 'report_community-services',
            	                'action'        => 'load',
            	                'visible'       => false,
            	                'resource'      => 'resource:report',
            	                'privilege'     => 'view'
            	            ),
            	            array(
            	                'label'         => Zend_Registry::get("Zend_Translate")->_("Edit"),
                				'module'        => 'portal',
            	            	'controller'    => 'report_community-services',
            	                'action'        => 'create',
            	                'visible'       => false,
            	                'resource'      => 'resource:report',
            	                'privilege'     => 'edit'
            	            ),
            	        ),
            	    ),
            	     
                ),
            ),
            
            array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Administration"),
                'uri'           => "",
                'resource'      => 'resource:admin',
                'pages'         => array(
                    array(
                        'label'         => Zend_Registry::get("Zend_Translate")->_("System Dictionaries"),
                		'uri'           => "",
                        'resource'      => 'resource:admin.system_dict',
                        'pages'         => array(
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Countries"),
                				'module'        => 'portal',
                            	'controller'    => 'country',
                                'action'        => 'list',
                                'resource'      => 'resource:admin.system_dict',
                                'privilege'     => 'view',
                                'pages'         => array(
                                    array(
                                        'label'         => Zend_Registry::get("Zend_Translate")->_("Create"),
                						'module'        => 'portal',
                                    	'controller'    => 'country',
                                        'action'        => 'create',
                                        'visible'       => false,
                                        'resource'      => 'resource:admin.system_dict',
                                        'privilege'     => 'create'
                                    ),
                                    array(
                                        'label'         => Zend_Registry::get("Zend_Translate")->_("Edit"),
                						'module'        => 'portal',
                                    	'controller'    => 'country',
                                        'action'        => 'edit',
                                        'visible'       => false,
                                        'resource'      => 'resource:admin.system_dict',
                                        'privilege'     => 'edit'
                                    ),
                                    array(
                                        'label'         => Zend_Registry::get("Zend_Translate")->_("Import"),
                						'module'        => 'portal',
                                    	'controller'    => 'country',
                                        'action'        => 'import',
                                        'visible'       => false,
                                        'resource'      => 'resource:admin.system_dict',
                                        'privilege'     => 'create'
                                    ),
                                ),
                            ),
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("States"),
                				'module'        => 'portal',
                            	'controller'    => 'state',
                                'action'        => 'list',
                                'resource'      => 'resource:admin.system_dict',
                                'privilege'     => 'view',
                                'pages'         => array(
                                    array(
                                        'label'         => Zend_Registry::get("Zend_Translate")->_("Create"),
                						'module'        => 'portal',
                                    	'controller'    => 'state',
                                        'action'        => 'create',
                                        'visible'       => false,
                                        'resource'      => 'resource:admin.system_dict',
                                        'privilege'     => 'create'
                                    ),
                                    array(
                                        'label'         => Zend_Registry::get("Zend_Translate")->_("Edit"),
                						'module'        => 'portal',
                                    	'controller'    => 'state',
                                        'action'        => 'edit',
                                        'visible'       => false,
                                        'resource'      => 'resource:admin.system_dict',
                                        'privilege'     => 'edit'
                                    ),
                                    array(
                                        'label'         => Zend_Registry::get("Zend_Translate")->_("Import"),
                						'module'        => 'portal',
                                    	'controller'    => 'state',
                                        'action'        => 'import',
                                        'visible'       => false,
                                        'resource'      => 'resource:admin.system_dict',
                                        'privilege'     => 'create'
                                    ),
                                ),
                            ),
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("LGAs"),
                				'module'        => 'portal',
                            	'controller'    => 'lga',
                                'action'        => 'list',
                                'resource'      => 'resource:admin.system_dict',
                                'privilege'     => 'view',
                                'pages'         => array(
                                    array(
                                        'label'         => Zend_Registry::get("Zend_Translate")->_("Create"),
                						'module'        => 'portal',
                                    	'controller'    => 'lga',
                                        'action'        => 'create',
                                        'visible'       => false,
                                        'resource'      => 'resource:admin.system_dict',
                                        'privilege'     => 'create'
                                    ),
                                    array(
                                        'label'         => Zend_Registry::get("Zend_Translate")->_("Edit"),
                						'module'        => 'portal',
                                    	'controller'    => 'lga',
                                        'action'        => 'edit',
                                        'visible'       => false,
                                        'resource'      => 'resource:admin.system_dict',
                                        'privilege'     => 'edit'
                                    ),
                                    array(
                                        'label'         => 'LGA',
                						'module'        => 'portal',
                                    	'controller'    => 'lga',
                                        'action'        => 'import',
                                        'visible'       => false,
                                        'resource'      => 'resource:admin.system_dict',
                                        'privilege'     => 'create'
                                    ),
                                ),
                            ),
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Facilities"),
                				'module'        => 'portal',
                            	'controller'    => 'facility',
                                'action'        => 'list',
                                'resource'      => 'resource:admin.system_dict',
                                'privilege'     => 'view',
                                'pages'         => array(
                                    array(
                                        'label'         => Zend_Registry::get("Zend_Translate")->_("Create"),
                						'module'        => 'portal',
                                    	'controller'    => 'facility',
                                        'action'        => 'create',
                                        'visible'       => false,
                                        'resource'      => 'resource:admin.system_dict',
                                        'privilege'     => 'create'
                                    ),
                                    array(
                                        'label'         => Zend_Registry::get("Zend_Translate")->_("Edit"),
                						'module'        => 'portal',
                                    	'controller'    => 'facility',
                                        'action'        => 'edit',
                                        'visible'       => false,
                                        'resource'      => 'resource:admin.system_dict',
                                        'privilege'     => 'edit'
                                    ),
                                    array(
                                        'label'         => Zend_Registry::get("Zend_Translate")->_("Import"),
                						'module'        => 'portal',
                                    	'controller'    => 'facility',
                                        'action'        => 'import',
                                        'visible'       => false,
                                        'resource'      => 'resource:admin.system_dict',
                                        'privilege'     => 'create'
                                    ),
                                    array(
                                        'label'         => Zend_Registry::get("Zend_Translate")->_("Types"),
                						'module'        => 'portal',
                                    	'controller'    => 'facility_type',
                                        'action'        => 'list',
                                        'resource'      => 'resource:admin.system_dict',
                                        'privilege'     => 'view',
                                        'pages'         => array(
                                            array(
                                                'label'         => Zend_Registry::get("Zend_Translate")->_("Create"),
                								'module'        => 'portal',
                                            	'controller'    => 'facility_type',
                                                'action'        => 'create',
                                                'visible'       => false,
                                                'resource'      => 'resource:admin.system_dict',
                                                'privilege'     => 'create'
                                            ),
                                            array(
                                                    'label'         => Zend_Registry::get("Zend_Translate")->_("Edit"),
                									'module'        => 'portal',
                                            		'controller'    => 'facility_type',
                                                    'action'        => 'edit',
                                                    'visible'       => false,
                                                    'resource'      => 'resource:admin.system_dict',
                                                    'privilege'     => 'edit'
                                            ),
                                        ),
                                    ),
                                    array(
                                        'label'         => Zend_Registry::get("Zend_Translate")->_("Levels"),
                						'module'        => 'portal',
                                    	'controller'    => 'facility_level',
                                        'action'        => 'list',
                                        'resource'      => 'resource:admin.system_dict',
                                        'privilege'     => 'view',
                                        'pages'         => array(
                                            array(
                                                'label'         => Zend_Registry::get("Zend_Translate")->_("Create"),
                								'module'        => 'portal',
                                            	'controller'    => 'facility_level',
                                                'action'        => 'create',
                                                'visible'       => false,
                                                'resource'      => 'resource:admin.system_dict',
                                                'privilege'     => 'create'
                                            ),
                                            array(
                                                    'label'         => Zend_Registry::get("Zend_Translate")->_("Edit"),
                									'module'        => 'portal',
                                            		'controller'    => 'facility_level',
                                                    'action'        => 'edit',
                                                    'visible'       => false,
                                                    'resource'      => 'resource:admin.system_dict',
                                                    'privilege'     => 'edit'
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'label'         => Zend_Registry::get("Zend_Translate")->_("Pharmacy Dictionaries"),
                		'module'        => 'portal',
                    	'controller'    => "pharm-dict",
                        'action'        => 'types-list',
                    	'resource'      => 'resource:admin.pharm_dict',
                    	'privilege'		=> 'view',
                    	'pages'			=> array(
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("List"),
                				'module'        => 'portal',
                            	'controller'    => 'pharm-dict',
                                'action'        => 'list',
                                'visible'       => false,
                                'resource'      => 'resource:admin.pharm_dict',
                                'privilege'     => 'view'
                            ),
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Create"),
                				'module'        => 'portal',
                            	'controller'    => 'pharm-dict',
                                'action'        => 'create',
                                'visible'       => false,
                                'resource'      => 'resource:admin.pharm_dict',
                                'privilege'     => 'create'
                            ),
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Edit"),
                				'module'        => 'portal',
                            	'controller'    => 'pharm-dict',
                                'action'        => 'edit',
                                'visible'       => false,
                                'resource'      => 'resource:admin.pharm_dict',
                                'privilege'     => 'edit'
                            ),
                        ),
                    ),
                    array(
                        'label'         => Zend_Registry::get("Zend_Translate")->_("Users"),
                		'module'        => 'portal',
                    	'controller'    => 'user',
                        'action'        => 'list',
                        'resource'      => 'resource:admin.user',
                        'privilege'     => 'view',
                        'pages'         => array(
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Create"),
                				'module'        => 'portal',
                            	'controller'    => 'user',
                                'action'        => 'create',
                                'visible'       => false,
                                'resource'      => 'resource:admin.user',
                                'privilege'     => 'create'
                            ),
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Edit"),
                				'module'        => 'portal',
                            	'controller'    => 'user',
                                'action'        => 'edit',
                                'visible'       => false,
                                'resource'      => 'resource:admin.user',
                                'privilege'     => 'edit'
                            ),
                        )
                    ),
                    array(
                        'label'         => Zend_Registry::get("Zend_Translate")->_("Pharmacies"),
                		'module'        => 'portal',
                    	'controller'    => 'pharmacy',
                        'action'        => 'list',
                        'resource'      => 'resource:admin.pharmacy',
                        'privilege'     => 'view',
                        'pages'         => array(
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Create"),
                				'module'        => 'portal',
                            	'controller'    => 'pharmacy',
                                'action'        => 'create',
                                'visible'       => false,
                                'resource'      => 'resource:admin.pharmacy',
                                'privilege'     => 'create'
                            ),
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Edit"),
                				'module'        => 'portal',
                            	'controller'    => 'pharmacy',
                                'action'        => 'edit',
                                'visible'       => false,
                                'resource'      => 'resource:admin.pharmacy',
                                'privilege'     => 'edit'
                            ),
                        )
                    ),
                    array(
                        'label'         => Zend_Registry::get("Zend_Translate")->_("Physicians"),
                		'module'        => 'portal',
                    	'controller'    => 'physician',
                        'action'        => 'list',
                        'resource'      => 'resource:admin.physician',
                        'privilege'     => 'view',
                        'pages'         => array(
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Create"),
                				'module'        => 'portal',
                            	'controller'    => 'physician',
                                'action'        => 'create',
                                'visible'       => false,
                                'resource'      => 'resource:admin.physician',
                                'privilege'     => 'create'
                            ),
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Edit"),
                				'module'        => 'portal',
                            	'controller'    => 'physician',
                                'action'        => 'edit',
                                'visible'       => false,
                                'resource'      => 'resource:admin.physician',
                                'privilege'     => 'edit'
                            ),
                        )
                    ),
                    array(
                        'label'         => Zend_Registry::get("Zend_Translate")->_("Patients"),
                		'module'        => 'portal',
                    	'controller'    => 'patient',
                        'action'        => 'list',
                        'resource'      => 'resource:admin.patient',
                        'privilege'     => 'view',
                        'pages'         => array(
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Create"),
                				'module'        => 'portal',
                            	'controller'    => 'patient',
                                'action'        => 'create',
                                'visible'       => false,
                                'resource'      => 'resource:admin.patient',
                                'privilege'     => 'create'
                            ),
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Edit"),
                				'module'        => 'portal',
                            	'controller'    => 'patient',
                                'action'        => 'edit',
                                'visible'       => false,
                                'resource'      => 'resource:admin.patient',
                                'privilege'     => 'edit'
                            ),
                        )
                    ),
                    
                    array(
                        'label'         => Zend_Registry::get("Zend_Translate")->_("System Logs"),
                        'uri'           => "",
                        'resource'      => 'resource:admin.system_log',
                        'privilege'     => 'view',
                        'pages'         => array(
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Objects"),
                				'module'        => 'portal',
                            	'controller'    => 'system_log',
                                'action'        => 'objects',
                            ),
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Access"),
                				'module'        => 'portal',
                            	'controller'    => 'system_log',
                                'action'        => 'access',
                            ),
                            array(
                                'label'         => Zend_Registry::get("Zend_Translate")->_("Errors"),
                				'module'        => 'portal',
                            	'controller'    => 'system_log',
                                'action'        => 'errors',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'label'         => Zend_Registry::get("Zend_Translate")->_("Settings"),
                'module'        => 'portal',
            	'controller'    => 'index',
                'action'        => 'settings',
                'pages'         => array(
                    array(
                        'label'         => Zend_Registry::get("Zend_Translate")->_("Edit"),
                		'module'        => 'portal',
                    	'controller'    => 'index',
                        'action'        => 'settings',
                        'visible'       => false,
                    ),
                ),
            ),
        );
        
        $container = new Zend_Navigation($pages);
        Zend_Registry::set('Zend_Navigation', $container);
        
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl(Zend_Registry::get("Zend_Acl"));
        $role = 'guest';
        $modelUser = Zend_Registry::get("TrustCare_Registry_User")->getUser();
        if(!is_null($modelUser)) {
            $role = $modelUser->role;
        }
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole((string)$role);
    }
}

