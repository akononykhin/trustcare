<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */

class TestOfFacility extends UnitTestCase {
    private $paramsAtDb = null;
    
    /**
     * 
     * @var Zend_Db
     */
    private $db = null;
    
    function setUp() {
        $this->db = Zend_Registry::get('dbAdapter');
        
        try {
            $fileName = sprintf("%s/_files/facility_test.sql", dirname(__FILE__));
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
                'id' => $this->db->nextSequenceId('facility_id_seq'),
                'name' => 'Test1',
                'sn' => '12345',
                'id_lga' => 1,
                'id_facility_type' => 11,
                'id_facility_level' => 101,
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
            $query = sprintf("insert into facility(%s) values(%s);", join(",", $columns), join(",", $values));
            $res = $this->db->query($query);
            $this->paramsAtDb = $params;
        }
        catch(Exception $ex){}
    }


    function tearDown() {
        $query = sprintf("delete from facility;");
        $this->db->query($query);
        
        $query = sprintf("update db_sequence set value=1 where name='facility_id_seq';");
        $this->db->query($query);
        
        $query = sprintf("delete from lga;");
        $this->db->query($query);
        
        $query = sprintf("delete from facility_type;");
        $this->db->query($query);
        
        $query = sprintf("delete from facility_level;");
        $this->db->query($query);
        
    }
    

    function testInitializing() {
        $params = array(
            'id' => '111',
            'name' => 'Test2',
            'sn' => '12345',
            'id_lga' => 1,
            'id_facility_type' => 11,
            'id_facility_level' => 101,
            'mapperOptions' => array('adapter' => $this->db)
        );
        
        $model = new TrustCare_Model_Facility($params);

        $this->_compareObjectAndParams($model, $params);
    }
    
    function testDefaultValues() {
    }

    function testLoadExistingById() {
        $model = TrustCare_Model_Facility::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->_compareObjectAndParams($model, $this->paramsAtDb);
    }

    function testLoadUnexistingById() {
        $model = TrustCare_Model_Facility::find(-1 * $this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        
        $this->assertNull($model, "This entity must not be loaded");
    }


    function testSaveNew() {
        $params = array(
            'name' => 'Test3',
            'sn' => '123456',
            'id_lga' => 2,
            'id_facility_type' => 12,
            'id_facility_level' => 102,
        );
        
        try {
            $model = new TrustCare_Model_Facility(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
            
            $id = $model->id;
            $params['id'] = $id;
            
            $model1 = TrustCare_Model_Facility::find($params['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->_compareObjectAndParams($model1, $params);
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Can't save new entity: %s", $ex->getMessage()));
        }
    }
    
    
    function testSaveNewWithNullValues() {
        /* Missing */
        try {
            $params = array(
                'name' => 'Test3',
                'sn' => '123456',
            );
            $model = new TrustCare_Model_Facility(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
    
            $id = $model->id;
            $params['id'] = $id;
            $params['id_lga'] = null;
            $params['id_facility_type'] = null;
            $params['id_facility_level'] = null;
            
            $model1 = TrustCare_Model_Facility::find($params['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->_compareObjectAndParams($model1, $params);
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Can't save new entity: %s", $ex->getMessage()));
        }
    
        /* Empty */
        try {
            $params = array(
                'name' => 'Test3',
                'sn' => '123456',
                'id_lga' => '',
                'id_facility_type' => '',
                'id_facility_level' => '',
            );
            $model = new TrustCare_Model_Facility(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
    
            $id = $model->id;
            $params['id'] = $id;
            $params['id_lga'] = null;
            $params['id_facility_type'] = null;
            $params['id_facility_level'] = null;
            
            $model1 = TrustCare_Model_Facility::find($params['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->_compareObjectAndParams($model1, $params);
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Can't save new entity: %s", $ex->getMessage()));
        }
    
        /* Null */
        try {
            $params = array(
                'name' => 'Test3',
                'sn' => '123456',
                'id_lga' => null,
                'id_facility_type' => null,
                'id_facility_level' => null,
            );
            $model = new TrustCare_Model_Facility(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
    
            $id = $model->id;
            $params['id'] = $id;
            $params['id_lga'] = null;
            $params['id_facility_type'] = null;
            $params['id_facility_level'] = null;
            
            $model1 = TrustCare_Model_Facility::find($params['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->_compareObjectAndParams($model1, $params);
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Can't save new entity: %s", $ex->getMessage()));
        }
    
    }
    

    function testChangeParameters() {
        $model = TrustCare_Model_Facility::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        if(!is_null($model)) {
            $params['name'] = $model->name . '22';
            $params['sn'] = $model->sn . '33';
            $params['id_lga'] = (1 == $model->id_lga) ? 2 : 1;
            $params['id_facility_type'] = (11 == $model->id_facility_type) ? 12 : 11;
            $params['id_facility_level'] = (101 == $model->id_facility_level) ? 102 : 101;
            
            try {
                $model->setOptions($params);
                $model->save();
                
                $params['id'] = $model->id;
                
                $model1 = TrustCare_Model_Facility::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
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
    
    
    function testChangeParametersToEmpty() {
        $model = TrustCare_Model_Facility::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        if(!is_null($model)) {
            $params['name'] = $model->name . '22';
            $params['sn'] = $model->sn . '33';
            $params['id_lga'] = '';
            $params['id_facility_type'] = '';
            $params['id_facility_level'] = '';
            
            try {
                $model->setOptions($params);
                $model->save();
    
                $params['id'] = $model->id;
                $params['id_lga'] = null;
                $params['id_facility_type'] = null;
                $params['id_facility_level'] = null;
                
                $model1 = TrustCare_Model_Facility::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
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
    
    function testChangeParametersToNull() {
        $model = TrustCare_Model_Facility::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        if(!is_null($model)) {
            $params['name'] = $model->name . '22';
            $params['sn'] = $model->sn . '33';
            $params['id_lga'] = null;
            $params['id_facility_type'] = null;
            $params['id_facility_level'] = null;
            
            try {
                $model->setOptions($params);
                $model->save();
    
                $params['id'] = $model->id;
    
                $model1 = TrustCare_Model_Facility::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
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
        $model = TrustCare_Model_Facility::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        try {
            $model->delete();
            
            $model1 = TrustCare_Model_Facility::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->assertNull($model1, "Entity hasn't been deleted: %s");
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Delete() thrown exception: %s", $ex->getMessage()));
        }
    }
    
    /**
     * 
     * @param TrustCare_Model_Facility $model
     * @param array $params
     * @return void|void
     */
    private function _compareObjectAndParams($model, $params) {
        if (is_null($model)) {
            $this->assertTrue(false, "Entity not initialized");
            return;
        }
        if(is_null($params) || !is_array($params)) {
            $this->assertTrue(false, "Sample parameters not filled!");
            return;
        }
        
        $this->assertEqual($model->id, $params['id'], "Incorrect 'id': %s");
        $this->assertEqual($model->id_lga, $params['id_lga'], "Incorrect 'id_lga': %s");
        $this->assertEqual($model->name, $params['name'], "Incorrect 'name': %s");
        $this->assertEqual($model->sn, $params['sn'], "Incorrect 'sn': %s");
        $this->assertEqual($model->id_facility_type, $params['id_facility_type'], "Incorrect 'id_facility_type': %s");
        $this->assertEqual($model->id_facility_level, $params['id_facility_level'], "Incorrect 'id_facility_level': %s");
    }
}

 