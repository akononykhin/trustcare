<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */

class TestOfUser extends UnitTestCase {
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
                'id' => $this->db->nextSequenceId('user_id_seq'),
            	'login' => 'admin1',
                'password' => md5('admin1_pwd'),
                'is_active' => 1,
                'role' => 'admin'
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
            $query = sprintf("insert into user(%s) values(%s);", join(",", $columns), join(",", $values));
            $res = $this->db->query($query);
            $this->paramsAtDb = $params;
        }
        catch(Exception $ex){}
    }


    function tearDown() {
        $query = sprintf("delete from user;");
        $this->db->query($query);
    }
    

    function testInitializing() {
        $params = array(
            'login' => 'admin2',
            'password' => md5('admin2_pwd'),
            'is_active' => 1,
            'role' => 'admin',
            'mapperOptions' => array('adapter' => $this->db)
            );
        
        $model = new TrustCare_Model_User($params);

        $this->_compareObjectAndParams($model, $params);
    }
    
    function testDefaultValues() {
    }

    function testLoadExistingById() {
        $model = TrustCare_Model_User::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->_compareObjectAndParams($model, $this->paramsAtDb);
    }

    function testLoadUnexistingById() {
        $model = TrustCare_Model_User::find(-1 * $this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        
        $this->assertNull($model, "This entity must not be loaded");
    }


    function testLoadExistingByLogin() {
        $model = TrustCare_Model_User::findByLogin($this->paramsAtDb['login'], array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->_compareObjectAndParams($model, $this->paramsAtDb);
    }
    
    function testSaveNew() {
        $params = array(
            'login' => 'admin2',
            'password' => md5('admin2_pwd'),
            'is_active' => 0,
            'role' => 'superadmin'
            );
        
        try {
            $model = new TrustCare_Model_User(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
            
            $id = $model->id;
            $params['id'] = $id;
            
            $model1 = TrustCare_Model_User::find($params['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->_compareObjectAndParams($model1, $params);
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Can't save new entity: %s", $ex->getMessage()));
        }
    }

    function testChangeParameters() {
        $model = TrustCare_Model_User::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        if(!is_null($model)) {
        	$oldLogin = $model->login;

        	$params['login'] = $model->login . '11';
        	$params['password'] = $model->password . '22';
        	$params['is_active'] = ($model->is_active == 1) ? 0 : 1;
        	$params['role'] = 'admin' == $model->role ? 'superadmin' : 'admin';
        	 
            try {
                $model->setOptions($this->paramsAtDb);
                $model->save();
                $params['login'] = $oldLogin;
                
                $model1 = TrustCare_Model_User::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
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
        $model = TrustCare_Model_User::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        try {
            $model->delete();
            
            $model1 = TrustCare_Model_User::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->assertNull($model1, "Entity hasn't been deleted: %s");
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Delete() thrown exception: %s", $ex->getMessage()));
        }
    }
    
    /**
     * 
     * @param TrustCare_Model_User $model
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
        
        $this->assertEqual($model->login, $params['login'], "Incorrect login: %s");
        $this->assertEqual($model->password, $params['password'], "Incorrect password: %s");
        $this->assertIdentical($model->is_active, !empty($params['is_active']) ? true : false, "Incorrect is_active: %s");
        $this->assertEqual($model->role, $params['role'], "Incorrect role: %s");
    }
}

 