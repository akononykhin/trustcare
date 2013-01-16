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

        $this->addTestFile(dirname(__FILE__) . '/log_access_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/log_objects_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/country_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/state_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/lga_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/facility_type_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/facility_level_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/facility_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/user_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/pharmacy_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/physician_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/patient_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/pharmacy_dictionary_type_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/pharmacy_dictionary_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/frm_care_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/frm_care_med_error_type_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/frm_care_med_adh_problem_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/frm_care_adh_intervention_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/frm_care_adh_intervention_outcome_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/frm_care_suspected_adr_hepatic_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/frm_care_suspected_adr_nervous_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/frm_care_suspected_adr_cardiovascular_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/frm_care_suspected_adr_skin_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/frm_care_suspected_adr_metabolic_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/frm_care_suspected_adr_musculoskeletal_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/frm_care_suspected_adr_general_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/frm_care_adr_intervention_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/frm_community_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/frm_community_referred_in_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/frm_community_referred_out_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/frm_community_palliative_care_type_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/frm_community_sti_type_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/frm_community_reproductive_health_type_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/frm_community_tuberculosis_type_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/frm_community_ovc_type_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/report_care_tests.php');
        $this->addTestFile(dirname(__FILE__) . '/report_community_tests.php');
        
    }
}


if (!class_exists('EclipseReporter') && !defined('TESTS_STARTED')) {
    $test = new ModelGroupTest();
    if (SimpleReporter::inCli()) {
        exit ($test->run(new TextReporter()) ? 0 : 1);
    }
 
    $test->run(new HtmlReporter());
}
