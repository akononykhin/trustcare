<?php

require_once(dirname(__FILE__) . '/setup.php');

define('TESTS_STARTED', 1);

class ModuleTests extends GroupTest {
    function ModuleTests() {
        $this->GroupTest('Module tests for TrustCare project');
        
        $this->addTestFile(dirname(__FILE__) . '/module/model/all_tests.php');
    }
}

if (!class_exists('EclipseReporter')) {
    $test = new ModuleTests();
    if (SimpleReporter::inCli()) {
        exit ($test->run(new TextReporter()) ? 0 : 1);
    }
 
    $test->run(new HtmlReporter());
}
