<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */

require_once(dirname(__FILE__) . '/../../setup.php');
 
class ModelGroupTest extends GroupTest {
    function ModelGroupTest() {
        $this->GroupTest('All Model tests');
//        $this->addTestFile(dirname(__FILE__) . '/active_session_tests.php');
    }
}


if (!class_exists('EclipseReporter') && !defined('TESTS_STARTED')) {
    $test = new ModelGroupTest();
    if (SimpleReporter::inCli()) {
        exit ($test->run(new TextReporter()) ? 0 : 1);
    }
 
    $test->run(new HtmlReporter());
}
