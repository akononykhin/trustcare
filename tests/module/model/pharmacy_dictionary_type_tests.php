<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */

class TestOfPharmacyDictionaryType extends UnitTestCase {
    private $paramsAtDb = null;
    
    /**
     * 
     * @var Zend_Db
     */
    private $db = null;
    
    function setUp() {
        $this->db = Zend_Registry::get('dbAdapter');
        
        try {
            $params = array(
                'id' => $this->db->nextSequenceId('pharmacy_dictionary_type_id_seq'),
                'ordernum' => 101,
                'name' => 'Test1',
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
            $query = sprintf("insert into pharmacy_dictionary_type(%s) values(%s);", join(",", $columns), join(",", $values));
            $res = $this->db->query($query);
            $this->paramsAtDb = $params;
        }
        catch(Exception $ex){}
    }


    function tearDown() {
        $query = sprintf("delete from pharmacy_dictionary_type where id >= 100;");
        $this->db->query($query);
        
        $query = sprintf("update db_sequence set value=100 where name='pharmacy_dictionary_type_id_seq';");
        $this->db->query($query);
    }
    

    function testInitializing() {
        $params = array(
            'id' => '111',
            'ordernum' => 102,
            'name' => 'Test2',
            'mapperOptions' => array('adapter' => $this->db)
        );
        
        $model = new TrustCare_Model_PharmacyDictionaryType($params);

        $this->_compareObjectAndParams($model, $params);
    }
    
    function testDefaultValues() {
    }

    function testLoadExistingById() {
        $model = TrustCare_Model_PharmacyDictionaryType::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->_compareObjectAndParams($model, $this->paramsAtDb);
    }

    function testLoadUnexistingById() {
        $model = TrustCare_Model_PharmacyDictionaryType::find(-1 * $this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        
        $this->assertNull($model, "This entity must not be loaded");
    }


    function testSaveNew() {
        $params = array(
            'ordernum' => 15,
            'name' => 'Test3',
            );
        
        try {
            $model = new TrustCare_Model_PharmacyDictionaryType(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
            
            $id = $model->id;
            $params['id'] = $id;
            
            $model1 = TrustCare_Model_PharmacyDictionaryType::find($params['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->_compareObjectAndParams($model1, $params);
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Can't save new entity: %s", $ex->getMessage()));
        }
    }

    function testChangeParameters() {
        $model = TrustCare_Model_PharmacyDictionaryType::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        if(!is_null($model)) {
            $params['ordernum'] = 15 == $model->ordernum ? 16 : 15;
            $params['name'] = $model->name . '22';
             
            try {
                $model->setOptions($params);
                $model->save();
                
                $params['id'] = $model->id;
                
                $model1 = TrustCare_Model_PharmacyDictionaryType::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
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
        $model = TrustCare_Model_PharmacyDictionaryType::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        try {
            $model->delete();
            
            $model1 = TrustCare_Model_PharmacyDictionaryType::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->assertNull($model1, "Entity hasn't been deleted: %s");
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Delete() thrown exception: %s", $ex->getMessage()));
        }
    }
    
    /**
     * 
     * @param TrustCare_Model_PharmacyDictionaryType $model
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
        $this->assertEqual($model->ordernum, $params['ordernum'], "Incorrect 'ordernum': %s");
        $this->assertEqual($model->name, $params['name'], "Incorrect 'name': %s");
    }
}

 