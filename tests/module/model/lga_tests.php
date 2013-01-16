<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */

class TestOfLga extends UnitTestCase {
    private $paramsAtDb = null;
    
    /**
     * 
     * @var Zend_Db
     */
    private $db = null;
    
    function setUp() {
        $this->db = Zend_Registry::get('dbAdapter');
        
        try {
            $fileName = sprintf("%s/_files/lga_test.sql", dirname(__FILE__));
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
                'id' => $this->db->nextSequenceId('lga_id_seq'),
                'name' => 'Test1',
                'id_state' => 1,        
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
            $query = sprintf("insert into lga(%s) values(%s);", join(",", $columns), join(",", $values));
            $res = $this->db->query($query);
            $this->paramsAtDb = $params;
        }
        catch(Exception $ex){}
    }


    function tearDown() {
        $query = sprintf("delete from lga;");
        $this->db->query($query);
        
        $query = sprintf("delete from state;");
        $this->db->query($query);
        
        $query = sprintf("update db_sequence set value=1 where name='lga_id_seq';");
        $this->db->query($query);
    }
    

    function testInitializing() {
        $params = array(
            'id' => '111',
            'name' => 'Test2',
            'id_state' => 2,
            'mapperOptions' => array('adapter' => $this->db)
        );
        
        $model = new TrustCare_Model_Lga($params);

        $this->_compareObjectAndParams($model, $params);
    }
    
    function testDefaultValues() {
    }

    function testLoadExistingById() {
        $model = TrustCare_Model_Lga::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->_compareObjectAndParams($model, $this->paramsAtDb);
    }

    function testLoadUnexistingById() {
        $model = TrustCare_Model_Lga::find(-1 * $this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        
        $this->assertNull($model, "This entity must not be loaded");
    }


    function testSaveNew() {
        $params = array(
            'name' => 'Test3',
            'id_state' => 1,
        );
        
        try {
            $model = new TrustCare_Model_Lga(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
            
            $id = $model->id;
            $params['id'] = $id;
            
            $model1 = TrustCare_Model_Lga::find($params['id'], array('mapperOptions' => array('adapter' => $this->db)));
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
            );
            $model = new TrustCare_Model_Lga(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
    
            $id = $model->id;
            $params['id'] = $id;
            $params['id_state'] = null;
    
            $model1 = TrustCare_Model_Lga::find($params['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->_compareObjectAndParams($model1, $params);
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Can't save new entity: %s", $ex->getMessage()));
        }
        
        /* Empty */
        try {
            $params = array(
                'name' => 'Test3',
                'id_state' => '',
            );
            $model = new TrustCare_Model_Lga(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
    
            $id = $model->id;
            $params['id'] = $id;
            $params['id_state'] = null;
    
            $model1 = TrustCare_Model_Lga::find($params['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->_compareObjectAndParams($model1, $params);
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Can't save new entity: %s", $ex->getMessage()));
        }
        
        /* Null */
        try {
            $params = array(
                    'name' => 'Test3',
                    'id_state' => null,
            );
            $model = new TrustCare_Model_Lga(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
        
            $id = $model->id;
            $params['id'] = $id;
            $params['id_state'] = null;
        
            $model1 = TrustCare_Model_Lga::find($params['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->_compareObjectAndParams($model1, $params);
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Can't save new entity: %s", $ex->getMessage()));
        }
        
    }
    
    function testChangeParameters() {
        $model = TrustCare_Model_Lga::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        if(!is_null($model)) {
            $params['name'] = $model->name . '22';
            $params['id_state'] = (2 == $model->id_state) ? 1 : 2;
             
            try {
                $model->setOptions($params);
                $model->save();
                
                $params['id'] = $model->id;
                
                $model1 = TrustCare_Model_Lga::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
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
        $model = TrustCare_Model_Lga::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        if(!is_null($model)) {
            $params['name'] = $model->name . '22';
            $params['id_state'] = '';
             
            try {
                $model->setOptions($params);
                $model->save();
    
                $params['id'] = $model->id;
                $params['id_state'] = null;
    
                $model1 = TrustCare_Model_Lga::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
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
        $model = TrustCare_Model_Lga::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        if(!is_null($model)) {
            $params['name'] = $model->name . '22';
            $params['id_state'] = null;
             
            try {
                $model->setOptions($params);
                $model->save();
    
                $params['id'] = $model->id;
    
                $model1 = TrustCare_Model_Lga::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
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
        $model = TrustCare_Model_Lga::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        try {
            if(is_null($model)) {
                throw new Exception("Entity not initialized");
            }
            $model->delete();
            
            $model1 = TrustCare_Model_Lga::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->assertNull($model1, "Entity hasn't been deleted: %s");
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Delete() thrown exception: %s", $ex->getMessage()));
        }
    }
    
    /**
     * 
     * @param TrustCare_Model_Lga $model
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
        $this->assertEqual($model->id_state, $params['id_state'], "Incorrect 'id_state': %s");
    }
}

 