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
        if(!$model->isExists() || $model->isParameterChanged('id_patient')) {
            $data['id_patient'] = $model->getIdPatient();
        }
        if(!$model->isExists() || $model->isParameterChanged('date_of_visit')) {
            $data['date_of_visit'] = $model->getDateOfVisit();
        }
        if(!$model->isExists() || $model->isParameterChanged('is_referred_in')) {
            $data['is_referred_in'] = $model->getIsReferredIn() ? 1 : 0;
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

    /**
     * @param  int $id 
     * @param  TrustCare_Model_FrmCommunity $model 
     * @return void
     */
    public function find($id, TrustCare_Model_FrmCommunity $model)
    {
        $query = sprintf("
        select
            id,
            id_patient,
            date_format(date_of_visit, '%%Y-%%m-%%d') as date_of_visit,
            date_of_visit_month_index,
            is_first_visit_to_pharmacy,
            is_referred_in,
            is_referred_out,
            is_referral_completed,
            is_hiv_risk_assesment_done,
            is_htc_done,
            htc_result_id,
            is_client_received_htc,
            is_htc_done_in_current_pharmacy,
            is_palliative_services_to_plwha,
            is_sti_services,
            is_reproductive_health_services,
            is_tuberculosis_services,
            is_ovc_services,
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
              ->setIdPatient($row->id_patient)
              ->setDateOfVisit($row->date_of_visit)
              ->setDateOfVisitMonthIndex($row->date_of_visit_month_index)
              ->setIsFirstVisitToPharmacy($row->is_first_visit_to_pharmacy)
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
              ->setIsTuberculosisServices($row->is_tuberculosis_services)
              ->setIsOvcServices($row->is_ovc_services)
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
            $entry = new TrustCare_Model_FrmCommunity(array('mapperOptions' => array('adapter' => $this->getDbAdapter())));
            $this->find($row->id, $entry);
            
            $entries[] = $entry;
        }
        return $entries;
    }
}