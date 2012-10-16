<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */

class TestOfFrmCare extends UnitTestCase {
    private $paramsAtDb = null;
    
    /**
     * 
     * @var Zend_Db
     */
    private $db = null;
    
    function setUp() {
        $this->db = Zend_Registry::get('dbAdapter');
        
        try {
            $fileName = sprintf("%s/_files/frm_care_test.sql", dirname(__FILE__));
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
                'id' => $this->db->nextSequenceId('frm_care_id_seq'),
                'id_patient' => 1,
                'date_of_visit' => '2012-05-01 12:30:45',
                'date_of_visit_month_index' => 201205,
                'is_pregnant' => false,
                'is_receive_prescription' => true,
                'is_med_error_screened' => false,
                'is_med_error_identified' => true,
                'is_med_adh_problem_screened' => false,
                'is_med_adh_problem_identified' => true,
                'is_adh_intervention_provided' => false,
                'is_adr_screened' => true,
                'is_adr_symptoms' => false,
                'adr_severity_id' => 1,
                'adr_start_date' => '2012-06-01 01:01:01',
                'adr_stop_date' => '2012-06-08 01:01:01',
                'is_adr_intervention_provided' => true,
                'is_nafdac_adr_filled' => false,
                'is_patient_younger_15' => true,
                'is_patient_male' => false,
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
            $query = sprintf("insert into frm_care(%s) values(%s);", join(",", $columns), join(",", $values));
            $res = $this->db->query($query);
            $this->paramsAtDb = $params;
        }
        catch(Exception $ex){}
    }


    function tearDown() {
        $query = sprintf("delete from frm_care;");
        $this->db->query($query);
        
        $query = sprintf("update db_sequence set value=1 where name='frm_care_id_seq';");
        $this->db->query($query);
        
        $query = sprintf("delete from patient;");
        $this->db->query($query);
    }
    

    function testInitializing() {
        $params = array(
                'id' => '1',
                'id_patient' => 1,
                'date_of_visit' => '2012-05-01 12:30:45',
                'date_of_visit_month_index' => 201206,
                'is_pregnant' => false,
                'is_receive_prescription' => true,
                'is_med_error_screened' => false,
                'is_med_error_identified' => true,
                'is_med_adh_problem_screened' => false,
                'is_med_adh_problem_identified' => true,
                'is_adh_intervention_provided' => false,
                'is_adr_screened' => true,
                'is_adr_symptoms' => false,
                'adr_severity_id' => 2,
        		'adr_start_date' => '2012-06-01 01:01:01',
                'adr_stop_date' => '2012-06-08 01:01:01',
                'is_adr_intervention_provided' => true,
                'is_nafdac_adr_filled' => false,
                'is_patient_younger_15' => true,
                'is_patient_male' => false,
                'mapperOptions' => array('adapter' => $this->db)
        );
        
        $model = new TrustCare_Model_FrmCare($params);

        $this->_compareObjectAndParams($model, $params);
    }
    
    function testDefaultValues() {
    }

    function testLoadExistingById() {
        $model = TrustCare_Model_FrmCare::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->_compareObjectAndParams($model, $this->paramsAtDb);
    }

