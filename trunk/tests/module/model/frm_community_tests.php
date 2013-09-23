<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */

class TestOfFrmCommunity extends UnitTestCase {
    private $paramsAtDb = null;
    
    /**
     * 
     * @var Zend_Db
     */
    private $db = null;
    
    function setUp() {
        $this->db = Zend_Registry::get('dbAdapter');
        
        try {
            $fileName = sprintf("%s/_files/frm_community_test.sql", dirname(__FILE__));
            $fh = fopen($fileName, "r");
            if ($fh) {
                while (!feof($fh)) {
                    $query = trim(fgets($fh, 4096));
                    if(!empty($query)) {
                        $res = $this->db->query($query);
                    }
                }

                fclose($fh);
            }
            
            $params = array(
                'id' => $this->db->nextSequenceId('frm_community_id_seq'),
                'generation_date' => '2012-10-01 11:23:45',
                'id_user' => 1,
                'is_commited' => true,
                'id_pharmacy' => 2,
                'id_patient' => 1,
                'date_of_visit' => '2012-05-01',
                'date_of_visit_month_index' => 201205,
                'is_first_visit_to_pharmacy' => true,
                'is_referred_in' => false,
                'is_referred_out' => true,
                'is_referral_completed' => false,
                'is_hiv_risk_assesment_done' => true,
                'is_htc_done' => false,
                'htc_result_id' => 1,
                'is_client_received_htc' => true,
                'is_htc_done_in_current_pharmacy' => false,
                'is_palliative_services_to_plwha' => true,
                'is_sti_services' => false,
                'is_reproductive_health_services' => true,
                'is_tuberculosis_services' => false,
                'is_ovc_services' => true,
                'is_patient_younger_15' => false,
                'is_patient_male' => true,
                'id_nafdac' => 1,
            );

            $columns = array();
            $values = array();
            foreach($params as $key=>$value) {
                $columns[] = $this->db->quoteIdentifier($key);
                if(!is_bool($value) && !is_int($value)) {
                    $values[] = $this->db->quote($value);
                }
                else {
                    $values[] = $this->db->quote($value, Zend_Db::INT_TYPE);
                }
            }
            $query = sprintf("insert into frm_community(%s) values(%s);", join(",", $columns), join(",", $values));
            $res = $this->db->query($query);
            $this->paramsAtDb = $params;
        }
        catch(Exception $ex){}
    }


    function tearDown() {
        $query = sprintf("delete from frm_community;");
        $this->db->query($query);
        
        $query = sprintf("update db_sequence set value=1 where name='frm_community_id_seq';");
        $this->db->query($query);
        
        $query = sprintf("delete from nafdac;");
        $this->db->query($query);
        
        $query = sprintf("delete from pharmacy;");
        $this->db->query($query);
        
        $query = sprintf("delete from patient;");
        $this->db->query($query);
        
        
        $query = sprintf("delete from user;");
        $this->db->query($query);
    }
    

    function testInitializing() {
        $params = array(
                'id' => '1',
                'generation_date' => '2012-09-01 11:23:45',
                'id_user' => 2,
                'is_commited' => false,
                'id_pharmacy' => 2,
                'id_patient' => 1,
                'date_of_visit' => '2012-05-01',
                'date_of_visit_month_index' => 201206,
                'is_first_visit_to_pharmacy' => false,
                'is_referred_in' => true,
                'is_referred_out' => false,
                'is_referral_completed' => true,
                'is_hiv_risk_assesment_done' => false,
                'is_htc_done' => true,
                'htc_result_id' => 2,
                'is_client_received_htc' => false,
                'is_htc_done_in_current_pharmacy' => true,
                'is_palliative_services_to_plwha' => false,
                'is_sti_services' => true,
                'is_reproductive_health_services' => false,
                'is_tuberculosis_services' => true,
                'is_ovc_services' => false,
                'is_patient_younger_15' => false,
                'is_patient_male' => true,
                'id_nafdac' => 2,
                'mapperOptions' => array('adapter' => $this->db)
        );
        
        $model = new TrustCare_Model_FrmCommunity($params);

        $this->_compareObjectAndParams($model, $params);
    }
    
    function testDefaultValues() {
    }

    function testLoadExistingById() {
        $model = TrustCare_Model_FrmCommunity::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->_compareObjectAndParams($model, $this->paramsAtDb);
    }

