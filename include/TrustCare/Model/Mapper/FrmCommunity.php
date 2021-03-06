<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */
 

class TrustCare_Model_Mapper_FrmCommunity extends TrustCare_Model_Mapper_Abstract
{
    /**
     * @param  TrustCare_Model_FrmCommunity $model 
     * @return void
     */
    public function save(TrustCare_Model_FrmCommunity &$model)
    {
        $data = array();
        if(!$model->isExists() || $model->isParameterChanged('generation_date')) {
            $data['generation_date'] = $model->getGenerationDate();
        }
        if(!$model->isExists() || $model->isParameterChanged('id_user')) {
            $data['id_user'] = $model->getIdUser();
        }
        if(!$model->isExists() || $model->isParameterChanged('is_commited')) {
            $data['is_commited'] = $model->getIsCommited() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('id_pharmacy')) {
            $data['id_pharmacy'] = $model->getIdPharmacy();
        }
        if(!$model->isExists() || $model->isParameterChanged('id_patient')) {
            $data['id_patient'] = $model->getIdPatient();
        }
        if(!$model->isExists() || $model->isParameterChanged('date_of_visit')) {
            $data['date_of_visit'] = $model->getDateOfVisit();
        }
            if(!$model->isExists() || $model->isParameterChanged('is_referred_in')) {
            $data['is_referred_in'] = $model->getIsReferredIn() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_referred_from')) {
            $data['is_referred_from'] = $model->getIsReferredFrom() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_first_visit_to_pharmacy')) {
            $data['is_first_visit_to_pharmacy'] = $model->getIsFirstVisitToPharmacy() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_referred_out')) {
            $data['is_referred_out'] = $model->getIsReferredOut() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_referral_completed')) {
            $data['is_referral_completed'] = $model->getIsReferralCompleted() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_hiv_risk_assesment_done')) {
            $data['is_hiv_risk_assesment_done'] = $model->getIsHivRiskAssesmentDone() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_htc_done')) {
            $data['is_htc_done'] = $model->getIsHtcDone() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('htc_result_id')) {
            $data['htc_result_id'] = $model->getHtcResultId();
        }
        if(!$model->isExists() || $model->isParameterChanged('is_client_received_htc')) {
            $data['is_client_received_htc'] = $model->getIsClientReceivedHtc() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_htc_done_in_current_pharmacy')) {
            $data['is_htc_done_in_current_pharmacy'] = $model->getIsHtcDoneInCurrentPharmacy() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_palliative_services_to_plwha')) {
            $data['is_palliative_services_to_plwha'] = $model->getIsPalliativeServicesToPlwha() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_sti_services')) {
            $data['is_sti_services'] = $model->getIsStiServices() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_reproductive_health_services')) {
            $data['is_reproductive_health_services'] = $model->getIsReproductiveHealthServices() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_malaria_services')) {
            $data['is_malaria_services'] = $model->getIsMalariaServices() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_tuberculosis_services')) {
            $data['is_tuberculosis_services'] = $model->getIsTuberculosisServices() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_ovc_services')) {
            $data['is_ovc_services'] = $model->getIsOvcServices() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_patient_younger_15')) {
            $data['is_patient_younger_15'] = $model->getIsPatientYounger15() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_patient_male')) {
            $data['is_patient_male'] = $model->getIsPatientMale() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('id_nafdac')) {
            $data['id_nafdac'] = $model->getIdNafdac();
        }
        if(!$model->isExists() || $model->isParameterChanged('hiv_status')) {
            $data['hiv_status'] = $model->getHivStatus();
        }
        if(!$model->isExists() || $model->isParameterChanged('is_adr_screened')) {
            $data['is_adr_screened'] = $model->getIsAdrScreened() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_adr_symptoms')) {
            $data['is_adr_symptoms'] = $model->getIsAdrSymptoms() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('is_adr_intervention_provided')) {
            $data['is_adr_intervention_provided'] = $model->getIsAdrInterventionProvided() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('adr_start_date')) {
            $data['adr_start_date'] = $model->getAdrStartDate();
        }
        if(!$model->isExists() || $model->isParameterChanged('adr_stop_date')) {
            $data['adr_stop_date'] = $model->getAdrStopDate();
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
    
    public function delete(TrustCare_Model_FrmCommunity $model)
    {
        $model->setObjectKeyInfo(array('id' => $model->getId()));
        if(!is_null($model->getId())) {
            $this->getDbTable()->delete(sprintf("id=%d", $model->getId()));
        }
    }

    
    private function _fillModelForFind(TrustCare_Model_FrmCommunity $model, $row)
    {
        $model->setSkipTrackChanges(true);
        $model->setId($row->id)
              ->setGenerationDate($row->generation_date_formatted)
              ->setIdUser($row->id_user)
              ->setIsCommited($row->is_commited)
              ->setIdPharmacy($row->id_pharmacy)
              ->setIdPatient($row->id_patient)
              ->setDateOfVisit($row->date_of_visit_formatted)
              ->setDateOfVisitMonthIndex($row->date_of_visit_month_index)
              ->setIsFirstVisitToPharmacy($row->is_first_visit_to_pharmacy)
              ->setIsReferredFrom($row->is_referred_from)
              ->setIsReferredIn($row->is_referred_in)
              ->setIsReferredOut($row->is_referred_out)
              ->setIsReferralCompleted($row->is_referral_completed)
              ->setIsHivRiskAssesmentDone($row->is_hiv_risk_assesment_done)
              ->setIsHtcDone($row->is_htc_done)
              ->setHtcResultId($row->htc_result_id)
              ->setIsClientReceivedHtc($row->is_client_received_htc)
              ->setIsHtcDoneInCurrentPharmacy($row->is_htc_done_in_current_pharmacy)
              ->setIsPalliativeServicesToPlwha($row->is_palliative_services_to_plwha)
              ->setIsStiServices($row->is_sti_services)
              ->setIsReproductiveHealthServices($row->is_reproductive_health_services)
              ->setIsMalariaServices($row->is_malaria_services)
              ->setIsTuberculosisServices($row->is_tuberculosis_services)
              ->setIsOvcServices($row->is_ovc_services)
              ->setIsPatientYounger15($row->is_patient_younger_15)
              ->setIsPatientMale($row->is_patient_male)
              ->setIdNafdac($row->id_nafdac)
              ->setHivStatus($row->hiv_status)
              ->setIsAdrScreened($row->is_adr_screened)
              ->setIsAdrSymptoms($row->is_adr_symptoms)
              ->setIsAdrInterventionProvided($row->is_adr_intervention_provided)
              ->setAdrStartDate($row->adr_start_date_formatted)
              ->setAdrStopDate($row->adr_stop_date_formatted);
          $model->setSkipTrackChanges(false);
    }
    
    /**
     * @param  int $id 
     * @param  TrustCare_Model_FrmCommunity $model 
     * @return void
     */
    public function find($id, TrustCare_Model_FrmCommunity $model)
    {
        $query = sprintf("
        select
            *,
            date_format(generation_date, '%%Y-%%m-%%d %%H:%%i:%%s') as generation_date_formatted,
            date_format(date_of_visit, '%%Y-%%m-%%d') as date_of_visit_formatted,
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
            $entry = new TrustCare_Model_FrmCommunity(array('mapperOptions' => array('adapter' => $this->getDbAdapter())));
            $this->find($row->id, $entry);
            
            $entries[] = $entry;
        }
        return $entries;
    }
}
