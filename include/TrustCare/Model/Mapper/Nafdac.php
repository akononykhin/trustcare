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
        if(!$model->isExists() || $model->isParameterChanged('id_user')) {
            $data['id_user'] = $model->getIdUser();
        }
        if(!$model->isExists() || $model->isParameterChanged('id_patient')) {
            $data['id_patient'] = $model->getIdPatient();
        }
        if(!$model->isExists() || $model->isParameterChanged('id_pharmacy')) {
            $data['id_pharmacy'] = $model->getIdPharmacy();
        }
        if(!$model->isExists() || $model->isParameterChanged('date_of_visit')) {
            $data['date_of_visit'] = $model->getDateOfVisit();
        }
        if(!$model->isExists() || $model->isParameterChanged('generation_date')) {
            $data['generation_date'] = $model->getGenerationDate();
        }
        if(!$model->isExists() || $model->isParameterChanged('filename')) {
            $data['filename'] = $model->getFilename();
        }
        if(!$model->isExists() || $model->isParameterChanged('adr_start_date')) {
            $data['adr_start_date'] = $model->getAdrStartDate();
        }
        if(!$model->isExists() || $model->isParameterChanged('adr_stop_date')) {
            $data['adr_stop_date'] = $model->getAdrStopDate();
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
        if(!$model->isExists() || $model->isParameterChanged('duration_of_admission')) {
            $data['duration_of_admission'] = $model->getDurationOfAdmission();
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
        if(!$model->isExists() || $model->isParameterChanged('reporter_email')) {
            $data['reporter_email'] = $model->getReporterEmail();
        }
        if(!$model->isExists() || $model->isParameterChanged('onset_time')) {
            $data['onset_time'] = $model->getOnsetTime();
        }
        if(!$model->isExists() || $model->isParameterChanged('onset_type')) {
            $data['onset_type'] = $model->getOnsetType();
        }
        if(!$model->isExists() || $model->isParameterChanged('subsided')) {
            $data['subsided'] = $model->getSubsided();
        }
        if(!$model->isExists() || $model->isParameterChanged('reappeared')) {
            $data['reappeared'] = $model->getReappeared();
        }
        if(!$model->isExists() || $model->isParameterChanged('extent')) {
            $data['extent'] = $model->getExtent();
        }
        if(!$model->isExists() || $model->isParameterChanged('seriousness')) {
            $data['seriousness'] = $model->getSeriousness();
        }
        if(!$model->isExists() || $model->isParameterChanged('relationship')) {
            $data['relationship'] = $model->getRelationship();
        }
        if(!$model->isExists() || $model->isParameterChanged('relevant_data')) {
            $data['relevant_data'] = $model->getRelevantData();
        }
        if(!$model->isExists() || $model->isParameterChanged('relevant_history')) {
            $data['relevant_history'] = $model->getRelevantHistory();
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
            )
        );
    }
    
    public function delete(TrustCare_Model_Nafdac $model)
    {
        $model->setObjectKeyInfo(
            array(
                'id' => $model->getId(),
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
              ->setDateOfVisit($row->date_of_visit_formatted)
              ->setGenerationDate($row->generation_date_formatted)
              ->setIdUser($row->id_user)
              ->setIdPatient($row->id_patient)
              ->setIdPharmacy($row->id_pharmacy)
              ->setFilename($row->filename)
              ->setAdrStartDate($row->adr_start_date_formatted)
              ->setAdrStopDate($row->adr_stop_date_formatted)
              ->setAdrDescription($row->adr_description)
              ->setWasAdmitted($row->was_admitted)
              ->setWasHospitalizationProlonged($row->was_hospitalization_prolonged)
              ->setDurationOfAdmission($row->duration_of_admission)
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
              ->setReporterContact($row->reporter_contact)
              ->setReporterEmail($row->reporter_email)
              ->setOnsetTime($row->onset_time)
              ->setOnsetType($row->onset_type)
              ->setSubsided($row->subsided)
              ->setReappeared($row->reappeared)
              ->setExtent($row->extent)
              ->setSeriousness($row->seriousness)
              ->setRelationship($row->relationship)
              ->setRelevantData($row->relevant_data)
              ->setRelevantHistory($row->relevant_history);
        $model->setSkipTrackChanges(false);
    }
    
    /**
     * @param  int $id 
     * @param  TrustCare_Model_Nafdac $model 
     * @return void
     */
    public function find($id, TrustCare_Model_Nafdac $model)
    {
        $query = sprintf("
        select
            *,
            date_format(date_of_visit, '%%Y-%%m-%%d') as date_of_visit_formatted,
            date_format(generation_date, '%%Y-%%m-%%d %%H:%%i:%%s') as generation_date_formatted,
            date_format(adr_start_date, '%%Y-%%m-%%d') as adr_start_date_formatted,
            date_format(adr_stop_date, '%%Y-%%m-%%d') as adr_stop_date_formatted
        from %s
        where id=?;", $this->getDbTable()->info(Zend_Db_Table_Abstract::NAME));
        
        $this->getDbAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);
        $result = $this->getDbAdapter()->fetchAll($query, $id);
        if (0 == count($result)) {
            return false;
        }
        $row = $result[0];
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
        
        $query = sprintf("
            select
                *,
                date_format(date_of_visit, '%%Y-%%m-%%d') as date_of_visit_formatted,
                date_format(generation_date, '%%Y-%%m-%%d %%H:%%i:%%s') as generation_date_formatted,
                date_format(adr_start_date, '%%Y-%%m-%%d') as adr_start_date_formatted,
                date_format(adr_stop_date, '%%Y-%%m-%%d') as adr_stop_date_formatted
            from
                %s
            where
                %s
            order by id desc;
            ", $this->getDbTable()->info(Zend_Db_Table_Abstract::NAME), join(' and ', $where));
        
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
