<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */

class TestOfFrmCommunity extends UnitTestCase {
    private $paramsAtDb = null;
    
    /**
     * 
     * @var Zend_Db
     */
    private $db = null;
    
    function setUp() {
        $this->db = Zend_Registry::get('dbAdapter');
        
        try {
            $fileName = sprintf("%s/_files/frm_community_test.sql", dirname(__FILE__));
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
                'id' => $this->db->nextSequenceId('frm_community_id_seq'),
                'id_patient' => 1,
                'date_of_visit' => '2012-05-01 12:30:45',
                'is_referred_in' => false,
                'is_referred_out' => true,
                'is_referral_completed' => false,
                'is_hiv_risk_assesment_done' => true,
                'is_htc_done' => false,
                'is_client_received_htc' => true,
                'is_htc_done_in_current_pharmacy' => false,
                'is_palliative_services_to_plwha' => true,
                'is_sti_services' => false,
                'is_reproductive_health_services' => true,
                'is_tuberculosis_services' => false,
                'is_ovc_services' => true,
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
            $query = sprintf("insert into frm_community(%s) values(%s);", join(",", $columns), join(",", $values));
            $res = $this->db->query($query);
            $this->paramsAtDb = $params;
        }
        catch(Exception $ex){}
    }


    function tearDown() {
        $query = sprintf("delete from frm_community;");
        $this->db->query($query);
        
        $query = sprintf("update db_sequence set value=1 where name='frm_community_id_seq';");
        $this->db->query($query);
        
        $query = sprintf("delete from patient;");
        $this->db->query($query);
    }
    

    function testInitializing() {
        $params = array(
            'id' => '1',
                'id_patient' => 1,
                'date_of_visit' => '2012-05-01 12:30:45',
                'is_referred_in' => true,
                'is_referred_out' => false,
                'is_referral_completed' => true,
                'is_hiv_risk_assesment_done' => false,
                'is_htc_done' => true,
                'is_client_received_htc' => false,
                'is_htc_done_in_current_pharmacy' => true,
                'is_palliative_services_to_plwha' => false,
                'is_sti_services' => true,
                'is_reproductive_health_services' => false,
                'is_tuberculosis_services' => true,
                'is_ovc_services' => false,
                'mapperOptions' => array('adapter' => $this->db)
        );
        
        $model = new TrustCare_Model_FrmCommunity($params);

        $this->_compareObjectAndParams($model, $params);
    }
    
    function testDefaultValues() {
    }

    function testLoadExistingById() {
        $model = TrustCare_Model_FrmCommunity::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        $this->_compareObjectAndParams($model, $this->paramsAtDb);
    }

