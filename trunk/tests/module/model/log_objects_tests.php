<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */

class TestOfLogObjects extends UnitTestCase {
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
                'timestamp' => '2010-01-01 19:21:22',
                'author' => 'admin1',
                'from_ip' => '1.1.1.1',
                'stack' => '121212',
                'action' => 'test action',
                'object_name' => '12312312',
                'key_info' => '123123133532311'
            );

            $columns = array();
            $values = array();
            foreach($params as $key=>$value) {
                $columns[] =$this->db->quoteIdentifier($key);
                if(!is_bool($value) && !is_int($value)) {
                    $values[] = $this->db->quote($value);
                }
                else {
                    $values[] = $this->db->quote($value, Zend_Db::INT_TYPE);
                }
            }
            $query = sprintf("insert into log_objects(%s) values(%s);", join(",", $columns), join(",", $values));
            $res = $this->db->query($query);
            $params['id'] = $this->db->lastInsertId();
            $this->paramsAtDb = $params;
        }
        catch(Exception $ex){}
    }


    function tearDown() {
        $query = sprintf("delete from log_objects;");
        $this->db->query($query);
    }
    

    function testInitializing() {
        $params = array(
                'id' => '1',
                'timestamp' => '2010-01-01 19:21:22',
                'author' => 'admin1',
                'from_ip' => '1.1.1.1',
                'stack' => '121212',
                'action' => 'test action',
                'object_name' => '12312312',
                'key_info' => '123123133532311',
                'mapperOptions' => array('adapter' => $this->db)
            );
        
        $model = new TrustCare_Model_LogObjects($params);

        $this->_compareObjectAndParams($model, $params);
    }
    
    function testDefaultValues() {
        $params = array(
                'author' => 'admin1',
                'from_ip' => '1.1.1.1',
                'stack' => '121212',
                'action' => 'test action',
                'object_name' => '12312312',
                'key_info' => '123123133532311',
                'mapperOptions' => array('adapter' => $this->db)
            );
        
        $model = new TrustCare_Model_LogObjects($params);
        $params['id'] = null;
        $params['timestamp'] = null;

        $this->_compareObjectAndParams($model, $params);
    }

    function testLoadExistingById() {
        $model = TrustCare_Model_LogObjects::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->_compareObjectAndParams($model, $this->paramsAtDb);
    }

    function testLoadUnexistingById() {
        $model = TrustCare_Model_LogObjects::find(-1 * $this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->assertNull($model->id, "This entity must not be loaded");
    }

    function testSaveNew() {
        $params = array(
                'author' => 'admin12',
                'from_ip' => '1.1.12.1',
                'stack' => '1212123',
                'action' => 'test action3',
                'object_name' => '123123123',
                'key_info' => '1231231335323113',
                'mapperOptions' => array('adapter' => $this->db),
        );
        
        try {
            $model = new TrustCare_Model_LogObjects($params);
            $model->save();
            $params['id'] = $model->id;
            
            $newObj = TrustCare_Model_LogObjects::find($params['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->_compareObjectAndParams($newObj, $params, false);
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Can't save new entity: %s", $ex->getMessage()));
        }
            }

    function testChangeParameters() {
        $newObj = TrustCare_Model_LogObjects::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));

        if(!is_null($newObj)) {

            $params = array();
            $params['action'] = $newObj->getAction() . '12';
            
            try {
                $newObj->setOptions($params);
                $newObj->save();

                $this->assertTrue(false, sprintf("Update must throw an exception"));
            }
            catch(Exception $ex) {
            }
        }
        else {
            $this->assertTrue(false, "Can't initialize object");
        }
    }

    public function testDelete() {
        $newObj = TrustCare_Model_LogObjects::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));

        if(!is_null($newObj)) {
            try {
                $newObj->delete();
                $this->assertTrue(false, sprintf("Delete must throw an exception"));
                
            }
            catch(Exception $ex) {
            }
        }
        else {
            $this->assertTrue(false, "Can't initialize object");
        }
    }

    /**
     * 
     * @param TrustCare_Model_LogObjects $model
     * @param array $params
     * @return void|void
     */
    private function _compareObjectAndParams($model, $params, $checkTimestamp = true) {
            if (is_null($model)) {
            $this->assertTrue(false, "Entity not initialized");
            return;
        }
        if(is_null($params) || !is_array($params)) {
            $this->assertTrue(false, "Sample parameters not filled!");
            return;
        }
                
        $this->assertEqual($model->getId(), $params['id'], "Incorrect id: %s");
        if($checkTimestamp) {
            $this->assertEqual($model->getTimestamp(), $params['timestamp'], "Incorrect timestamp: %s");
        }
        $this->assertEqual($model->getAuthor(), $params['author'], "Incorrect author: %s");
        $this->assertEqual($model->getFromIp(), $params['from_ip'], "Incorrect from_ip: %s");
        $this->assertEqual($model->getStack(), $params['stack'], "Incorrect stack: %s");
        $this->assertEqual($model->getAction(), $params['action'], "Incorrect action: %s");
        $this->assertEqual($model->getObjectName(), $params['object_name'], "Incorrect object_name: %s");
        $this->assertEqual($model->getKeyInfo(), $params['key_info'], "Incorrect key_info: %s");
    }
}

 