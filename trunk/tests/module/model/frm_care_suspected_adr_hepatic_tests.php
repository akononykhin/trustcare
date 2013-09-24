<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */

class TestOfFrmCareSuspectedAdrHepatic extends UnitTestCase {
    private $paramsAtDb = null;
    
    /**
     * 
     * @var Zend_Db
     */
    private $db = null;
    
    function setUp() {
        $this->db = Zend_Registry::get('dbAdapter');
        
        try {
            $fileName = sprintf("%s/_files/frm_care_suspected_adr_hepatic_test.sql", dirname(__FILE__));
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
                'id' => $this->db->nextSequenceId('frm_care_suspected_adr_hepatic_id_seq'),
                'id_frm_care' => 1,
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
            $query = sprintf("insert into frm_care_suspected_adr_hepatic(%s) values(%s);", join(",", $columns), join(",", $values));
            $res = $this->db->query($query);
            $this->paramsAtDb = $params;
        }
        catch(Exception $ex){}
    }


    function tearDown() {
        $query = sprintf("delete from frm_care_suspected_adr_hepatic;");
        $this->db->query($query);
        
        $query = sprintf("update db_sequence set value=1 where name='frm_care_suspected_adr_hepatic_id_seq';");
        $this->db->query($query);
        
        $query = sprintf("delete from frm_care;");
        $this->db->query($query);
        
        $query = sprintf("delete from patient;");
        $this->db->query($query);
    }
    

    function testInitializing() {
        $params = array(
            'id' => '1',
            'id_frm_care' => 2,
            'id_pharmacy_dictionary' => 3,
            'mapperOptions' => array('adapter' => $this->db)
        );
        
        $model = new TrustCare_Model_FrmCareSuspectedAdrHepatic($params);

        $this->_compareObjectAndParams($model, $params);
    }
    
    function testDefaultValues() {
    }

    function testLoadExistingById() {
        $model = TrustCare_Model_FrmCareSuspectedAdrHepatic::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->_compareObjectAndParams($model, $this->paramsAtDb);
    }

    function testLoadUnexistingById() {
        $model = TrustCare_Model_FrmCareSuspectedAdrHepatic::find(-1 * $this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->assertNull($model, "This entity must not be loaded");
    }


    function testSaveNew() {
        $params = array(
            'id_frm_care' => 1,
            'id_pharmacy_dictionary' => 3,
            );
        
        try {
            $model = new TrustCare_Model_FrmCareSuspectedAdrHepatic(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
            
            $id = $model->id;
            $params['id'] = $id;
            
            $model1 = TrustCare_Model_FrmCareSuspectedAdrHepatic::find($params['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->_compareObjectAndParams($model1, $params);
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Can't save new entity: %s", $ex->getMessage()));
        }
    }
    
    function testChangeParameters() {
        $model = TrustCare_Model_FrmCareSuspectedAdrHepatic::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        if(!is_null($model)) {
            $params['id_frm_care'] = 1 == $model->id_frm_care ? 2 : 1;
            $params['id_pharmacy_dictionary'] = 2 == $model->id_pharmacy_dictionary ? 3 : 2;
             
            try {
                $model->setOptions($params);
                $model->save();
                
                $params['id'] = $model->id;
                
                $model1 = TrustCare_Model_FrmCareSuspectedAdrHepatic::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
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
    
    
    
    function testLoadAllForFrmCare() {
        /* clean rows */
        $model = TrustCare_Model_FrmCareSuspectedAdrHepatic::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        if(!is_null($model)) {
            $model->delete();
        }

        $id_frm_care = 2;
        $samples = array();
        try {
            $params = array(
                'id_frm_care' => 1,
                'id_pharmacy_dictionary' => 3,
            );
            $model = new TrustCare_Model_FrmCareSuspectedAdrHepatic(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
            
            $params = array(
                'id_frm_care' => $id_frm_care,
                'id_pharmacy_dictionary' => 1,
            );
            $model = new TrustCare_Model_FrmCareSuspectedAdrHepatic(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
            $params['id'] = $model->id;
            $samples[$params['id']] = $params;
            
            $params = array(
                'id_frm_care' => $id_frm_care,
                'id_pharmacy_dictionary' => 5,
            );
            $model = new TrustCare_Model_FrmCareSuspectedAdrHepatic(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
            $params['id'] = $model->id;
            $samples[$params['id']] = $params;
                        
            $params = array(
                'id_frm_care' => 1,
                'id_pharmacy_dictionary' => 6,
            );
            $model = new TrustCare_Model_FrmCareSuspectedAdrHepatic(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
                        
            
            $model = new TrustCare_Model_FrmCareSuspectedAdrHepatic(array('mapperOptions' => array('adapter' => $this->db)));
            $objs = $model->fetchAllForFrmCare($id_frm_care);
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
        $model = TrustCare_Model_FrmCareSuspectedAdrHepatic::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        try {
            if(is_null($model)) {
                throw new Exception("Entity not found");
            }
            $model->delete();
            
            $model1 = TrustCare_Model_FrmCareSuspectedAdrHepatic::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->assertNull($model1, "Entity hasn't been deleted: %s");
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Delete() thrown exception: %s", $ex->getMessage()));
        }
    }
    
    function testReplaceForFrmCare() {
        /* clean rows */
        $model = TrustCare_Model_FrmCareSuspectedAdrHepatic::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        if(!is_null($model)) {
            $model->delete();
        }
    
        $frmCareId = 2;
        $currentDictIds = array(1,2,4,7);
        $newDictIds = array(2,3,4,8);
        
        $samples = array();
        try {
            foreach($currentDictIds as $dictId) {
                $params = array(
                    'id_frm_care' => $frmCareId,
                    'id_pharmacy_dictionary' => $dictId,
                );
                $model = new TrustCare_Model_FrmCareSuspectedAdrHepatic(array('mapperOptions' => array('adapter' => $this->db)));
                $model->setOptions($params);
                $model->save();
            }
    
    
            TrustCare_Model_FrmCareSuspectedAdrHepatic::replaceForFrmCare($frmCareId, $newDictIds, array('mapperOptions' => array('adapter' => $this->db)));
    
            $model = new TrustCare_Model_FrmCareSuspectedAdrHepatic(array('mapperOptions' => array('adapter' => $this->db)));
            $objs = $model->fetchAllForFrmCare($frmCareId);
            $this->assertEqual(count($objs), count($newDictIds), "Incorrect number of objects loaded: %s");
    
            $checkDictIds = array();
            foreach($objs as $obj) {
                $checkDictIds[] = $obj->getIdPharmacyDictionary();
            }
            if(count($checkDictIds) == count($newDictIds)) {
                foreach($newDictIds as $dictId) {
                    $this->assertTrue(in_array($dictId, $checkDictIds), sprintf("Object with id_pharmacy_dictionary=%s not loaded", $dictId));
                }
            }
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Unexpected exception: %s", $ex->getMessage()));
        }
    }
    
    /**
     * 
     * @param TrustCare_Model_FrmCareSuspectedAdrHepatic $model
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
        $this->assertEqual($model->id_frm_care, $params['id_frm_care'], "Incorrect 'id_frm_care': %s");
        $this->assertEqual($model->id_pharmacy_dictionary, $params['id_pharmacy_dictionary'], "Incorrect 'id_pharmacy_dictionary': %s");
    }
}

 