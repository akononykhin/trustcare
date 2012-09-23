<?php
include_once 'BootstrapMain.php';

class BootstrapUnitTests extends BootstrapMain
{
    protected function _initSimpletest()
    {
        defined('SIMPLE_TEST')
            || define('SIMPLE_TEST', APPLICATION_PATH . '/../external/simpletest/');

        if (!file_exists(SIMPLE_TEST . '/browser.php')) {
            die ('Make sure the SIMPLE_TEST constant is set correctly in this file(' . SIMPLE_TEST . ')');
        }

        require_once(SIMPLE_TEST . '/web_tester.php');
        require_once(SIMPLE_TEST . '/reporter.php');
        require_once(SIMPLE_TEST . '/unit_tester.php');
        require_once(SIMPLE_TEST . '/mock_objects.php');
    }
    
}

