<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */

class TestOfNafdacDrug extends UnitTestCase {
    private $paramsAtDb = null;
    
    /**
     * 
     * @var Zend_Db
     */
    private $db = null;
    
    function setUp() {
        $this->db = Zend_Registry::get('dbAdapter');
        
        try {
            $fileName = sprintf("%s/_files/nafdac_drug_test.sql", dirname(__FILE__));
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
                'id' => $this->db->nextSequenceId('nafdac_drug_id_seq'),
                'id_nafdac' => 1,
                'name' => '11',
                'dosage' => '12',
                'batch'   => '100',
                'started' => '13',
                'stopped' => '14',
                'reason' => '15',
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
            $query = sprintf("insert into nafdac_drug(%s) values(%s);", join(",", $columns), join(",", $values));
            $res = $this->db->query($query);
            $this->paramsAtDb = $params;
        }
        catch(Exception $ex){}
    }


    function tearDown() {
        $query = sprintf("delete from nafdac_drug;");
        $this->db->query($query);
        
        $query = sprintf("update db_sequence set value=1 where name='nafdac_drug_id_seq';");
        $this->db->query($query);
        
        $query = sprintf("delete from nafdac;");
        $this->db->query($query);
        
        $query = sprintf("delete from patient;");
        $this->db->query($query);
        
        $query = sprintf("delete from pharmacy;");
        $this->db->query($query);
        
        $query = sprintf("delete from user;");
        $this->db->query($query);
    }
    

    function testInitializing() {
        $params = array(
            'id' => '1',
            'id_nafdac' => 2,
            'name' => '11',
            'dosage' => '12',
            'batch'   => '100',
            'started' => '13',
            'stopped' => '14',
            'reason' => '15',
            'mapperOptions' => array('adapter' => $this->db)
        );
        
        $model = new TrustCare_Model_NafdacDrug($params);

        $this->_compareObjectAndParams($model, $params);
    }
    
    function testDefaultValues() {
    }

    function testLoadExistingById() {
        $model = TrustCare_Model_NafdacDrug::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->_compareObjectAndParams($model, $this->paramsAtDb);
    }

    function testLoadUnexistingById() {
        $model = TrustCare_Model_NafdacDrug::find(-1 * $this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->assertNull($model, "This entity must not be loaded");
    }


    function testSaveNew() {
        $params = array(
            'id_nafdac' => 1,
            'name' => '11',
            'dosage' => '12',
            'batch'   => '100',
            'started' => '13',
            'stopped' => '14',
            'reason' => '15',
        );
        
        try {
            $model = new TrustCare_Model_NafdacDrug(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
            
            $id = $model->id;
            $params['id'] = $id;
            
            $model1 = TrustCare_Model_NafdacDrug::find($params['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->_compareObjectAndParams($model1, $params);
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Can't save new entity: %s", $ex->getMessage()));
        }
    }
    
    function testChangeParameters() {
        $model = TrustCare_Model_NafdacDrug::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        if(!is_null($model)) {
            $params['id_nafdac'] = 1 == $model->id_nafdac ? 2 : 1;
            $params['name'] = $model->name . '_11';
            $params['dosage'] = $model->dosage . '_12';
            $params['batch'] = $model->batch . '_100';
            $params['started'] = $model->started . '_13';
            $params['stopped'] = $model->stopped . '_14';
            $params['reason'] = $model->reason . '_15';
            
            try {
                $model->setOptions($params);
                $model->save();
                
                $params['id'] = $model->id;
                
                $model1 = TrustCare_Model_NafdacDrug::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
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
    
    
    
    function testLoadAllForNafdac() {
        /* clean rows */
        $model = TrustCare_Model_NafdacDrug::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        if(!is_null($model)) {
            $model->delete();
        }

        $id_nafdac = 2;
        $samples = array();
        try {
            $params = array(
                'id_nafdac' => 1,
            );
            $model = new TrustCare_Model_NafdacDrug(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
            
            $params = array(
                'id_nafdac' => $id_nafdac,
            );
            $model = new TrustCare_Model_NafdacDrug(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
            $params['id'] = $model->id;
            $samples[$params['id']] = $params;
            
            $params = array(
                'id_nafdac' => $id_nafdac,
            );
            $model = new TrustCare_Model_NafdacDrug(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
            $params['id'] = $model->id;
            $samples[$params['id']] = $params;
                        
            $params = array(
                'id_nafdac' => 1,
            );
            $model = new TrustCare_Model_NafdacDrug(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
                        
            
            $model = new TrustCare_Model_NafdacDrug(array('mapperOptions' => array('adapter' => $this->db)));
            $objs = $model->fetchAllByIdNafdac($id_nafdac);
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
        $model = TrustCare_Model_NafdacDrug::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        try {
            if(is_null($model)) {
                throw new Exception("Entity not initialized");
            }
            $model->delete();
            
            $model1 = TrustCare_Model_NafdacDrug::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->assertNull($model1, "Entity hasn't been deleted: %s");
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Delete() thrown exception: %s", $ex->getMessage()));
        }
    }
    
    /**
     * 
     * @param TrustCare_Model_NafdacDrug $model
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
        $this->assertEqual($model->id_nafdac, $params['id_nafdac'], "Incorrect 'id_nafdac': %s");
        $this->assertEqual($model->name, $params['name'], "Incorrect 'name': %s");
        $this->assertEqual($model->dosage, $params['dosage'], "Incorrect 'dosage': %s");
        $this->assertEqual($model->batch, $params['batch'], "Incorrect 'batch': %s");
        $this->assertEqual($model->name, $params['name'], "Incorrect 'name': %s");
        $this->assertEqual($model->stopped, $params['stopped'], "Incorrect 'stopped': %s");
        $this->assertEqual($model->reason, $params['reason'], "Incorrect 'reason': %s");
    }
}

 