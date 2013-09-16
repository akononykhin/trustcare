<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */

class TestOfNafdac extends UnitTestCase {
    private $paramsAtDb = null;
    
    /**
     * 
     * @var Zend_Db
     */
    private $db = null;
    
    function setUp() {
        $this->db = Zend_Registry::get('dbAdapter');
        
        try {
            $fileName = sprintf("%s/_files/nafdac_test.sql", dirname(__FILE__));
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
                'id' => $this->db->nextSequenceId('nafdac_id_seq'),
                'generation_date' => '2012-10-01 11:23:45',
                'date_of_visit' => '2012-05-01',
                'id_user' => 21,
                'id_patient' => 1,
                'id_pharmacy' => 31,
                'filename' => '111',
                'adr_start_date' => '2012-06-01',
                'adr_stop_date' => '2012-06-08',
                'adr_description' => '222',
                'was_admitted' => false,
                'was_hospitalization_prolonged' => true,
                'duration_of_admission' => '555',
                'treatment_of_reaction' => '3',
                'outcome_of_reaction_type' => 4,
                'outcome_of_reaction_desc' => '555',
                'drug_brand_name' => '6',
                'drug_generic_name' => '7',
                'drug_batch_number' => '8',
                'drug_nafdac_number' => '9',
                'drug_expiry_name' => '10',
                'drug_manufactor' => '11',
                'drug_indication_for_use' => '12',
                'drug_dosage' => '13',
                'drug_route_of_administration' => '14',
                'drug_date_started' => '15',
                'drug_date_stopped' => '16',
                'reporter_name' => '17',
                'reporter_address' => '18',
                'reporter_profession' => '19',
                'reporter_contact' => '20',
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
            $query = sprintf("insert into nafdac(%s) values(%s);", join(",", $columns), join(",", $values));
            $res = $this->db->query($query);
            $this->paramsAtDb = $params;
        }
        catch(Exception $ex){}
    }


    function tearDown() {
        $query = sprintf("delete from nafdac;");
        $this->db->query($query);
        
        $query = sprintf("update db_sequence set value=1 where name='nafdac_id_seq';");
        $this->db->query($query);
        
        $query = sprintf("delete from patient;");
        $this->db->query($query);
        
        $query = sprintf("delete from pharmacy;");
        $this->db->query($query);
        
        $query = sprintf("delete from user;");
        $this->db->query($query);
    }
    

    function testInitializing() {
        $params = array(
            'id' => '1',
            'generation_date' => '2012-09-01 11:23:45',
            'date_of_visit' => '2012-05-01',
            'id_user' => 22,
            'id_patient' => 2,
            'id_pharmacy' => 32,
            'filename' => '111',
            'adr_start_date' => '2012-06-02',
            'adr_stop_date' => '2012-06-09',
            'adr_description' => '222',
            'was_admitted' => true,
            'was_hospitalization_prolonged' => false,
            'duration_of_admission' => '555',
            'treatment_of_reaction' => '3',
            'outcome_of_reaction_type' => 4,
            'outcome_of_reaction_desc' => '555',
            'drug_brand_name' => '6',
            'drug_generic_name' => '7',
            'drug_batch_number' => '8',
            'drug_nafdac_number' => '9',
            'drug_expiry_name' => '10',
            'drug_manufactor' => '11',
            'drug_indication_for_use' => '12',
            'drug_dosage' => '13',
            'drug_route_of_administration' => '14',
            'drug_date_started' => '15',
            'drug_date_stopped' => '16',
            'reporter_name' => '17',
            'reporter_address' => '18',
            'reporter_profession' => '19',
            'reporter_contact' => '20',
            'mapperOptions' => array('adapter' => $this->db)
        );
        
        $model = new TrustCare_Model_Nafdac($params);

        $this->_compareObjectAndParams($model, $params);
    }
    
    function testDefaultValues() {
    }

    function testLoadExistingById() {
        $model = TrustCare_Model_Nafdac::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->_compareObjectAndParams($model, $this->paramsAtDb);
    }

