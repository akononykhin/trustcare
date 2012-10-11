<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */

class TestOfPatient extends UnitTestCase {
    private $paramsAtDb = null;
    
    /**
     * 
     * @var Zend_Db
     */
    private $db = null;
    
    function setUp() {
        $this->db = Zend_Registry::get('dbAdapter');
        
        try {
            $fileName = sprintf("%s/_files/patient_test.sql", dirname(__FILE__));
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
                'id' => $this->db->nextSequenceId('patient_id_seq'),
            	'identifier' => 'physic1',
                'first_name' => 'First1_1',
                'last_name' => 'Last1_1',
                'id_country' => 1000,
                'id_state' => 2,
                'city' => 'City1',
                'address' => 'Addr1',
                'zip' => 'Zip1',
                'phone' => 'Phone1',
                'birthdate' => '2010-09-08 12:12:14',
                'id_physician' => 1,
                'is_male' => 0,
                'is_active' => 1,
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
            $query = sprintf("insert into patient(%s) values(%s);", join(",", $columns), join(",", $values));
            $res = $this->db->query($query);
            $this->paramsAtDb = $params;
        }
        catch(Exception $ex){}
    }


    function tearDown() {
        $query = sprintf("delete from patient;");
        $this->db->query($query);
        
        $query = sprintf("update db_sequence set value=1 where name='patient_id_seq';");
        $this->db->query($query);
        
        $query = sprintf("delete from country where id >= 500;");
        $this->db->query($query);
        
        $query = sprintf("delete from state;");
        $this->db->query($query);
        
        $query = sprintf("delete from physician;");
        $this->db->query($query);
    }
    

    function testInitializing() {
        $params = array(
                'id' => '1',
                'identifier' => 'physic1',
                'first_name' => 'First1_1',
                'last_name' => 'Last1_1',
                'id_country' => 1000,
                'id_state' => 2,
                'city' => 'City1',
                'address' => 'Addr1',
                'zip' => 'Zip1',
                'phone' => 'Phone1',
                'birthdate' => '2010-09-08 12:12:14',
                'id_physician' => 1,
                'is_male' => false,
                'is_active' => true,
                'mapperOptions' => array('adapter' => $this->db)
            );
        
        $model = new TrustCare_Model_Patient($params);

        $this->_compareObjectAndParams($model, $params);
    }
    
    function testDefaultValues() {
    }

    function testLoadExistingById() {
        $model = TrustCare_Model_Patient::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->_compareObjectAndParams($model, $this->paramsAtDb);
    }

    function testLoadUnexistingById() {
        $model = TrustCare_Model_Patient::find(-1 * $this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->assertNull($model, "This entity must not be loaded");
    }


    function testLoadExistingByIdentifier() {
        $model = TrustCare_Model_Patient::findByIdentifier($this->paramsAtDb['identifier'], array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->_compareObjectAndParams($model, $this->paramsAtDb);
    }

    function testLoadUnexistingByIdentifier() {
        $model = TrustCare_Model_Patient::findByIdentifier($this->paramsAtDb['identifier'] . '_unknown', array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->assertNull($model, "This entity must not be loaded");
    }

    
    function testSaveNew() {
        $params = array(
                'identifier' => 'physic12',
                'first_name' => 'First1_13',
                'last_name' => 'Last1_14',
                'id_country' => 1001,
                'id_state' => 1,
                'city' => 'City2',
                'address' => 'Addr13',
                'zip' => 'Zip14',
                'phone' => 'Phone15',
                'birthdate' => '2011-09-08 12:12:14',
                'id_physician' => 2,
                'is_male' => true,
                'is_active' => false,
        );
        
        try {
            $model = new TrustCare_Model_Patient(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
            
            $id = $model->id;
            $params['id'] = $id;
            
            $model1 = TrustCare_Model_Patient::find($params['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->_compareObjectAndParams($model1, $params);
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Can't save new entity: %s", $ex->getMessage()));
        }
    }

    function testChangeParameters() {
        $model = TrustCare_Model_Patient::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        if(!is_null($model)) {
        	$params['identifier'] = $model->identifier . '11';
            $params['first_name'] = $model->first_name . '112';
            $params['last_name'] = $model->last_name . '113';
            $params['id_country'] = '1001' == $model->id_country ? '1000' : '1001';
            $params['id_state'] = '2' == $model->id_state ? '1' : '2';
            $params['city'] = $model->city . '41';
            $params['address'] = $model->address . '42';
            $params['zip'] = $model->zip . '43';
            $params['phone'] = $model->phone . '44';
            $params['id_physician'] = '1' == $model->id_physician ? '2' : '1';
            $params['birthdate'] = '2011-01-01 11:11:03' == $model->birthdate ? '2011-01-02 11:11:03' : '2011-01-01 11:11:03'; 
            $params['is_male'] = ($model->is_male == 1) ? 0 : 1;
            $params['is_active'] = ($model->is_active == 1) ? 0 : 1;
            
            try {
                $model->setOptions($this->paramsAtDb);
                $model->save();
                
                $model1 = TrustCare_Model_Patient::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
                $this->_compareObjectAndParams($model1, $this->paramsAtDb);
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
        $model = TrustCare_Model_Patient::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        try {
            if(is_null($model)) {
                throw new Exception("Object not loaded!");
            }
            $model->delete();
            
            $model1 = TrustCare_Model_Patient::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->assertNull($model1, "Entity hasn't been deleted: %s");
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Delete() thrown exception: %s", $ex->getMessage()));
        }
    }
    
    /**
     * 
     * @param TrustCare_Model_Patient $model
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
        $this->assertEqual($model->identifier, $params['identifier'], "Incorrect 'identifier': %s");
        $this->assertEqual($model->first_name, $params['first_name'], "Incorrect 'first_name': %s");
        $this->assertEqual($model->last_name, $params['last_name'], "Incorrect 'last_name': %s");
        $this->assertEqual($model->id_country, $params['id_country'], "Incorrect 'id_country': %s");
        $this->assertEqual($model->id_state, $params['id_state'], "Incorrect 'id_state': %s");
        $this->assertEqual($model->city, $params['city'], "Incorrect 'city': %s");
        $this->assertEqual($model->address, $params['address'], "Incorrect 'address': %s");
        $this->assertEqual($model->zip, $params['zip'], "Incorrect 'zip': %s");
        $this->assertEqual($model->phone, $params['phone'], "Incorrect 'phone': %s");
        if($checkTime) {
            $this->assertEqual($model->birthdate, $params['birthdate'], "Incorrect 'birthdate': %s");
        }
        $this->assertEqual($model->id_physician, $params['id_physician'], "Incorrect 'id_physician': %s");
        $this->assertIdentical($model->is_male, !empty($params['is_male']) ? true : false, "Incorrect 'is_male': %s");
        $this->assertIdentical($model->is_active, !empty($params['is_active']) ? true : false, "Incorrect 'is_active': %s");
    }
}

 