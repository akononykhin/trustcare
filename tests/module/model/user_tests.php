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
            $fileName = sprintf("%s/_files/user_test.sql", dirname(__FILE__));
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
                'id' => $this->db->nextSequenceId('user_id_seq'),
                'login' => 'admin1',
                'password' => md5('admin1_pwd'),
                'first_name' => 'First1_1',
                'last_name' => 'Last1_1',
                'role' => 'admin',
                'city' => 'City1',
                'address' => 'Addr1',
                'zip' => 'Zip1',
                'phone' => 'Phone1',
                'id_pharmacy' => 1,
                'id_country' => 1000,
                'id_state' => 2,
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
            $query = sprintf("insert into user(%s) values(%s);", join(",", $columns), join(",", $values));
            $res = $this->db->query($query);
            $this->paramsAtDb = $params;
        }
        catch(Exception $ex){}
    }


    function tearDown() {
        $query = sprintf("delete from user where id >= 10;");
        $this->db->query($query);
        
        $query = sprintf("update db_sequence set value=10 where name='user_id_seq';");
        $this->db->query($query);
        
        $query = sprintf("delete from country where id >= 500;");
        $this->db->query($query);
        
        $query = sprintf("delete from state;");
        $this->db->query($query);
        
        $query = sprintf("delete from pharmacy;");
        $this->db->query($query);
    }
    

    function testInitializing() {
        $params = array(
            'id' => '1',
            'login' => 'admin2',
            'password' => md5('admin2_pwd'),
            'first_name' => 'First1',
            'last_name' => 'Last1',
            'is_active' => 1,
            'role' => 'admin',
            'city' => 'City12',
            'address' => 'Addr2',
            'zip' => 'Zip1',
            'phone' => 'Phone1',
            'id_pharmacy' => 2,
            'id_country' => 1001,
            'id_state' => 1,
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
            'first_name' => 'First1_112',
            'last_name' => 'Last1_134',
            'is_active' => 0,
            'role' => 'superadmin',
            'city' => 'City123',
            'address' => 'Addr3',
            'zip' => 'Zip12',
            'phone' => 'Phone13',
            'id_pharmacy' => 1,
            'id_country' => 1001,
            'id_state' => 2,
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
            $params['first_name'] = $model->first_name . '112';
            $params['last_name'] = $model->last_name . '113';
            $params['is_active'] = ($model->is_active == 1) ? 0 : 1;
            $params['role'] = 'admin' == $model->role ? 'superadmin' : 'admin';
            $params['city'] = $model->city . '41';
            $params['address'] = $model->address . '42';
            $params['zip'] = $model->zip . '43';
            $params['phone'] = $model->phone . '44';
            $params['id_pharmacy'] = '1' == $model->id_pharmacy ? '2' : '1';
            $params['id_country'] = '1001' == $model->id_country ? '1000' : '1001';
            $params['id_state'] = '2' == $model->id_state ? '1' : '2';
            
            try {
                $model->setOptions($params);
                $model->save();
                $params['login'] = $oldLogin;                
                $params['id'] = $model->id;
                
                $model1 = TrustCare_Model_User::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
                $this->_compareObjectAndParams($model1, $params);
            }
            catch(Exception $ex) {
                $this->assertTrue(false, sprintf("Unexpected exception: %s", $ex->getMessage()));
            }

            
            /* Check null values */
            try {
                $params = array(
                    'id_pharmacy' => null,
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
        
        $this->assertEqual($model->id, $params['id'], "Incorrect 'id': %s");
        $this->assertEqual($model->login, $params['login'], "Incorrect 'login': %s");
        $this->assertEqual($model->password, $params['password'], "Incorrect 'password': %s");
        $this->assertEqual($model->first_name, $params['first_name'], "Incorrect 'first_name': %s");
        $this->assertEqual($model->last_name, $params['last_name'], "Incorrect 'last_name': %s");
        $this->assertEqual($model->role, $params['role'], "Incorrect 'role': %s");
        $this->assertEqual($model->city, $params['city'], "Incorrect 'city': %s");
        $this->assertEqual($model->address, $params['address'], "Incorrect 'address': %s");
        $this->assertEqual($model->zip, $params['zip'], "Incorrect 'zip': %s");
        $this->assertEqual($model->phone, $params['phone'], "Incorrect 'phone': %s");
        $this->assertEqual($model->id_pharmacy, $params['id_pharmacy'], "Incorrect 'id_pharmacy': %s");
        $this->assertEqual($model->id_country, $params['id_country'], "Incorrect 'id_country': %s");
        $this->assertEqual($model->id_state, $params['id_state'], "Incorrect 'id_state': %s");
        $this->assertIdentical($model->is_active, !empty($params['is_active']) ? true : false, "Incorrect 'is_active': %s");
    }
}

 