    function testLoadUnexistingById() {
        $model = TrustCare_Model_FrmCommunity::find(-1 * $this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        
        $this->assertNull($model, "This entity must not be loaded");
    }


    function testSaveNew() {
        $params = array(
                'generation_date' => '2012-09-01 11:23:45',
                'id_user' => 1,
                'is_commited' => true,
                'id_pharmacy' => 1,
        		'id_patient' => 2,
                'date_of_visit' => '2012-07-02',
                'is_first_visit_to_pharmacy' => false,
                'is_referred_in' => true,
                'is_referred_out' => true,
                'is_referral_completed' => false,
                'is_hiv_risk_assesment_done' => true,
                'is_htc_done' => false,
                'htc_result_id' => 3,
                'is_client_received_htc' => true,
                'is_htc_done_in_current_pharmacy' => false,
                'is_palliative_services_to_plwha' => true,
                'is_sti_services' => false,
                'is_reproductive_health_services' => true,
                'is_tuberculosis_services' => false,
                'is_ovc_services' => true,
                'is_patient_younger_15' => false,
                'is_patient_male' => true,
                'id_nafdac' => 1,
        );
        
        try {
            $model = new TrustCare_Model_FrmCommunity(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
            
            $id = $model->id;
            $params['id'] = $id;
            $params['date_of_visit_month_index'] = 201207; /* it's filled indirectly */
            
            $model1 = TrustCare_Model_FrmCommunity::find($params['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->_compareObjectAndParams($model1, $params);
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Can't save new entity: %s", $ex->getMessage()));
        }
    }
    
    
    function testSaveWithEmptyValues()
    {
        /* with empty */
        try {
            $params = array(
                'id_pharmacy' => 1,
                'id_patient' => 2,
                'date_of_visit' => '2012-07-02',
                'id_nafdac' => ''
            );
            $model = new TrustCare_Model_FrmCommunity(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
    
            $model1 = TrustCare_Model_FrmCommunity::find($params['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->assertNull($model1->id_nafdac, "Not NULL 'id_nafdac': %s");
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Can't save new entity: %s", $ex->getMessage()));
        }
    
        /* with null */
        try {
            $params = array(
                'id_pharmacy' => 1,
                'id_patient' => 2,
                'date_of_visit' => '2012-07-02',
                'id_nafdac' => null
            );
            $model = new TrustCare_Model_FrmCommunity(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
    
            $model1 = TrustCare_Model_FrmCommunity::find($params['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->assertNull($model1->id_nafdac, "Not NULL 'id_nafdac': %s");
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Can't save new entity: %s", $ex->getMessage()));
        }
    
        /* with not specified */
        try {
            $params = array(
                'id_pharmacy' => 1,
                'id_patient' => 2,
                'date_of_visit' => '2012-07-02',
            );
            $model = new TrustCare_Model_FrmCommunity(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
    
            $model1 = TrustCare_Model_FrmCommunity::find($params['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->assertNull($model1->id_nafdac, "Not NULL 'id_nafdac': %s");
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Can't save new entity: %s", $ex->getMessage()));
        }
    }
    
    function testChangeParameters()
    {
        $model = TrustCare_Model_FrmCommunity::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        if(!is_null($model)) {
            $dateOfVisit = '2011-03-01' == $model->date_of_visit ? '2011-04-02' : '2011-03-01';
            $params['id_user'] = 2 == $model->id_user ? 1 : 2;
            $params['is_commited'] = !$model->is_commited;
            $params['id_pharmacy'] = 2 == $model->id_pharmacy ? 1 : 2;
            $params['id_patient'] = 1 == $model->id_patient ? 2 : 1;
            $params['date_of_visit'] = $dateOfVisit;
            $params['is_first_visit_to_pharmacy'] = !$model->is_first_visit_to_pharmacy;
            $params['is_referred_in'] = !$model->is_referred_in;
            $params['is_referred_out'] = !$model->is_referred_out;
            $params['is_referral_completed'] = !$model->is_referral_completed;
            $params['is_hiv_risk_assesment_done'] = !$model->is_hiv_risk_assesment_done;
            $params['is_htc_done'] = !$model->is_htc_done;
            $params['htc_result_id'] = $model->htc_result_id == 1 ? 2 : 1;
            $params['is_client_received_htc'] = !$model->is_client_received_htc;
            $params['is_htc_done_in_current_pharmacy'] = !$model->is_htc_done_in_current_pharmacy;
            $params['is_palliative_services_to_plwha'] = !$model->is_palliative_services_to_plwha;
            $params['is_sti_services'] = !$model->is_sti_services;
            $params['is_reproductive_health_services'] = !$model->is_reproductive_health_services;
            $params['is_tuberculosis_services'] = !$model->is_tuberculosis_services;
            $params['is_ovc_services'] = !$model->is_ovc_services;
            $params['is_patient_younger_15'] = !$model->is_patient_younger_15;
            $params['is_patient_male'] = !$model->is_patient_male;
            $params['id_nafdac'] = $model->id_nafdac == 1 ? 2 : 1;
            
            try {
                $model->setOptions($params);
                $model->save();
                
                $params['id'] = $model->id;
                $params['generation_date'] = $model->generation_date;
                if(!preg_match('/^(\d{4})-(\d{2})-\d{2}$/', $dateOfVisit, $matches)) {
                    throw new Exception(sprintf("Incorrect format of date_of_visit: %s", $dateOfVisit));
                }
                $params['date_of_visit_month_index'] = $matches[1].$matches[2];
                
                $model1 = TrustCare_Model_FrmCommunity::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
                $this->_compareObjectAndParams($model1, $params);
            }
            catch(Exception $ex) {
                $this->assertTrue(false, sprintf("Unexpected exception: %s", $ex->getMessage()));
            }

        }
        else {
            $this->assertTrue(false, "Can't initialize object");
        }
    }
    
    
    function testChangeParametersSetEmpty()
    {
        $model = TrustCare_Model_FrmCommunity::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
    
        if(!is_null($model)) {
            /* Check empty values */
            try {
                $params = array(
                    'htc_result_id' => '',
                    'id_nafdac' => ''
                );
                $model->setOptions($params);
                $model->save();
    
                $model1 = TrustCare_Model_FrmCommunity::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
                $this->assertEqual($model1->htc_result_id, $params['htc_result_id'], "Incorrect 'htc_result_id': %s");
            }
            catch(Exception $ex) {
                $this->assertTrue(false, sprintf("Unexpected exception: %s", $ex->getMessage()));
            }
    
            /* Check null values */
            try {
                $params = array(
                    'htc_result_id' => null,
                    'id_nafdac' => null
                );
                $model->setOptions($params);
                $model->save();
    
                $model1 = TrustCare_Model_FrmCommunity::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
                $this->assertEqual($model1->htc_result_id, $params['htc_result_id'], "Incorrect 'htc_result_id': %s");
            }
            catch(Exception $ex) {
                $this->assertTrue(false, sprintf("Unexpected exception: %s", $ex->getMessage()));
            }
        }
        else {
            $this->assertTrue(false, "Can't initialize object");
        }
    }
    
    public function testDelete() {
        $model = TrustCare_Model_FrmCommunity::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        try {
            $model->delete();
            
            $model1 = TrustCare_Model_FrmCommunity::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->assertNull($model1, "Entity hasn't been deleted: %s");
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Delete() thrown exception: %s", $ex->getMessage()));
        }
    }

    
    public function testCheckFormsFromPatient()
    {
        try {
            $patientId = $this->paramsAtDb['id_patient'];
            $foundNum = TrustCare_Model_FrmCommunity::getNumberOfFormsForPatient($patientId, array('mapperOptions' => array('adapter' => $this->db)));
            $this->assertEqual($foundNum, 1, sprintf("Incorrect number of forms for patient.id=%s found: %%s", $patientId));
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("%s - unexpected exception: %s", __METHOD__, $ex->getMessage()));
        }
        
        try {
            $patientId = $this->paramsAtDb['id_patient'] * -1;
            $foundNum = TrustCare_Model_FrmCommunity::getNumberOfFormsForPatient($patientId, array('mapperOptions' => array('adapter' => $this->db)));
            $this->assertEqual($foundNum, 0, sprintf("Incorrect number of forms for patient.id=%s found: %%s", $patientId));
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("%s - unexpected exception: %s", __METHOD__, $ex->getMessage()));
        }
        
    }

    
    public function testCheckIsFirstVisitOfPatientToPharmacy()
    {
        $model = TrustCare_Model_FrmCommunity::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        if(!is_null($model)) {
            $model->delete();
        }
        
        $patientId = 1;
        $pharmacyId = 1;
        try {
            $params = array(
                    'id_pharmacy' => $pharmacyId,
            		'id_patient' => $patientId,
                    'date_of_visit' => '2012-07-02',
            );
            $model = new TrustCare_Model_FrmCommunity(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
            

            $ret = TrustCare_Model_FrmCommunity::isFirstVisitOfPatientToPharmacy($patientId, $pharmacyId, array('mapperOptions' => array('adapter' => $this->db)));
            $this->assertTrue($ret, "Haven't found existing visit of patent to pharmacy");

            $ret = TrustCare_Model_FrmCommunity::isFirstVisitOfPatientToPharmacy($patientId, -1 *$pharmacyId, array('mapperOptions' => array('adapter' => $this->db)));
            $this->assertFalse($ret, "Have found not-existing visit of patent to pharmacy");
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("%s - unexpected exception: %s", __METHOD__, $ex->getMessage()));
        }
    }
    
    /**
     * 
     * @param TrustCare_Model_FrmCommunity $model
     * @param array $params
     * @return void|void
     */
    private function _compareObjectAndParams($model, $params, $checkTime = true) {
        if (is_null($model)) {
            $this->assertTrue(false, "Entity not initialized");
            return;
        }
        if(is_null($params) || !is_array($params)) {
            $this->assertTrue(false, "Sample parameters not filled!");
            return;
        }

        $this->assertEqual($model->id, $params['id'], "Incorrect 'id': %s");
        $this->assertEqual($model->id_user, $params['id_user'], "Incorrect 'id_user': %s");
        $this->assertIdentical($model->is_commited, !empty($params['is_commited']) ? true : false, "Incorrect 'is_commited': %s");
        $this->assertEqual($model->id_pharmacy, $params['id_pharmacy'], "Incorrect 'id_pharmacy': %s");
        $this->assertEqual($model->id_patient, $params['id_patient'], "Incorrect 'id_patient': %s");
        if($checkTime) {
            $this->assertEqual($model->generation_date, $params['generation_date'], "Incorrect 'generation_date': %s");
            $this->assertEqual($model->date_of_visit, $params['date_of_visit'], "Incorrect 'date_of_visit': %s");
        }
        $this->assertEqual($model->date_of_visit_month_index, $params['date_of_visit_month_index'], "Incorrect 'date_of_visit_month_index': %s");
        $this->assertIdentical($model->is_first_visit_to_pharmacy, !empty($params['is_first_visit_to_pharmacy']) ? true : false, "Incorrect 'is_referred_in': %s");
        $this->assertIdentical($model->is_referred_in, !empty($params['is_referred_in']) ? true : false, "Incorrect 'is_referred_in': %s");
        $this->assertIdentical($model->is_referred_out, !empty($params['is_referred_out']) ? true : false, "Incorrect 'is_referred_out': %s");
        $this->assertIdentical($model->is_referral_completed, !empty($params['is_referral_completed']) ? true : false, "Incorrect 'is_referral_completed': %s");
        $this->assertIdentical($model->is_hiv_risk_assesment_done, !empty($params['is_hiv_risk_assesment_done']) ? true : false, "Incorrect 'is_hiv_risk_assesment_done': %s");
        $this->assertIdentical($model->is_htc_done, !empty($params['is_htc_done']) ? true : false, "Incorrect 'is_htc_done': %s");
        $this->assertIdentical($model->is_client_received_htc, !empty($params['is_client_received_htc']) ? true : false, "Incorrect 'is_client_received_htc': %s");
        $this->assertIdentical($model->is_htc_done_in_current_pharmacy, !empty($params['is_htc_done_in_current_pharmacy']) ? true : false, "Incorrect 'is_htc_done_in_current_pharmacy': %s");
        $this->assertEqual($model->htc_result_id, $params['htc_result_id'], "Incorrect 'htc_result_id': %s");
        $this->assertIdentical($model->is_palliative_services_to_plwha, !empty($params['is_palliative_services_to_plwha']) ? true : false, "Incorrect 'is_palliative_services_to_plwha': %s");
        $this->assertIdentical($model->is_sti_services, !empty($params['is_sti_services']) ? true : false, "Incorrect 'is_sti_services': %s");
        $this->assertIdentical($model->is_reproductive_health_services, !empty($params['is_reproductive_health_services']) ? true : false, "Incorrect 'is_reproductive_health_services': %s");
        $this->assertIdentical($model->is_tuberculosis_services, !empty($params['is_tuberculosis_services']) ? true : false, "Incorrect 'is_tuberculosis_services': %s");
        $this->assertIdentical($model->is_ovc_services, !empty($params['is_ovc_services']) ? true : false, "Incorrect 'is_ovc_services': %s");
        $this->assertIdentical($model->is_patient_younger_15, !empty($params['is_patient_younger_15']) ? true : false, "Incorrect 'is_patient_younger_15': %s");
        $this->assertIdentical($model->is_patient_male, !empty($params['is_patient_male']) ? true : false, "Incorrect 'is_patient_male': %s");
        $this->assertEqual($model->id_nafdac, $params['id_nafdac'], "Incorrect 'id_nafdac': %s");
        
    }
}

 