<?php

class BootstrapMain extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initAutoload()
    {
        $autoLoaderInstance = Zend_Loader_Autoloader::getInstance();
        
        $autoLoaderInstance->registerNamespace("TrustCare_");
    }

    protected function _initLogging()
    {
        define('LOG4PHP_DIR', APPLICATION_PATH."/../library/log4php/");
        define('LOG4PHP_CONFIGURATION', APPLICATION_PATH."/configs/log4php.ini");

        $this->bootstrap('db');
        require_once('LoggerManager.php');
        
        require_once 'ErrorHandler.php';
        set_error_handler('errorHandler');
        if (function_exists('set_exception_handler')) {
            set_exception_handler('exceptionHandler');
        }

    }
    
    protected function _initRegistry()
    {
        Zend_Registry::set("Storage", new TrustCare_Registry_Storage());
    }

    protected function _initDb()
    {
        $db_options = $this->getOption('db');
        $dbAdapter = Zend_Db::factory($db_options['adapter'], $db_options['params']);
        Zend_Registry::set('dbAdapter', $dbAdapter);
        Zend_Registry::set('dbOptions', $db_options);
    }
}