    function testLoadUnexistingById() {
        $model = TrustCare_Model_Nafdac::find(-1 * $this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->assertNull($model, "This entity must not be loaded");
    }

    
    function testSaveNew() {
        $params = array(
            'generation_date' => '2012-09-01 11:23:45',
            'date_of_visit' => '2012-07-02',
            'id_user' => 21,
            'id_patient' => 1,
            'id_pharmacy' => 31,
            'filename' => '111',
            'adr_start_date' => '2012-06-03',
            'adr_stop_date' => '2012-06-10',
            'adr_description' => '222',
            'was_admitted' => false,
            'was_hospitalization_prolonged' => true,
            'duration_of_admission' => '555',
            'treatment_of_reaction' => '3',
            'outcome_of_reaction_type' => 4,
            'outcome_of_reaction_desc' => '555',
            'drug_brand_name' => '6',
            'drug_generic_name' => '7',
            'drug_batch_number' => '8',
            'drug_nafdac_number' => '9',
            'drug_expiry_name' => '10',
            'drug_manufactor' => '11',
            'drug_indication_for_use' => '12',
            'drug_dosage' => '13',
            'drug_route_of_administration' => '14',
            'drug_date_started' => '15',
            'drug_date_stopped' => '16',
            'reporter_name' => '17',
            'reporter_address' => '18',
            'reporter_profession' => '19',
            'reporter_contact' => '20',
        );
        
        try {
            $model = new TrustCare_Model_Nafdac(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
            
            $id = $model->id;
            $params['id'] = $id;
            
            $model1 = TrustCare_Model_Nafdac::find($params['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->_compareObjectAndParams($model1, $params);
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Can't save new entity: %s", $ex->getMessage()));
        }
    }
    
    function testChangeParameters() {
        $model = TrustCare_Model_Nafdac::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        if(!is_null($model)) {
            $params['filename'] = $model->filename . '_1';
            
            try {
                $model->setOptions($params);
                $model->save();

                $this->assertTrue(false, sprintf("Trying to modify NAFDAC parameters must throw an exception"));
            }
            catch(Exception $ex) {
            }
        }
        else {
            $this->assertTrue(false, "Can't initialize object");
        }
    }
    
    
    public function testDelete() {
        $model = TrustCare_Model_Nafdac::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        try {
            if(is_null($model)) {
                throw new Exception('Entity not initialized');
            }
            $model->delete();
            
            $model1 = TrustCare_Model_Nafdac::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->assertNull($model1, "Entity hasn't been deleted: %s");
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Delete() thrown exception: %s", $ex->getMessage()));
        }
    }
    
    /**
     * 
     * @param TrustCare_Model_Nafdac $model
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
        if($checkTime) {
            $this->assertEqual($model->generation_date, $params['generation_date'], "Incorrect 'generation_date': %s");
            $this->assertEqual($model->date_of_visit, $params['date_of_visit'], "Incorrect 'date_of_visit': %s");
            $this->assertEqual($model->adr_start_date, $params['adr_start_date'], "Incorrect 'adr_start_date': %s");
            $this->assertEqual($model->adr_stop_date, $params['adr_stop_date'], "Incorrect 'adr_stop_date': %s");
        }
        $this->assertEqual($model->id_user, $params['id_user'], "Incorrect 'id_user': %s");
        $this->assertEqual($model->id_patient, $params['id_patient'], "Incorrect 'id_patient': %s");
        $this->assertEqual($model->id_pharmacy, $params['id_pharmacy'], "Incorrect 'id_pharmacy': %s");
        $this->assertEqual($model->filename, $params['filename'], "Incorrect 'filename': %s");
        $this->assertEqual($model->adr_description, $params['adr_description'], "Incorrect 'adr_description': %s");
        $this->assertIdentical($model->was_admitted, $params['was_admitted'], "Incorrect 'was_admitted': %s");
        $this->assertIdentical($model->was_hospitalization_prolonged, $params['was_hospitalization_prolonged'], "Incorrect 'was_hospitalization_prolonged': %s");
        $this->assertEqual($model->treatment_of_reaction, $params['treatment_of_reaction'], "Incorrect 'treatment_of_reaction': %s");
        $this->assertEqual($model->duration_of_admission, $params['duration_of_admission'], "Incorrect 'duration_of_admission': %s");
        $this->assertEqual($model->outcome_of_reaction_type, $params['outcome_of_reaction_type'], "Incorrect 'outcome_of_reaction_type': %s");
        $this->assertEqual($model->outcome_of_reaction_desc, $params['outcome_of_reaction_desc'], "Incorrect 'outcome_of_reaction_desc': %s");
        $this->assertEqual($model->drug_brand_name, $params['drug_brand_name'], "Incorrect 'drug_brand_name': %s");
        $this->assertEqual($model->drug_generic_name, $params['drug_generic_name'], "Incorrect 'drug_generic_name': %s");
        $this->assertEqual($model->drug_batch_number, $params['drug_batch_number'], "Incorrect 'drug_batch_number': %s");
        $this->assertEqual($model->drug_nafdac_number, $params['drug_nafdac_number'], "Incorrect 'drug_nafdac_number': %s");
        $this->assertEqual($model->drug_expiry_name, $params['drug_expiry_name'], "Incorrect 'drug_expiry_name': %s");
        $this->assertEqual($model->drug_manufactor, $params['drug_manufactor'], "Incorrect 'drug_manufactor': %s");
        $this->assertEqual($model->drug_indication_for_use, $params['drug_indication_for_use'], "Incorrect 'drug_indication_for_use': %s");
        $this->assertEqual($model->drug_dosage, $params['drug_dosage'], "Incorrect 'drug_dosage': %s");
        $this->assertEqual($model->drug_route_of_administration, $params['drug_route_of_administration'], "Incorrect 'drug_route_of_administration': %s");
        $this->assertEqual($model->drug_date_started, $params['drug_date_started'], "Incorrect 'drug_date_started': %s");
        $this->assertEqual($model->drug_date_stopped, $params['drug_date_stopped'], "Incorrect 'drug_date_stopped': %s");
        $this->assertEqual($model->reporter_name, $params['reporter_name'], "Incorrect 'reporter_name': %s");
        $this->assertEqual($model->reporter_address, $params['reporter_address'], "Incorrect 'reporter_address': %s");
        $this->assertEqual($model->reporter_profession, $params['reporter_profession'], "Incorrect 'reporter_profession': %s");
        $this->assertEqual($model->reporter_contact, $params['reporter_contact'], "Incorrect 'reporter_contact': %s");
        
        
        
    }
}

 