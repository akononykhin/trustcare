<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */
 

class TrustCare_Model_Mapper_FrmCare extends TrustCare_Model_Mapper_Abstract
{
    /**
     * @param  TrustCare_Model_FrmCare $model 
     * @return void
     */
    public function save(TrustCare_Model_FrmCare &$model)
    {
        $data = array();
        if(!$model->isExists() || $model->isParameterChanged('id_pharmacy')) {
            $data['id_pharmacy'] = $model->getIdPharmacy();
        }
        if(!$model->isExists() || $model->isParameterChanged('id_patient')) {
            $data['id_patient'] = $model->getIdPatient();
        }
        if(!$model->isExists() || $model->isParameterChanged('date_of_visit')) {
            $data['date_of_visit'] = $model->getDateOfVisit();
        }
        if(!$model->isExists() || $model->isParameterChanged('is_pregnant')) {
            $data['is_pregnant'] = $model->getIsPregnant() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_receive_prescription')) {
            $data['is_receive_prescription'] = $model->getIsReceivePrescription() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_med_error_screened')) {
            $data['is_med_error_screened'] = $model->getIsMedErrorScreened() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_med_error_identified')) {
            $data['is_med_error_identified'] = $model->getIsMedErrorIdentified() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_med_adh_problem_screened')) {
            $data['is_med_adh_problem_screened'] = $model->getIsMedAdhProblemScreened() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_med_adh_problem_identified')) {
            $data['is_med_adh_problem_identified'] = $model->getIsMedAdhProblemIdentified() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_adh_intervention_provided')) {
            $data['is_adh_intervention_provided'] = $model->getIsAdhInterventionProvided() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_adr_screened')) {
            $data['is_adr_screened'] = $model->getIsAdrScreened() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_adr_symptoms')) {
            $data['is_adr_symptoms'] = $model->getIsAdrSymptoms() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('adr_severity_id')) {
            $data['adr_severity_id'] = $model->getAdrSeverityId();
        }
        if(!$model->isExists() || $model->isParameterChanged('adr_start_date')) {
            $data['adr_start_date'] = $model->getAdrStartDate();
        }
        if(!$model->isExists() || $model->isParameterChanged('adr_stop_date')) {
            $data['adr_stop_date'] = $model->getAdrStopDate();
        }
        if(!$model->isExists() || $model->isParameterChanged('is_adr_intervention_provided')) {
            $data['is_adr_intervention_provided'] = $model->getIsAdrInterventionProvided() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_nafdac_adr_filled')) {
            $data['is_nafdac_adr_filled'] = $model->getIsNafdacAdrFilled() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_patient_younger_15')) {
            $data['is_patient_younger_15'] = $model->getIsPatientYounger15() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_patient_male')) {
            $data['is_patient_male'] = $model->getIsPatientMale() ? 1 : 0;
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
    
    public function delete(TrustCare_Model_FrmCare $model)
    {
        $model->setObjectKeyInfo(array('id' => $model->getId()));
        if(!is_null($model->getId())) {
            $this->getDbTable()->delete(sprintf("id=%d", $model->getId()));
        }
    }

    /**
     * @param  int $id 
     * @param  TrustCare_Model_FrmCare $model 
     * @return void
     */
    public function find($id, TrustCare_Model_FrmCare $model)
    {
        $query = sprintf("
        select
            id,
            id_pharmacy,
            id_patient,
            date_format(date_of_visit, '%%Y-%%m-%%d') as date_of_visit,
            date_of_visit_month_index,
            is_pregnant,
            is_receive_prescription,
            is_med_error_screened,
            is_med_error_identified,
            is_med_adh_problem_screened,
            is_med_adh_problem_identified,
            is_adh_intervention_provided,
            is_adr_screened,
            is_adr_symptoms,
            adr_severity_id,
            date_format(adr_start_date, '%%Y-%%m-%%d') as adr_start_date,
            date_format(adr_stop_date, '%%Y-%%m-%%d') as adr_stop_date,
            is_adr_intervention_provided,
            is_nafdac_adr_filled,
            is_patient_younger_15,
            is_patient_male
        from %s
        where id=?;", $this->getDbTable()->info(Zend_Db_Table_Abstract::NAME));
        
        $this->getDbAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);
        $result = $this->getDbAdapter()->fetchAll($query, $id);
        if (0 == count($result)) {
            return false;
        }
        $row = $result[0];

        $model->setSkipTrackChanges(true);
        $model->setId($row->id)
              ->setIdPharmacy($row->id_pharmacy)
              ->setIdPatient($row->id_patient)
              ->setDateOfVisit($row->date_of_visit)
              ->setDateOfVisitMonthIndex($row->date_of_visit_month_index)
              ->setIsPregnant($row->is_pregnant)
              ->setIsReceivePrescription($row->is_receive_prescription)
              ->setIsMedErrorScreened($row->is_med_error_screened)
              ->setIsMedErrorIdentified($row->is_med_error_identified)
              ->setIsMedAdhProblemScreened($row->is_med_adh_problem_screened)
              ->setIsMedAdhProblemIdentified($row->is_med_adh_problem_identified)
              ->setIsAdhInterventionProvided($row->is_adh_intervention_provided)
              ->setIsAdrScreened($row->is_adr_screened)
              ->setIsAdrSymptoms($row->is_adr_symptoms)
              ->setAdrSeverityId($row->adr_severity_id)
              ->setAdrStartDate($row->adr_start_date)
              ->setAdrStopDate($row->adr_stop_date)
              ->setIsAdrInterventionProvided($row->is_adr_intervention_provided)
              ->setIsNafdacAdrFilled($row->is_nafdac_adr_filled)
              ->setIsPatientYounger15($row->is_patient_younger_15)
              ->setIsPatientMale($row->is_patient_male);
        $model->setSkipTrackChanges(false);
              
        return true;
    }
    
    /**
     * 
     * Get the number of forms generated for specified patient
     * @param int $patientId
     */
    public function getNumberOfFormsForPatient($patientId)
    {
        $query = sprintf("select count(id) from %s where id_patient=%d;", $this->getDbTable()->info(Zend_Db_Table_Abstract::NAME), $patientId);
        $this->getDbAdapter()->setFetchMode(Zend_Db::FETCH_NUM);
        $result = $this->getDbAdapter()->fetchAll($query);
        
        return $result[0][0];
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
            $entry = new TrustCare_Model_FrmCare(array('mapperOptions' => array('adapter' => $this->getDbAdapter())));
            $this->find($row->id, $entry);
            
            $entries[] = $entry;
        }
        return $entries;
    }
}
