<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */

class TestOfLogAccess extends UnitTestCase {
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
                'time' => 'now()',
                'author' => 'admin1',
                'ip' => '1.1.1.1',
                'action' => 'test action',
            );

            $columns = array();
            $values = array();
            foreach($params as $key=>$value) {
                $columns[] =$this->db->quoteIdentifier($key);
                if('time' != $key) {
                    if(!is_bool($value) && !is_int($value)) {
                        $values[] = $this->db->quote($value);
                    }
                    else {
                        $values[] = $this->db->quote($value, Zend_Db::INT_TYPE);
                    }
                }
                else {
                    $values[] = $value;
                }
            }
            $query = sprintf("insert into log_access(%s) values(%s);", join(",", $columns), join(",", $values));
            $res = $this->db->query($query);
            $params['id'] = $this->db->lastInsertId();
            $this->paramsAtDb = $params;
        }
        catch(Exception $ex){}
    }


    function tearDown() {
        $query = sprintf("delete from log_access;");
        $this->db->query($query);
    }
    

    function testInitializing() {
        $params = array(
            'time' => 'now()',
            'author' => 'admin1',
            'ip' => '1.1.1.1',
            'action' => 'test action',
            'mapperOptions' => array('adapter' => $this->db)
            );
        
        $model = new TrustCare_Model_LogAccess($params);

        $this->_compareObjectAndParams($model, $params);
    }
    
    function testDefaultValues() {
        /* no default values */
    }

    function testLoadExistingById() {
        $model = TrustCare_Model_LogAccess::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->_compareObjectAndParams($model, $this->paramsAtDb);
    }

    function testLoadUnexistingById() {
        $model = TrustCare_Model_LogAccess::find(-1 * $this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
                
        $this->assertNull($model->id, "This entity must not be loaded");
    }

    function testSaveNew() {
        $params = array(
            'author' => 'admin2',
            'ip' => '1.1.1.2',
            'action' => 'test action2',
            'mapperOptions' => array('adapter' => $this->db),
        );
        
        try {
            $model = new TrustCare_Model_LogAccess($params);
            $model->save();
            $params['id'] = $model->id;

            $newObj = TrustCare_Model_LogAccess::find($params['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->_compareObjectAndParams($newObj, $params, false);
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Can't save new entity: %s", $ex->getMessage()));
        }

    }

    
    function testChangeParameters() {
        $newObj = TrustCare_Model_LogAccess::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));

        if(!is_null($newObj)) {

            $params = array();
            $params['action'] = $newObj->getAction() . '12';
            
            try {
                $newObj->setOptions($params);
                $newObj->save();
                
                $params['id'] = $newObj->id;

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
        $newObj = TrustCare_Model_LogAccess::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));

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
     * @param TrustCare_Model_LogAccess $model
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

        $this->assertEqual($model->author, $params['author'], "Incorrect author: %s");
        $this->assertEqual($model->ip, $params['ip'], "Incorrect ip: %s");
        $this->assertEqual($model->action, $params['action'], "Incorrect action: %s");
    }
}

 