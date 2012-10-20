<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */
 

class TrustCare_Model_Mapper_ReportCare extends TrustCare_Model_Mapper_Abstract
{
    /**
     * @param  TrustCare_Model_ReportCare $model 
     * @return void
     */
    public function save(TrustCare_Model_ReportCare &$model)
    {
        $data = array();
        if(!$model->isExists() || $model->isParameterChanged('generation_date')) {
            $data['generation_date'] = $model->getGenerationDate();
        }
        if(!$model->isExists() || $model->isParameterChanged('period')) {
            $data['period'] = $model->getPeriod();
        }
        if(!$model->isExists() || $model->isParameterChanged('id_pharmacy')) {
            $data['id_pharmacy'] = $model->getIdPharmacy();
        }
        if(!$model->isExists() || $model->isParameterChanged('number_of_clients_with_prescription_male_younger_15')) {
            $data['number_of_clients_with_prescription_male_younger_15'] = $model->getNumberOfClientsWithPrescriptionMaleYounger15();
        }
        if(!$model->isExists() || $model->isParameterChanged('number_of_clients_with_prescription_female_younger_15')) {
            $data['number_of_clients_with_prescription_female_younger_15'] = $model->getNumberOfClientsWithPrescriptionFemaleYounger15();
        }
        if(!$model->isExists() || $model->isParameterChanged('number_of_clients_with_prescription_male_from_15')) {
            $data['number_of_clients_with_prescription_male_from_15'] = $model->getNumberOfClientsWithPrescriptionMaleFrom15();
        }
        if(!$model->isExists() || $model->isParameterChanged('number_of_clients_with_prescription_female_from_15')) {
            $data['number_of_clients_with_prescription_female_from_15'] = $model->getNumberOfClientsWithPrescriptionFemaleFrom15();
        }
        if(!$model->isExists() || $model->isParameterChanged('number_of_dispensed_drugs')) {
            $data['number_of_dispensed_drugs'] = $model->getNumberOfDispensedDrugs();
        }
        if(!$model->isExists() || $model->isParameterChanged('filename')) {
            $data['filename'] = $model->getFilename();
        }
        
        if (null === ($id = $model->getId())) {
            unset($data['id']);
            $primaryKey = $this->getDbTable()->insert($data);
            $model->id = $primaryKey;
            $this->setLastOperationInsert(true);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
            $this->setLastOperationInsert(false);
        }
        $model->setObjectKeyInfo(array('id' => $model->getId()));
    }
    
    public function delete(TrustCare_Model_ReportCare $model)
    {
        $model->setObjectKeyInfo(array('id' => $model->getId()));
        if(!is_null($model->getId())) {
            $this->getDbTable()->delete(sprintf("id=%d", $model->getId()));
        }
    }

    /**
     * @param  int $id 
     * @param  TrustCare_Model_ReportCare $model 
     * @return void
     */
    public function find($id, TrustCare_Model_ReportCare $model)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return false;
        }
        $row = $result->current();
        $model->setSkipTrackChanges(true);
        $model->setId($row->id)
              ->setGenerationDate($row->generation_date)
              ->setPeriod($row->period)
              ->setIdPharmacy($row->id_pharmacy)
              ->setNumberOfClientsWithPrescriptionMaleYounger15($row->number_of_clients_with_prescription_male_younger_15)
              ->setNumberOfClientsWithPrescriptionFemaleYounger15($row->number_of_clients_with_prescription_female_younger_15)
              ->setNumberOfClientsWithPrescriptionMaleFrom15($row->number_of_clients_with_prescription_male_from_15)
              ->setNumberOfClientsWithPrescriptionFemaleFrom15($row->number_of_clients_with_prescription_female_from_15)
              ->setNumberOfDispensedDrugs($row->number_of_dispensed_drugs)
              ->setFilename($row->filename);
        $model->setSkipTrackChanges(false);
              
        return true;
    }

    /**
     * @return array
     */
    public function fetchAll(array $clauses = array())
    {
        $entries   = array();
        
        $where = array();
        $where[] = '1=1';
        foreach($clauses as $clause) {
            $where[] = $clause;
        }
        
        
        $query = sprintf("select id from %s where %s;", $this->getDbTable()->info(Zend_Db_Table_Abstract::NAME), join(' and ', $where));
        $this->getDbAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);
        $resultSet = $this->getDbAdapter()->fetchAll($query);
        foreach ($resultSet as $row) {
            $entry = new TrustCare_Model_ReportCare(array('mapperOptions' => array('adapter' => $this->getDbAdapter())));
            $this->find($row->id, $entry);
            
            $entries[] = $entry;
        }
        return $entries;
    }
}
