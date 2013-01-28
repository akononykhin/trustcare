<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */
 

class TrustCare_Model_Mapper_Nafdac extends TrustCare_Model_Mapper_Abstract
{
    /**
     * @param  TrustCare_Model_Nafdac $model 
     * @return void
     */
    public function save(TrustCare_Model_Nafdac &$model)
    {
        $data = array();
        if(!$model->isExists() || $model->isParameterChanged('id_frm_care')) {
            $data['id_frm_care'] = $model->getIdFrmCare();
        }
        if(!$model->isExists() || $model->isParameterChanged('generation_date')) {
            $data['generation_date'] = $model->getGenerationDate();
        }
        if(!$model->isExists() || $model->isParameterChanged('filename')) {
            $data['filename'] = $model->getFilename();
        }
        if(!$model->isExists() || $model->isParameterChanged('adr_description')) {
            $data['adr_description'] = $model->getAdrDescription();
        }
        if(!$model->isExists() || $model->isParameterChanged('was_admitted')) {
            $data['was_admitted'] = $model->getWasAdmitted() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('was_hospitalization_prolonged')) {
            $data['was_hospitalization_prolonged'] = $model->getWasHospitalizationProlonged() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('treatment_of_reaction')) {
            $data['treatment_of_reaction'] = $model->getTreatmentOfReaction();
        }
        if(!$model->isExists() || $model->isParameterChanged('outcome_of_reaction_type')) {
            $data['outcome_of_reaction_type'] = $model->getOutcomeOfReactionType();
        }
        if(!$model->isExists() || $model->isParameterChanged('outcome_of_reaction_desc')) {
            $data['outcome_of_reaction_desc'] = $model->getOutcomeOfReactionDesc();
        }
        if(!$model->isExists() || $model->isParameterChanged('drug_brand_name')) {
            $data['drug_brand_name'] = $model->getDrugBrandName();
        }
        if(!$model->isExists() || $model->isParameterChanged('drug_generic_name')) {
            $data['drug_generic_name'] = $model->getDrugGenericName();
        }
        if(!$model->isExists() || $model->isParameterChanged('drug_batch_number')) {
            $data['drug_batch_number'] = $model->getDrugBatchNumber();
        }
        if(!$model->isExists() || $model->isParameterChanged('drug_nafdac_number')) {
            $data['drug_nafdac_number'] = $model->getDrugNafdacNumber();
        }
        if(!$model->isExists() || $model->isParameterChanged('drug_expiry_name')) {
            $data['drug_expiry_name'] = $model->getDrugExpiryName();
        }
        if(!$model->isExists() || $model->isParameterChanged('drug_manufactor')) {
            $data['drug_manufactor'] = $model->getDrugManufactor();
        }
        if(!$model->isExists() || $model->isParameterChanged('drug_indication_for_use')) {
            $data['drug_indication_for_use'] = $model->getDrugIndicationForUse();
        }
        if(!$model->isExists() || $model->isParameterChanged('drug_dosage')) {
            $data['drug_dosage'] = $model->getDrugDosage();
        }
        if(!$model->isExists() || $model->isParameterChanged('drug_route_of_administration')) {
            $data['drug_route_of_administration'] = $model->getDrugRouteOfAdministration();
        }
        if(!$model->isExists() || $model->isParameterChanged('drug_date_started')) {
            $data['drug_date_started'] = $model->getDrugDateStarted();
        }
        if(!$model->isExists() || $model->isParameterChanged('drug_date_stopped')) {
            $data['drug_date_stopped'] = $model->getDrugDateStopped();
        }
        if(!$model->isExists() || $model->isParameterChanged('reporter_name')) {
            $data['reporter_name'] = $model->getReporterName();
        }
        if(!$model->isExists() || $model->isParameterChanged('reporter_address')) {
            $data['reporter_address'] = $model->getReporterAddress();
        }
        if(!$model->isExists() || $model->isParameterChanged('reporter_profession')) {
            $data['reporter_profession'] = $model->getReporterProfession();
        }
        if(!$model->isExists() || $model->isParameterChanged('reporter_contact')) {
            $data['reporter_contact'] = $model->getReporterContact();
        }
        
        if(!count($data)) {
            return;
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
        $model->setObjectKeyInfo(
            array(
                'id' => $model->getId(),
                'id_frm_care' => $model->getIdFrmCare()
            )
        );
    }
    
    public function delete(TrustCare_Model_Nafdac $model)
    {
        $model->setObjectKeyInfo(
            array(
                'id' => $model->getId(),
                'id_frm_care' => $model->getIdFrmCare()
            )
        );
        if(!is_null($model->getId())) {
            $this->getDbTable()->delete(sprintf("id=%d", $model->getId()));
        }
    }

    
    
    private function _fillModelForFind(TrustCare_Model_Nafdac $model, $row)
    {
        $model->setSkipTrackChanges(true);
        $model->setId($row->id)
              ->setIdFrmCare($row->id_frm_care)
              ->setGenerationDate($row->generation_date)
              ->setFilename($row->filename)
              ->setAdrDescription($row->adr_description)
              ->setWasAdmitted($row->was_admitted)
              ->setWasHospitalizationProlonged($row->was_hospitalization_prolonged)
              ->setTreatmentOfReaction($row->treatment_of_reaction)
              ->setOutcomeOfReactionType($row->outcome_of_reaction_type)
              ->setOutcomeOfReactionDesc($row->outcome_of_reaction_desc)
              ->setDrugBrandName($row->drug_brand_name)
              ->setDrugGenericName($row->drug_generic_name)
              ->setDrugBatchNumber($row->drug_batch_number)
              ->setDrugNafdacNumber($row->drug_nafdac_number)
              ->setDrugExpiryName($row->drug_expiry_name)
              ->setDrugManufactor($row->drug_manufactor)
              ->setDrugIndicationForUse($row->drug_indication_for_use)
              ->setDrugDosage($row->drug_dosage)
              ->setDrugRouteOfAdministration($row->drug_route_of_administration)
              ->setDrugDateStarted($row->drug_date_started)
              ->setDrugDateStopped($row->drug_date_stopped)
              ->setReporterName($row->reporter_name)
              ->setReporterAddress($row->reporter_address)
              ->setReporterProfession($row->reporter_profession)
              ->setReporterContact($row->reporter_contact);
        $model->setSkipTrackChanges(false);
    }
    
    /**
     * @param  int $id 
     * @param  TrustCare_Model_Nafdac $model 
     * @return void
     */
    public function find($id, TrustCare_Model_Nafdac $model)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return false;
        }
        $row = $result->current();
        $this->_fillModelForFind($model, $row);
                      
        return true;
    }

    
    /**
     * @param  int $balue
     * @param  TrustCare_Model_Nafdac $model
     * @return void
     */
    public function findByIdFrmCare($value, TrustCare_Model_Nafdac $model)
    {
        $select = $this->getDbTable()->select();
        $select->from($this->getDbTable(), array('*'))
               ->where("id_frm_care=?", $value);
        $result = $this->getDbTable()->fetchAll($select);
        if (0 == count($result)) {
            return false;
        }
        $row = $result->current();
                
        $this->_fillModelForFind($model, $row);
    
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
        
        $query = sprintf("select * from %s where %s order by id desc;", $this->getDbTable()->info(Zend_Db_Table_Abstract::NAME), join(' and ', $where));
        $this->getDbAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);
        $resultSet = $this->getDbAdapter()->fetchAll($query);
        foreach ($resultSet as $row) {
            $entry = new TrustCare_Model_Nafdac(array('mapperOptions' => array('adapter' => $this->getDbAdapter())));
            $this->_fillModelForFind($entry, $row);
                        
            $entries[] = $entry;
        }
        return $entries;
    }
}