    function testLoadUnexistingById() {
        $model = TrustCare_Model_FrmCare::find(-1 * $this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        
        $this->assertNull($model, "This entity must not be loaded");
    }


    function testSaveNew() {
        $params = array(
                'id_patient' => 2,
                'date_of_visit' => '2012-07-02 12:30:45',
                'is_pregnant' => true,
                'is_receive_prescription' => true,
                'is_med_error_screened' => false,
                'is_med_error_identified' => true,
                'is_med_adh_problem_screened' => false,
                'is_med_adh_problem_identified' => true,
                'is_adh_intervention_provided' => false,
                'is_adr_screened' => true,
                'is_adr_symptoms' => false,
                'adr_severity_id' => 3,
        		'adr_start_date' => '2012-06-01 01:01:01',
                'adr_stop_date' => '2012-06-08 01:01:01',
                'is_adr_intervention_provided' => true,
                'is_nafdac_adr_filled' => false,
                'is_patient_younger_15' => true,
                'is_patient_male' => false,
        );
        
        try {
            $model = new TrustCare_Model_FrmCare(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
            
            $id = $model->id;
            $params['id'] = $id;
            $params['date_of_visit_month_index'] = 201207; /* it's filled indirectly */
            
            $model1 = TrustCare_Model_FrmCare::find($params['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->_compareObjectAndParams($model1, $params);
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Can't save new entity: %s", $ex->getMessage()));
        }
    }
    
    function testChangeParameters() {
        $model = TrustCare_Model_FrmCare::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        if(!is_null($model)) {
            $dateOfVisit = '2011-03-01 01:01:01' == $model->date_of_visit ? '2011-04-02 01:01:01' : '2011-03-01 01:01:01';
        	$params['id_patient'] = 1 == $model->id_patient ? 2 : 1;
        	$params['date_of_visit'] = $dateOfVisit;
        	$params['is_pregnant'] = !$model->is_pregnant;
        	$params['is_receive_prescription'] = !$model->is_receive_prescription;
        	$params['is_med_error_screened'] = !$model->is_med_error_screened;
        	$params['is_med_error_identified'] = !$model->is_med_error_identified;
        	$params['is_med_adh_problem_screened'] = !$model->is_med_adh_problem_screened;
        	$params['is_med_adh_problem_identified'] = !$model->is_med_adh_problem_identified;
        	$params['is_adh_intervention_provided'] = !$model->is_adh_intervention_provided;
        	$params['is_adr_screened'] = !$model->is_adr_screened;
        	$params['is_adr_symptoms'] = !$model->is_adr_symptoms;
        	$params['adr_severity_id'] = $model->adr_severity_id == 1 ? 2 : 1;
            $params['adr_start_date'] = '2011-06-01 01:01:01' == $model->adr_start_date ? '2011-06-02 01:01:01' : '2011-06-01 01:01:01';
            $params['adr_stop_date'] = '2011-07-01 01:01:01' == $model->adr_stop_date ? '2011-07-02 01:01:01' : '2011-07-01 01:01:01';
            $params['is_adr_intervention_provided'] = !$model->is_adr_intervention_provided;
        	$params['is_nafdac_adr_filled'] = !$model->is_nafdac_adr_filled;
            $params['is_patient_younger_15'] = !$model->is_patient_younger_15;
            $params['is_patient_male'] = !$model->is_patient_male;
            
            try {
                $model->setOptions($params);
                $model->save();
                
                $params['id'] = $model->id;
                if(!preg_match('/^(\d{4})-(\d{2})-\d{2} \d{2}:\d{2}:\d{2}$/', $dateOfVisit, $matches)) {
                    throw new Exception(sprintf("Incorrect format of date_of_visit: %s", $dateOfVisit));
                }
                $params['date_of_visit_month_index'] = $matches[1].$matches[2];

                $model1 = TrustCare_Model_FrmCare::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
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
    
    public function testDelete() {
        $model = TrustCare_Model_FrmCare::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        try {
            $model->delete();
            
            $model1 = TrustCare_Model_FrmCare::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->assertNull($model1, "Entity hasn't been deleted: %s");
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Delete() thrown exception: %s", $ex->getMessage()));
        }
    }
    
    /**
     * 
     * @param TrustCare_Model_FrmCare $model
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
        $this->assertEqual($model->id_patient, $params['id_patient'], "Incorrect 'id_patient': %s");
        if($checkTime) {
            $this->assertEqual($model->date_of_visit, $params['date_of_visit'], "Incorrect 'date_of_visit': %s");
            $this->assertEqual($model->adr_start_date, $params['adr_start_date'], "Incorrect 'adr_start_date': %s");
            $this->assertEqual($model->adr_stop_date, $params['adr_stop_date'], "Incorrect 'adr_stop_date': %s");
        }
        $this->assertEqual($model->date_of_visit_month_index, $params['date_of_visit_month_index'], "Incorrect 'date_of_visit_month_index': %s");
        $this->assertIdentical($model->is_pregnant, !empty($params['is_pregnant']) ? true : false, "Incorrect 'is_pregnant': %s");
        $this->assertIdentical($model->is_receive_prescription, !empty($params['is_receive_prescription']) ? true : false, "Incorrect 'is_receive_prescription': %s");
        $this->assertIdentical($model->is_med_error_screened, !empty($params['is_med_error_screened']) ? true : false, "Incorrect 'is_med_error_screened': %s");
        $this->assertIdentical($model->is_med_error_identified, !empty($params['is_med_error_identified']) ? true : false, "Incorrect 'is_med_error_identified': %s");
        $this->assertIdentical($model->is_med_adh_problem_screened, !empty($params['is_med_adh_problem_screened']) ? true : false, "Incorrect 'is_med_adh_problem_screened': %s");
        $this->assertIdentical($model->is_med_adh_problem_identified, !empty($params['is_med_adh_problem_identified']) ? true : false, "Incorrect 'is_med_adh_problem_identified': %s");
        $this->assertIdentical($model->is_adh_intervention_provided, !empty($params['is_adh_intervention_provided']) ? true : false, "Incorrect 'is_adh_intervention_provided': %s");
        $this->assertIdentical($model->is_adr_screened, !empty($params['is_adr_screened']) ? true : false, "Incorrect 'is_adr_screened': %s");
        $this->assertIdentical($model->is_adr_symptoms, !empty($params['is_adr_symptoms']) ? true : false, "Incorrect 'is_adr_symptoms': %s");
        $this->assertEqual($model->adr_severity_id, $params['adr_severity_id'], "Incorrect 'adr_severity_id': %s");
        $this->assertIdentical($model->is_adr_intervention_provided, !empty($params['is_adr_intervention_provided']) ? true : false, "Incorrect 'is_adr_intervention_provided': %s");
        $this->assertIdentical($model->is_nafdac_adr_filled, !empty($params['is_nafdac_adr_filled']) ? true : false, "Incorrect 'is_nafdac_adr_filled': %s");
        $this->assertIdentical($model->is_patient_younger_15, !empty($params['is_patient_younger_15']) ? true : false, "Incorrect 'is_patient_younger_15': %s");
        $this->assertIdentical($model->is_patient_male, !empty($params['is_patient_male']) ? true : false, "Incorrect 'is_patient_male': %s");
        
    }
}

 