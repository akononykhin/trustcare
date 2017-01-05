<?php

class Adr_ReportsController extends ZendX_Controller_Action
{

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
    }

    public function listAction()
    {
        $offset = (int)$this->getRequest()->getParam('offset', 0);
        $quantity = (int)$this->getRequest()->getParam('quantity', 50);
        
        $list = array();
        try {
        	$pharmacyIds = array_keys(Zend_Registry::get("TrustCare_Registry_User")->getListOfAvailablePharmacies());
        	 
        	$db = Zend_Registry::getInstance()->dbAdapter;
        	$db->setFetchMode(Zend_Db::FETCH_ASSOC);
        	$select = $db->select()
        		->from('nafdac',
        			array(
        					'nafdac.id',
        					'date_of_visit' => new Zend_Db_Expr("date_format(nafdac.date_of_visit, '%Y-%m-%d')"),
        					'generation_date' => new Zend_Db_Expr("date_format(nafdac.generation_date, '%Y-%m-%d %H:%i:%s')")
        			))
        		->joinLeft(array('patient'), 'nafdac.id_patient = patient.id', array('patient_identifier' => 'patient.identifier'))
        		->joinLeft(array('pharmacy'), 'nafdac.id_pharmacy = pharmacy.id', array('pharmacy_name' => 'pharmacy.name'))
        		->where(sprintf("nafdac.id_pharmacy in (%s)", join(",", $pharmacyIds)));
        	
        	$select->order("id desc");
        	$select->limit($quantity, $offset);
        			
			$resultSet = $db->fetchAll($select);
			foreach ( $resultSet as $row ) {
				$list[] = array (
					'id' => $row['id'],
					'gd' => $this->convertTimeToUserTimezone($row['generation_date'], Zend_Registry::getInstance()->dateFormat),
					'vd' => $this->showDateAtSpecifiedFormat($row['date_of_visit']),
					'pi' => $row['patient_identifier'],
					'ph' => $row['pharmacy_name'],
				);
			}
        }
        catch(Exception $ex) {
            $this->getLogger()->error(sprintf("'%s': %s", Zend_Auth::getInstance()->getIdentity(), $ex->getMessage()));
        }
        
        $o = new stdClass();
        $o->list = $list;
        $this->_helper->json($o);
    }
    

}

