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
                'id_frm_care' => 3,
                'filename' => '111',
                'adr_description' => '222',
                'was_admitted' => false,
                'was_hospitalization_prolonged' => true,
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
        
        $query = sprintf("delete from frm_care;");
        $this->db->query($query);
        
        $query = sprintf("delete from patient;");
        $this->db->query($query);
    }
    

    function testInitializing() {
        $params = array(
            'id' => '1',
            'id_frm_care' => 2,
            'filename' => '111',
            'adr_description' => '222',
            'was_admitted' => true,
            'was_hospitalization_prolonged' => false,
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

    
    function testLoadExistingByIdFrmCare() {
        $model = TrustCare_Model_Nafdac::findByIdFrmCare($this->paramsAtDb['id_frm_care'], array('mapperOptions' => array('adapter' => $this->db)));
    
        $this->_compareObjectAndParams($model, $this->paramsAtDb);
    }
    
    function testLoadUnexistingByIdFrmCare() {
        $model = TrustCare_Model_Nafdac::findByIdFrmCare(-1 * $this->paramsAtDb['id_frm_care'], array('mapperOptions' => array('adapter' => $this->db)));
    
        $this->assertNull($model, "This entity must not be loaded");
    }
    
    function testSaveNew() {
        $params = array(
            'id_frm_care' => 2,
            'filename' => '111',
            'adr_description' => '222',
            'was_admitted' => false,
            'was_hospitalization_prolonged' => true,
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
            $params['id_frm_care'] = 2 == $model->id_frm_care ? 3 : 2;
            $params['filename'] = $model->filename . '_1';
            $params['adr_description'] = $model->adr_description . '_2';
            $params['was_admitted'] = !$model->was_admitted;
            $params['was_hospitalization_prolonged'] = !$model->was_hospitalization_prolonged;
            $params['treatment_of_reaction'] = $model->treatment_of_reaction . '_3';
            $params['outcome_of_reaction_type'] = 4 == $model->outcome_of_reaction_type ? 40 : 4;
            $params['outcome_of_reaction_desc'] = $model->outcome_of_reaction_desc . '_5';
            $params['drug_brand_name'] = $model->drug_brand_name . '_6';
            $params['drug_generic_name'] = $model->drug_generic_name . '_7';
            $params['drug_batch_number'] = $model->drug_batch_number . '_8';
            $params['drug_nafdac_number'] = $model->drug_nafdac_number . '_9';
            $params['drug_expiry_name'] = $model->drug_expiry_name . '_10';
            $params['drug_manufactor'] = $model->drug_manufactor . '_11';
            $params['drug_indication_for_use'] = $model->drug_indication_for_use . '_12';
            $params['drug_dosage'] = $model->drug_dosage . '_13';
            $params['drug_route_of_administration'] = $model->drug_route_of_administration . '_14';
            $params['drug_date_started'] = $model->drug_date_started . '_15';
            $params['drug_date_stopped'] = $model->drug_date_stopped . '_16';
            $params['reporter_name'] = $model->reporter_name . '_17';
            $params['reporter_address'] = $model->reporter_address . '_18';
            $params['reporter_profession'] = $model->reporter_profession . '_19';
            $params['reporter_contact'] = $model->reporter_profession . '_20';
            
            try {
                $model->setOptions($params);
                $model->save();
                
                $params['id'] = $model->id;
                
                $model1 = TrustCare_Model_Nafdac::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
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
        $this->assertEqual($model->id_frm_care, $params['id_frm_care'], "Incorrect 'id_frm_care': %s");
        $this->assertEqual($model->filename, $params['filename'], "Incorrect 'filename': %s");
        $this->assertEqual($model->adr_description, $params['adr_description'], "Incorrect 'adr_description': %s");
        $this->assertIdentical($model->was_admitted, $params['was_admitted'], "Incorrect 'was_admitted': %s");
        $this->assertIdentical($model->was_hospitalization_prolonged, $params['was_hospitalization_prolonged'], "Incorrect 'was_hospitalization_prolonged': %s");
        $this->assertEqual($model->treatment_of_reaction, $params['treatment_of_reaction'], "Incorrect 'treatment_of_reaction': %s");
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

 