<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */

class TestOfPharmacy extends UnitTestCase {
    private $paramsAtDb = null;
    
    /**
     * 
     * @var Zend_Db
     */
    private $db = null;
    
    function setUp() {
        $this->db = Zend_Registry::get('dbAdapter');
        
        try {
            $fileName = sprintf("%s/_files/pharmacy_test.sql", dirname(__FILE__));
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
                'id' => $this->db->nextSequenceId('pharmacy_id_seq'),
                'name' => 'pharm_1',
                'address' => 'Addr1',
                'id_lga' => 1,
                'id_country' => 1000,
                'id_state' => 2,
                'id_facility' => 1,
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
            $query = sprintf("insert into pharmacy(%s) values(%s);", join(",", $columns), join(",", $values));
            $res = $this->db->query($query);
            $this->paramsAtDb = $params;
        }
        catch(Exception $ex){}
    }


    function tearDown() {
        $query = sprintf("delete from pharmacy;");
        $this->db->query($query);
        
        $query = sprintf("update db_sequence set value=1 where name='pharmacy_id_seq';");
        $this->db->query($query);
        
        $query = sprintf("delete from country where id >= 500;");
        $this->db->query($query);
        
        $query = sprintf("delete from state;");
        $this->db->query($query);
        
        $query = sprintf("delete from facility;");
        $this->db->query($query);
        
        $query = sprintf("delete from lga;");
        $this->db->query($query);
    }
    

    function testInitializing() {
        $params = array(
                'id' => '1',
                'name' => 'pharm_1',
                'address' => 'Addr1',
                'id_lga' => 1,
                'id_country' => 1000,
                'id_state' => 2,
                'id_facility' => 1,
                'is_active' => 1,
                'mapperOptions' => array('adapter' => $this->db)
            );
        
        $model = new TrustCare_Model_Pharmacy($params);

        $this->_compareObjectAndParams($model, $params);
    }
    
    function testDefaultValues() {
    }

    function testLoadExistingById() {
        $model = TrustCare_Model_Pharmacy::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->_compareObjectAndParams($model, $this->paramsAtDb);
    }

    function testLoadUnexistingById() {
        $model = TrustCare_Model_Pharmacy::find(-1 * $this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->assertNull($model, "This entity must not be loaded");
    }

    function testLoadExistingByName() {
        $model = TrustCare_Model_Pharmacy::findByName($this->paramsAtDb['name'], array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->_compareObjectAndParams($model, $this->paramsAtDb);
    }

    
    function testLoadUnexistingByName() {
        $model = TrustCare_Model_Pharmacy::findByName($this->paramsAtDb['name'] . '_unknown', array('mapperOptions' => array('adapter' => $this->db)));
                
        $this->assertNull($model, "This entity must not be loaded");
    }
    
    function testSaveNew() {
        $params = array(
            'name' => 'pharm_2',
            'is_active' => 0,
            'address' => 'Addr3',
            'id_lga' => 1,
            'id_country' => 1001,
            'id_state' => 2,
            'id_facility' => 1
        );
        
        try {
            $model = new TrustCare_Model_Pharmacy(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
            
            $id = $model->id;
            $params['id'] = $id;
            
            $model1 = TrustCare_Model_Pharmacy::find($params['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->_compareObjectAndParams($model1, $params);
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Can't save new entity: %s", $ex->getMessage()));
        }
    }

    function testChangeParameters() {
        $model = TrustCare_Model_Pharmacy::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        if(!is_null($model)) {
            $params['name'] = $model->name . '11';
            $params['is_active'] = ($model->is_active == 1) ? 0 : 1;
            $params['address'] = $model->address . '42';
            $params['id_lga'] = '1' == $model->id_lga ? '2' : '1';
            $params['id_country'] = '1001' == $model->id_country ? '1000' : '1001';
            $params['id_state'] = '2' == $model->id_state ? '1' : '2';
            $params['id_facility'] = '1' == $model->id_facility ? '2' : '1';
            
            try {
                $model->setOptions($params);
                $model->save();
                
                $params['id'] = $model->id;
                
                $model1 = TrustCare_Model_Pharmacy::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
                $this->_compareObjectAndParams($model1, $params);
            }
            catch(Exception $ex) {
                $this->assertTrue(false, sprintf("Unexpected exception: %s", $ex->getMessage()));
            }
            
            /* Check null values */
            try {
                $params = array(
                    'id_lga' => null,
                    'id_facility' => null,
                    'id_country' => null,
                    'id_state' => null);
                $model->setOptions($params);
                $model->save();
                
                $model1 = TrustCare_Model_User::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
                $this->assertEqual($model1->id_country, $params['id_country'], "Incorrect 'id_country': %s");
                $this->assertEqual($model1->id_state, $params['id_state'], "Incorrect 'id_state': %s");
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
        $model = TrustCare_Model_Pharmacy::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        try {
            $model->delete();
            
            $model1 = TrustCare_Model_Pharmacy::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->assertNull($model1, "Entity hasn't been deleted: %s");
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Delete() thrown exception: %s", $ex->getMessage()));
        }
    }
    
    /**
     * 
     * @param TrustCare_Model_Pharmacy $model
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
        $this->assertEqual($model->name, $params['name'], "Incorrect 'name': %s");
        $this->assertEqual($model->address, $params['address'], "Incorrect 'address': %s");
        $this->assertEqual($model->id_lga, $params['id_lga'], "Incorrect 'id_lga': %s");
        $this->assertEqual($model->id_country, $params['id_country'], "Incorrect 'id_country': %s");
        $this->assertEqual($model->id_state, $params['id_state'], "Incorrect 'id_state': %s");
        $this->assertEqual($model->id_facility, $params['id_facility'], "Incorrect 'id_facility': %s");
        $this->assertIdentical($model->is_active, !empty($params['is_active']) ? true : false, "Incorrect 'is_active': %s");
    }
}

 