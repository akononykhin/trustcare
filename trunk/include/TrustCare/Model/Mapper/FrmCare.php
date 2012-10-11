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
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return false;
        }
        $row = $result->current();
        $model->setSkipTrackChanges(true);
        $model->setId($row->id)
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
              ->setAdrStartDate($row->adr_start_date)
              ->setAdrStopDate($row->adr_stop_date)
              ->setIsAdrInterventionProvided($row->is_adr_intervention_provided)
              ->setIsNafdacAdrFilled($row->is_nafdac_adr_filled);
          $model->setSkipTrackChanges(false);
              
        return true;
    }

    /**
     * @return array
     */
    public function fetchAll()
    {
        $entries   = array();
        
        $select = $this->getDbTable()->select();
        $select->from($this->getDbTable(), array('id'));
        $resultSet = $this->getDbTable()->fetchAll($select);
        foreach ($resultSet as $row) {
            $entry = new TrustCare_Model_FrmCare(array('mapperOptions' => array('adapter' => $this->getDbAdapter())));
            $this->find($row['id'], $entry);
            
            $entries[] = $entry;
        }
        return $entries;
    }
}
