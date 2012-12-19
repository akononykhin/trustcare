<?php
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

define('APPLICATION_ENV', 'testing');

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

require_once 'Zend/Application.php';

$application = new Zend_Application(
    APPLICATION_ENV, 
   APPLICATION_PATH . '/configs/app.ini'
);

function errorHandlerStub($errno, $errstr)
{
}

$application->bootstrap();
error_reporting (E_ALL ^ E_NOTICE);

