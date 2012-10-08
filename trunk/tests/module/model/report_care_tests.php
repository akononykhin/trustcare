<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */

class TestOfReportCare extends UnitTestCase {
    private $paramsAtDb = null;
    
    /**
     * 
     * @var Zend_Db
     */
    private $db = null;
    
    function setUp() {
        $this->db = Zend_Registry::get('dbAdapter');
        
        try {
            $fileName = sprintf("%s/_files/report_care_test.sql", dirname(__FILE__));
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
                'id' => $this->db->nextSequenceId('report_care_id_seq'),
            	'generation_date' => '2012-10-01 11:23:45',
                'period' => 201209,
                'id_pharmacy' => 1,
                'number_of_clients_with_prescription_male_younger_15' => 10,
                'number_of_clients_with_prescription_female_younger_15' => 15,
                'number_of_clients_with_prescription_male_from_15' => 25,
                'number_of_clients_with_prescription_female_from_15' => 30,
                'number_of_dispensed_drugs' => 140,
                'filename' => 'test 111',
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
            $query = sprintf("insert into report_care(%s) values(%s);", join(",", $columns), join(",", $values));
            $res = $this->db->query($query);
            $this->paramsAtDb = $params;
        }
        catch(Exception $ex){}
    }


    function tearDown() {
        $query = sprintf("delete from report_care;");
        $this->db->query($query);
        
        $query = sprintf("update db_sequence set value=1 where name='report_care_id_seq';");
        $this->db->query($query);
        
        $query = sprintf("delete from pharmacy;");
        $this->db->query($query);
    }
    

    function testInitializing() {
        $params = array(
            'id' => '1',
            'generation_date' => '2012-09-01 11:23:45',
            'period' => 201208,
            'id_pharmacy' => 2,
            'number_of_clients_with_prescription_male_younger_15' => 11,
            'number_of_clients_with_prescription_female_younger_15' => 16,
            'number_of_clients_with_prescription_male_from_15' => 26,
            'number_of_clients_with_prescription_female_from_15' => 31,
            'number_of_dispensed_drugs' => 141,
            'filename' => 'test 111231',
            'mapperOptions' => array('adapter' => $this->db)
        );
        
        $model = new TrustCare_Model_ReportCare($params);

        $this->_compareObjectAndParams($model, $params);
    }
    
    function testDefaultValues() {
    }

    function testLoadExistingById() {
        $model = TrustCare_Model_ReportCare::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->_compareObjectAndParams($model, $this->paramsAtDb);
    }

    function testLoadUnexistingById() {
        $model = TrustCare_Model_ReportCare::find(-1 * $this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        
        $this->assertNull($model, "This entity must not be loaded");
    }


    function testSaveNew() {
        $params = array(
            'generation_date' => '2012-09-01 11:23:45',
            'period' => 201208,
            'id_pharmacy' => 2,
            'number_of_clients_with_prescription_male_younger_15' => 11,
            'number_of_clients_with_prescription_female_younger_15' => 16,
            'number_of_clients_with_prescription_male_from_15' => 26,
            'number_of_clients_with_prescription_female_from_15' => 31,
            'number_of_dispensed_drugs' => 141,
            'filename' => 'test 111231',
        );
        
        try {
            $model = new TrustCare_Model_ReportCare(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
            
            $id = $model->id;
            $params['id'] = $id;
            
            $model1 = TrustCare_Model_ReportCare::find($params['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->_compareObjectAndParams($model1, $params);
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Can't save new entity: %s", $ex->getMessage()));
        }
    }

    function testChangeParameters() {
        $model = TrustCare_Model_ReportCare::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        if(!is_null($model)) {
        	$params['generation_date'] = $model->generation_date == '2012-09-01 01:01:01' ? '2012-10-01 01:01:01' : '2012-09-01 01:01:01';
        	$params['period'] = $model->period == 201208 ? 201209 : 201208;
            $params['number_of_clients_with_prescription_male_younger_15'] = $model->number_of_clients_with_prescription_male_younger_15 == 10 ? 20 : 10;
            $params['number_of_clients_with_prescription_female_younger_15'] = $model->number_of_clients_with_prescription_female_younger_15 == 11 ? 21 : 11;
        	$params['number_of_clients_with_prescription_male_from_15'] = $model->number_of_clients_with_prescription_male_from_15 == 12 ? 22 : 12;
            $params['number_of_clients_with_prescription_female_from_15'] = $model->number_of_clients_with_prescription_female_from_15 == 13 ? 23 : 13;
            $params['number_of_dispensed_drugs'] = $model->number_of_dispensed_drugs == 14 ? 24 : 14;
            $params['filename'] = $model->filename . '43';
            $params['id_pharmacy'] = '1' == $model->id_pharmacy ? '2' : '1';
            
            try {
                $model->setOptions($this->paramsAtDb);
                $model->save();
                
                $newFilename = $params['filename'];
                $params = $this->paramsAtDb;
                $params['filename'] = $newFilename;
                
                $model1 = TrustCare_Model_ReportCare::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
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
        $model = TrustCare_Model_ReportCare::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        try {
            if(is_null($model)) {
                throw new Exception("Object not loaded!");
            }
            $model->delete();
            
            $model1 = TrustCare_Model_ReportCare::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->assertNull($model1, "Entity hasn't been deleted: %s");
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Delete() thrown exception: %s", $ex->getMessage()));
        }
    }
    
    /**
     * 
     * @param TrustCare_Model_ReportCare $model
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
        if($checkTime) {
            $this->assertEqual($model->generation_date, $params['generation_date'], "Incorrect 'generation_date': %s");
        }
        $this->assertEqual($model->period, $params['period'], "Incorrect 'period': %s");
        $this->assertEqual($model->number_of_clients_with_prescription_male_younger_15, $params['number_of_clients_with_prescription_male_younger_15'], "Incorrect 'number_of_clients_with_prescription_male_younger_15': %s");
        $this->assertEqual($model->number_of_clients_with_prescription_female_younger_15, $params['number_of_clients_with_prescription_female_younger_15'], "Incorrect 'number_of_clients_with_prescription_female_younger_15': %s");
        $this->assertEqual($model->number_of_clients_with_prescription_male_from_15, $params['number_of_clients_with_prescription_male_from_15'], "Incorrect 'number_of_clients_with_prescription_male_from_15': %s");
        $this->assertEqual($model->number_of_clients_with_prescription_female_from_15, $params['number_of_clients_with_prescription_female_from_15'], "Incorrect 'number_of_clients_with_prescription_female_from_15': %s");
        $this->assertEqual($model->number_of_dispensed_drugs, $params['number_of_dispensed_drugs'], "Incorrect 'number_of_dispensed_drugs': %s");
        $this->assertEqual($model->filename, $params['filename'], "Incorrect 'filename': %s");
    }
}

 