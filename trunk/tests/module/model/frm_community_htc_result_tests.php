<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */

class TestOfFrmCommunityHtcResult extends UnitTestCase {
    private $paramsAtDb = null;
    
    /**
     * 
     * @var Zend_Db
     */
    private $db = null;
    
    function setUp() {
        $this->db = Zend_Registry::get('dbAdapter');
        
        try {
            $fileName = sprintf("%s/_files/frm_community_htc_result_test.sql", dirname(__FILE__));
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
                'id' => $this->db->nextSequenceId('frm_community_htc_result_id_seq'),
                'id_frm_community' => 1,
                'id_pharmacy_dictionary' => 2,
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
            $query = sprintf("insert into frm_community_htc_result(%s) values(%s);", join(",", $columns), join(",", $values));
            $res = $this->db->query($query);
            $this->paramsAtDb = $params;
        }
        catch(Exception $ex){}
    }


    function tearDown() {
        $query = sprintf("delete from frm_community_htc_result;");
        $this->db->query($query);
        
        $query = sprintf("update db_sequence set value=1 where name='frm_community_htc_result_id_seq';");
        $this->db->query($query);
        
        $query = sprintf("delete from frm_community;");
        $this->db->query($query);
        
        $query = sprintf("delete from patient;");
        $this->db->query($query);
    }
    

    function testInitializing() {
        $params = array(
            'id' => '1',
            'id_frm_community' => 2,
            'id_pharmacy_dictionary' => 3,
            'mapperOptions' => array('adapter' => $this->db)
        );
        
        $model = new TrustCare_Model_FrmCommunityHtcResult($params);

        $this->_compareObjectAndParams($model, $params);
    }
    
    function testDefaultValues() {
    }

    function testLoadExistingById() {
        $model = TrustCare_Model_FrmCommunityHtcResult::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->_compareObjectAndParams($model, $this->paramsAtDb);
    }

    function testLoadUnexistingById() {
        $model = TrustCare_Model_FrmCommunityHtcResult::find(-1 * $this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->assertNull($model, "This entity must not be loaded");
    }


    function testSaveNew() {
        $params = array(
            'id_frm_community' => 1,
            'id_pharmacy_dictionary' => 3,
            );
        
        try {
            $model = new TrustCare_Model_FrmCommunityHtcResult(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
            
            $id = $model->id;
            $params['id'] = $id;
            
            $model1 = TrustCare_Model_FrmCommunityHtcResult::find($params['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->_compareObjectAndParams($model1, $params);
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Can't save new entity: %s", $ex->getMessage()));
        }
    }
    
    function testChangeParameters() {
        $model = TrustCare_Model_FrmCommunityHtcResult::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        if(!is_null($model)) {
        	$params['id_frm_community'] = 1 == $model->id_frm_community ? 2 : 1;
        	$params['id_pharmacy_dictionary'] = 2 == $model->id_pharmacy_dictionary ? 3 : 2;
        	 
            try {
                $model->setOptions($this->paramsAtDb);
                $model->save();
                
                $model1 = TrustCare_Model_FrmCommunityHtcResult::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
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
    
    
    
    function testLoadAllForFrmCommunity() {
        // clean rows
        $model = TrustCare_Model_FrmCommunityHtcResult::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        $model->delete();

        $id_frm_community = 2;
        $samples = array();
        try {
            $params = array(
                'id_frm_community' => 1,
                'id_pharmacy_dictionary' => 3,
            );
            $model = new TrustCare_Model_FrmCommunityHtcResult(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
            
            $params = array(
                'id_frm_community' => $id_frm_community,
                'id_pharmacy_dictionary' => 1,
            );
            $model = new TrustCare_Model_FrmCommunityHtcResult(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
            $params['id'] = $model->id;
            $samples[$params['id']] = $params;
            
            $params = array(
                'id_frm_community' => $id_frm_community,
                'id_pharmacy_dictionary' => 5,
            );
            $model = new TrustCare_Model_FrmCommunityHtcResult(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
            $params['id'] = $model->id;
            $samples[$params['id']] = $params;
                        
            $params = array(
                'id_frm_community' => 1,
                'id_pharmacy_dictionary' => 6,
            );
            $model = new TrustCare_Model_FrmCommunityHtcResult(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
                        
            
            $model = new TrustCare_Model_FrmCommunityHtcResult(array('mapperOptions' => array('adapter' => $this->db)));
            $objs = $model->fetchAllForFrmCommunity($id_frm_community);
            $this->assertEqual(count($objs), count($samples), "Incorrect number of objects loaded: %s");
            
            $checkObjs = array();
            foreach($objs as $obj) {
                $checkObjs[$obj->getId()] = $obj;
            }
            if(count($CheckObjs) == count($samples)) {
                foreach($samples as $params) {
                    if(array_key_exists($params['id'], $checkObjs)) {
                        $this->_compareObjectAndParams($checkObjs[$params['id']], $params);
                    }
                    else {
                        $this->assertTrue(false, sprintf("Object %s not loaded", $params['id']));
                    }
                }
            }
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Unexpected exception: %s", $ex->getMessage()));
        }
    }
    
    public function testDelete() {
        $model = TrustCare_Model_FrmCommunityHtcResult::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        try {
            $model->delete();
            
            $model1 = TrustCare_Model_FrmCommunityHtcResult::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->assertNull($model1, "Entity hasn't been deleted: %s");
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Delete() thrown exception: %s", $ex->getMessage()));
        }
    }
    
    /**
     * 
     * @param TrustCare_Model_FrmCommunityHtcResult $model
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
        $this->assertEqual($model->id_frm_community, $params['id_frm_community'], "Incorrect 'id_frm_community': %s");
        $this->assertEqual($model->id_pharmacy_dictionary, $params['id_pharmacy_dictionary'], "Incorrect 'id_pharmacy_dictionary': %s");
    }
}

 