    function testLoadUnexistingById() {
        $model = TrustCare_Model_FrmCommunity::find(-1 * $this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        
        $this->assertNull($model, "This entity must not be loaded");
    }


    function testSaveNew() {
        $params = array(
                'id_patient' => 2,
                'date_of_visit' => '2012-05-02 12:30:45',
                'is_referred_in' => true,
                'is_referred_out' => true,
                'is_referral_completed' => false,
                'is_hiv_risk_assesment_done' => true,
                'is_htc_done' => false,
                'is_client_received_htc' => true,
                'is_htc_done_in_current_pharmacy' => false,
                'is_palliative_services_to_plwha' => true,
                'is_sti_services' => false,
                'is_reproductive_health_services' => true,
                'is_tuberculosis_services' => false,
                'is_ovc_services' => true,
            );
        
        try {
            $model = new TrustCare_Model_FrmCommunity(array('mapperOptions' => array('adapter' => $this->db)));
            $model->setOptions($params);
            $model->save();
            
            $id = $model->id;
            $params['id'] = $id;
            
            $model1 = TrustCare_Model_FrmCommunity::find($params['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->_compareObjectAndParams($model1, $params);
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Can't save new entity: %s", $ex->getMessage()));
        }
    }
    
    function testChangeParameters() {
        $model = TrustCare_Model_FrmCommunity::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        if(!is_null($model)) {
        	$params['id_patient'] = 1 == $model->id_patient ? 2 : 1;
        	$params['date_of_visit'] = '2011-05-01 01:01:01' == $model->date_of_visit ? '2011-05-02 01:01:01' : '2011-05-01 01:01:01';
        	$params['is_referred_in'] = !$model->is_referred_in;
        	$params['is_referred_out'] = !$model->is_referred_out;
        	$params['is_referral_completed'] = !$model->is_referral_completed;
        	$params['is_hiv_risk_assesment_done'] = !$model->is_hiv_risk_assesment_done;
        	$params['is_htc_done'] = !$model->is_htc_done;
        	$params['is_client_received_htc'] = !$model->is_client_received_htc;
        	$params['is_htc_done_in_current_pharmacy'] = !$model->is_htc_done_in_current_pharmacy;
        	$params['is_palliative_services_to_plwha'] = !$model->is_palliative_services_to_plwha;
        	$params['is_sti_services'] = !$model->is_sti_services;
            $params['is_reproductive_health_services'] = !$model->is_reproductive_health_services;
        	$params['is_tuberculosis_services'] = !$model->is_tuberculosis_services;
        	$params['is_ovc_services'] = !$model->is_ovc_services;

            try {
                $model->setOptions($this->paramsAtDb);
                $model->save();
                
                $model1 = TrustCare_Model_FrmCommunity::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
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
        $model = TrustCare_Model_FrmCommunity::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
        
        try {
            $model->delete();
            
            $model1 = TrustCare_Model_FrmCommunity::find($this->paramsAtDb['id'], array('mapperOptions' => array('adapter' => $this->db)));
            $this->assertNull($model1, "Entity hasn't been deleted: %s");
        }
        catch(Exception $ex) {
            $this->assertTrue(false, sprintf("Delete() thrown exception: %s", $ex->getMessage()));
        }
    }
    
    /**
     * 
     * @param TrustCare_Model_FrmCommunity $model
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
        $this->assertEqual($model->id_patient, $params['id_patient'], "Incorrect 'id_patient': %s");
        if($checkTime) {
            $this->assertEqual($model->date_of_visit, $params['date_of_visit'], "Incorrect 'date_of_visit': %s");
        }
        $this->assertIdentical($model->is_referred_in, !empty($params['is_referred_in']) ? true : false, "Incorrect 'is_referred_in': %s");
        $this->assertIdentical($model->is_referred_out, !empty($params['is_referred_out']) ? true : false, "Incorrect 'is_referred_out': %s");
        $this->assertIdentical($model->is_referral_completed, !empty($params['is_referral_completed']) ? true : false, "Incorrect 'is_referral_completed': %s");
        $this->assertIdentical($model->is_hiv_risk_assesment_done, !empty($params['is_hiv_risk_assesment_done']) ? true : false, "Incorrect 'is_hiv_risk_assesment_done': %s");
        $this->assertIdentical($model->is_htc_done, !empty($params['is_htc_done']) ? true : false, "Incorrect 'is_htc_done': %s");
        $this->assertIdentical($model->is_client_received_htc, !empty($params['is_client_received_htc']) ? true : false, "Incorrect 'is_client_received_htc': %s");
        $this->assertIdentical($model->is_htc_done_in_current_pharmacy, !empty($params['is_htc_done_in_current_pharmacy']) ? true : false, "Incorrect 'is_htc_done_in_current_pharmacy': %s");
        $this->assertIdentical($model->is_palliative_services_to_plwha, !empty($params['is_palliative_services_to_plwha']) ? true : false, "Incorrect 'is_palliative_services_to_plwha': %s");
        $this->assertIdentical($model->is_sti_services, !empty($params['is_sti_services']) ? true : false, "Incorrect 'is_sti_services': %s");
        $this->assertIdentical($model->is_reproductive_health_services, !empty($params['is_reproductive_health_services']) ? true : false, "Incorrect 'is_reproductive_health_services': %s");
        $this->assertIdentical($model->is_tuberculosis_services, !empty($params['is_tuberculosis_services']) ? true : false, "Incorrect 'is_tuberculosis_services': %s");
        $this->assertIdentical($model->is_ovc_services, !empty($params['is_ovc_services']) ? true : false, "Incorrect 'is_ovc_services': %s");
        
    }
}

 