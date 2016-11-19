<?php
/**
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 */

class TrustCare_SystemInterface_ReportGenerator_CommunityServices extends TrustCare_SystemInterface_ReportGenerator_Abstract
{
    const TMP_TABLE_NAME = 'tmp_community_report_register';
    
    public function getCode()
    {
        return self::CODE_COMMUNITY_SERVICES;
    }

    private function prepareDataForReport($db, $uuid, $monthIndex, $pharmacyId)
    {
        $dataRows = array();
        
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $result = $db->fetchAll("select distinct id_patient from frm_community where id_pharmacy=? and date_of_visit_month_index=?;", array($pharmacyId, $monthIndex));
        foreach($result as $row) {
            $patientId = $row['id_patient'];
            $patientObj = TrustCare_Model_Patient::find($patientId, array('mapperOptions' => array('adapter' => $db)));
            if(is_null($patientObj)) {
                throw new Exception(sprintf("Failed to load patient with id=%s", $patientId));
            }
            
            $dataRow = array();
            $dataRow['report_uid'] = $uuid;
            $dataRow['client_name'] = $patientObj->showNameAs();
            $dataRow['is_plhiv_paba'] = $this->isPlhivOrPaba($db, $monthIndex, $pharmacyId, $patientId);
            $dataRow['fe_provided_preventive'] = $this->isPreventiveProvided($db, $monthIndex, $pharmacyId, $patientId, true);
            $dataRow['fe_provided_supportive'] = $this->isSupportiveProvided($db, $monthIndex, $pharmacyId, $patientId, true);
            $dataRow['fe_provided_clinical'] = $this->isClinicalProvided($db, $monthIndex, $pharmacyId, $patientId, true);
            
            $info = $this->getMinimumCarePlwhaInfo($db, $monthIndex, $pharmacyId, $patientId);
            $dataRow['plwha_young_male'] = $info['male_younger_15'];
            $dataRow['plwha_adult_male'] = $info['male_older_15'];
            $dataRow['plwha_young_female'] = $info['female_younger_15'];
            $dataRow['plwha_adult_female'] = $info['female_older_15'];
            
            $info = $this->getMinimumCarePabaInfo($db, $monthIndex, $pharmacyId, $patientId);
            $dataRow['paba_young_male'] = $info['male_younger_15'];
            $dataRow['paba_adult_male'] = $info['male_older_15'];
            $dataRow['paba_young_female'] = $info['female_younger_15'];
            $dataRow['paba_adult_female'] = $info['female_older_15'];
            
            $info = $this->getMinimumCareOtherInfo($db, $monthIndex, $pharmacyId, $patientId);
            $dataRow['other_young_male'] = $info['male_younger_15'];
            $dataRow['other_adult_male'] = $info['male_older_15'];
            $dataRow['other_young_female'] = $info['female_younger_15'];
            $dataRow['other_adult_female'] = $info['female_older_15'];

            $dataRow['se_provided_preventive'] = $this->isPreventiveProvided($db, $monthIndex, $pharmacyId, $patientId, false);
            $dataRow['se_provided_supportive'] = $this->isSupportiveProvided($db, $monthIndex, $pharmacyId, $patientId, false);
            $dataRow['se_provided_clinical'] = $this->isClinicalProvided($db, $monthIndex, $pharmacyId, $patientId, false);


            $reproductiveServices = $this->getReproductiveHealthServices($db, $monthIndex, $pharmacyId, $patientId);
            $referredOutServices = $this->getReferredOutServices($db, $monthIndex, $pharmacyId, $patientId);
            $referredInServices = $this->getReferredInServices($db, $monthIndex, $pharmacyId, $patientId);
            $stiServices = $this->getStiServices($db, $monthIndex, $pharmacyId, $patientId);
            $malariaServices = $this->getMalariaServices($db, $monthIndex, $pharmacyId, $patientId);
            $tuberculosisServices = $this->getTuberculosisServices($db, $monthIndex, $pharmacyId, $patientId);
            $palliativeServices = $this->getPalliativeCareServices($db, $monthIndex, $pharmacyId, $patientId);
            $adrInterventions = $this->getAdrInterventions($db, $monthIndex, $pharmacyId, $patientId);
            
            $dataRow['preventive_1'] = $this->isPreventive1($db, $monthIndex, $pharmacyId, $patientId);
            $dataRow['preventive_2'] = $this->isPreventive2($db, $monthIndex, $pharmacyId, $patientId);
            $dataRow['preventive_3'] = $this->isPreventive3($db, $monthIndex, $pharmacyId, $patientId);
            $dataRow['preventive_4'] = in_array(385, $reproductiveServices) ? 1 : 0;   /* Counseling on safe sex practices */
            $dataRow['preventive_5'] = in_array(380, $reproductiveServices) ? 1 : 0;   /* Condoms provided */
            $dataRow['preventive_6'] = in_array(386, $reproductiveServices) ? 1 : 0;   /* Health education & promotion */

            $dataRow['supportive_out_1'] = in_array(320, $referredOutServices) ? 1 : 0;   /* HCT */
            $dataRow['supportive_out_2'] = in_array(321, $referredOutServices) ? 1 : 0;   /* ART */
            $dataRow['supportive_out_3'] = in_array(328, $referredOutServices) ? 1 : 0;   /* Post Exposure Prophylaxis (PEP) */
            $dataRow['supportive_out_4'] = in_array(322, $referredOutServices) ? 1 : 0;   /* PMTCT */
            $dataRow['supportive_out_5'] = in_array(323, $referredOutServices) ? 1 : 0;   /* Tuberculosis services */
            $dataRow['supportive_out_6'] = in_array(326, $referredOutServices) ? 1 : 0;   /* Support group */

            $dataRow['supportive_in_1'] = in_array(460, $referredInServices) ? 1 : 0;   /* OVC identification and referral to CBO for enrollment */
            $dataRow['supportive_in_2'] = in_array(461, $referredInServices) ? 1 : 0;   /* Adherence counseling */
            $dataRow['supportive_in_3'] = in_array(462, $referredInServices) ? 1 : 0;   /* Psychosocial support */
            $dataRow['supportive_in_4'] = in_array(463, $referredInServices) ? 1 : 0;   /* Nutritional support & counseling */
            $dataRow['supportive_in_5'] = in_array(464, $referredInServices) ? 1 : 0;   /* Distribution of SBC materials */

            $dataRow['clinical_sti_1'] = in_array(400, $stiServices) ? 1 : 0;   /* STIs Screening & Counselling */
            $dataRow['clinical_sti_2'] = in_array(401, $stiServices) ? 1 : 0;   /* STIs Treatment */

            $dataRow['clinical_malaria_1'] = in_array(480, $malariaServices) ? 1 : 0;   /* Malaria prevention (LLITN) */
            $dataRow['clinical_malaria_2'] = in_array(481, $malariaServices) ? 1 : 0;   /* Malaria prevention (IPT) */
            $dataRow['clinical_malaria_3'] = in_array(482, $malariaServices) ? 1 : 0;   /* Malaria Treatment */
            
            $dataRow['clinical_reproductive_1'] = in_array(381, $reproductiveServices) ? 1 : 0;   /* Emergency Contraceptive Provided */
            $dataRow['clinical_reproductive_2'] = in_array(382, $reproductiveServices) ? 1 : 0;   /* Injectable Contraceptive Provided */
            $dataRow['clinical_reproductive_3'] = in_array(383, $reproductiveServices) ? 1 : 0;   /* Oral Contraceptives Provided */
            $dataRow['clinical_reproductive_4'] = in_array(384, $reproductiveServices) ? 1 : 0;   /* RH/FP Counseling */
            
            $dataRow['clinical_tb_1'] = in_array(420, $tuberculosisServices) ? 1 : 0;   /* TB Screening */
            $dataRow['clinical_tb_2'] = in_array(421, $tuberculosisServices) ? 1 : 0;   /* TB Adherence Support */
            $dataRow['clinical_tb_3'] = in_array(422, $tuberculosisServices) ? 1 : 0;   /* TB Drugs Refills */
            $dataRow['clinical_tb_4'] = in_array(424, $tuberculosisServices) ? 1 : 0;   /* INH Preventive Therapy (IPT) */
            $dataRow['clinical_tb_5'] = in_array(423, $tuberculosisServices) ? 1 : 0;   /* DOTs/CTBC */
            
            $dataRow['clinical_palliative_1'] = in_array(360, $palliativeServices) ? 1 : 0;   /* Opportunistic Infections Screening & Management */
            $dataRow['clinical_palliative_2'] = in_array(363, $palliativeServices) ? 1 : 0;   /* Pain Management */
            
            $isAdrDetected = $this->isAdrDetected($db, $monthIndex, $pharmacyId, $patientId);
            $dataRow['adr_screened'] = $this->isScreenedForAdr($db, $monthIndex, $pharmacyId, $patientId);
            $dataRow['adr_not_detected'] = (1 == $isAdrDetected) ? 0 : 1;
            $dataRow['adr_detected'] = $isAdrDetected;
            $dataRow['nafdac_filled'] = $this->isNafdacFilled($db, $monthIndex, $pharmacyId, $patientId);
            
            $dataRow['adr_intervention_1'] = in_array(500, $adrInterventions) ? 1 : 0;   /* Referred to prescriber / other HCWs/facility for ADR management */
            $dataRow['adr_intervention_2'] = in_array(501, $adrInterventions) ? 1 : 0;   /* Patient counseled on how to manage ADR */
            $dataRow['adr_intervention_3'] = in_array(502, $adrInterventions) ? 1 : 0;   /* Drug therapy initiated/ changed */
            
            $dataRows[] = $dataRow;
        }
        
        foreach($dataRows as $data) {
            $db->insert(self::TMP_TABLE_NAME, $data);
        }
    }
    
    private function cleanDataForReport($db, $uuid)
    {
        $ret = $db->delete(self::TMP_TABLE_NAME, "report_uid=".$db->quote($uuid));
    }
    
    public function generate($params, $format = '')
    {
        $id_user = array_key_exists('id_user', $params) ? $params['id_user'] : null;
        $year = array_key_exists('year', $params) ? $params['year'] : -1;
        $month = array_key_exists('month', $params) ? $params['month'] : -1;
        $month_index = array_key_exists('month_index', $params) ? $params['month_index'] : -1;
        $id_pharmacy = array_key_exists('id_pharmacy', $params) ? $params['id_pharmacy'] : -1;

        if(empty($format)) {
            $format = $this->getDefaultFormat();
        }
        
        $dbOptions = Zend_Registry::get('dbOptions');
        $db = Zend_Db::factory($dbOptions['adapter'], $dbOptions['params']);
        
        $obj = null;
        $uuid = uniqid(rand(1, 999999), true);
        try {
            $this->prepareDataForReport($db, $uuid, $month_index, $id_pharmacy);
            
            $fileName = sprintf("%s_%s%s_%s_%s.%s", $this->getCode(), $year, $month, gmdate("Ymd"), rand(0, 1000), strtolower($format));
            $fileReportOutput = sprintf("%s/%s", $this->reportsDirectory(), $fileName);
            
            $designFile= "community_services.rptdesign";
            $parameters = array();
            $parameters[] = sprintf('jdbc_driver_url=jdbc:mysql://%s/%s', $dbOptions['params']['host'], $dbOptions['params']['dbname']);
            $parameters[] = sprintf('jdbc_username=%s', $dbOptions['params']['username']);
            $parameters[] = sprintf('jdbc_password=%s', $dbOptions['params']['password']);
            $parameters[] = sprintf("month_index=%s", $month_index);
            $parameters[] = sprintf("month=%s", $month);
            $parameters[] = sprintf("year=%s", $year);
            $parameters[] = sprintf("id_pharmacy=%s", $id_pharmacy);
            $parameters[] = sprintf("report_uid=%s", $uuid);
            
            $this->_generateReportFile($designFile, $fileReportOutput, $parameters, $format);
            
            $obj = new TrustCare_Model_ReportCommunityServices(array(
                'id_user' => $id_user,
                'generation_date' => ZendX_Db_Table_Abstract::LABEL_NOW,
                'period' => sprintf("%04d%02d", $year, $month),
                'id_pharmacy' => $id_pharmacy,
                'filename' => $fileName,
            ));
            $obj->save();
        }
        catch(Exception $ex) {
            $logger = LoggerManager::getLogger("General");
            $logger->error(sprintf("Failed to generate community_services report: %s", $ex->getMessage()));
        }
        $this->cleanDataForReport($db, $uuid);
        

        return $obj;
    }
    
    public function getDefaultFormat()
    {
        return "PDF";
    }
    
    private function isPlhivOrPaba($db, $monthIndex, $pharmacyId, $patientId)
    {
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $result1 = $db->fetchAll("select id from frm_community where id_pharmacy=? and date_of_visit_month_index=? and id_patient=? and (hiv_status='plwha' or hiv_status='paba');", array($pharmacyId, $monthIndex, $patientId));
        return count($result1) ? 1 : 0;
    }
    
    private function isPreventiveProvided($db, $monthIndex, $pharmacyId, $patientId, $isFirstVisit=true)
    {
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $result1 = $db->fetchAll("
            select
                id
            from
                frm_community
            where
                id_pharmacy=? and
                date_of_visit_month_index=? and
                id_patient=? and
                is_first_visit_to_pharmacy=? and
                (
                    is_hiv_risk_assesment_done=1 or
                    is_htc_done=1 or
                    is_client_received_htc=1 or
                    is_reproductive_health_services=1
                );", array($pharmacyId, $monthIndex, $patientId, ($isFirstVisit ? 1 : 0)));
        return count($result1) ? 1 : 0;
    }
    
    private function isSupportiveProvided($db, $monthIndex, $pharmacyId, $patientId, $isFirstVisit=true)
    {
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $result1 = $db->fetchAll("
            select
                id
            from
                frm_community
            where
                id_pharmacy=? and
                date_of_visit_month_index=? and
                id_patient=? and
                is_first_visit_to_pharmacy=? and
                (
                    is_referred_in=1 or
                    is_referred_out=1
                );", array($pharmacyId, $monthIndex, $patientId, ($isFirstVisit ? 1 : 0)));
        return count($result1) ? 1 : 0;
    }
    
    private function isClinicalProvided($db, $monthIndex, $pharmacyId, $patientId, $isFirstVisit=true)
    {
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $result1 = $db->fetchAll("
            select
                id
            from
                frm_community
            where
                id_pharmacy=? and
                date_of_visit_month_index=? and
                id_patient=? and
                is_first_visit_to_pharmacy=? and
                (
                    is_sti_services=1 or
                    is_reproductive_health_services=1 or
                    is_malaria_services=1 or
                    is_tuberculosis_services=1 or
                    is_palliative_services_to_plwha=1 or
                    is_adr_screened=1 or
                    is_adr_intervention_provided=1
                );", array($pharmacyId, $monthIndex, $patientId, ($isFirstVisit ? 1 : 0)));
        return count($result1) ? 1 : 0;
    }
    
    private function getMinimumCarePlwhaInfo($db, $monthIndex, $pharmacyId, $patientId)
    {
        $info = array(
            'male_younger_15' => 0,
            'male_older_15' => 0,
            'female_younger_15' => 0,
            'female_older_15' => 0,
        );
        
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $result1 = $db->fetchAll("
    select distinct
      is_patient_male as is_male,
      is_patient_younger_15 as is_younger_15
    from
      frm_community
    where
      id_pharmacy=? and
      date_of_visit_month_index=? and
      hiv_status='plwha' and
      id_patient=?;", array($pharmacyId, $monthIndex, $patientId));
        
        foreach($result1 as $row) {
            $isMale = !empty($row['is_male']) ? true : false;
            $isYounger15 =!empty($row['is_younger_15']) ? true : false;
            if($isMale) {
                if($isYounger15) {
                    $info['male_younger_15'] = 1;
                }
                else {
                    $info['male_older_15'] = 1;
                }
            }
            else {
                if($isYounger15) {
                    $info['female_younger_15'] = 1;
                }
                else {
                    $info['female_older_15'] = 1;
                }
            }
        }
        
        return $info;
    }
    
    private function getMinimumCarePabaInfo($db, $monthIndex, $pharmacyId, $patientId)
    {
        $info = array(
            'male_younger_15' => 0,
            'male_older_15' => 0,
            'female_younger_15' => 0,
            'female_older_15' => 0,
        );
        
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $result1 = $db->fetchAll("
    select distinct
      is_patient_male as is_male,
      is_patient_younger_15 as is_younger_15
    from
      frm_community
    where
      id_pharmacy=? and
      date_of_visit_month_index=? and
      hiv_status='paba' and
      id_patient=?;", array($pharmacyId, $monthIndex, $patientId));
        
        foreach($result1 as $row) {
            $isMale = !empty($row['is_male']) ? true : false;
            $isYounger15 =!empty($row['is_younger_15']) ? true : false;
            if($isMale) {
                if($isYounger15) {
                    $info['male_younger_15'] = 1;
                }
                else {
                    $info['male_older_15'] = 1;
                }
            }
            else {
                if($isYounger15) {
                    $info['female_younger_15'] = 1;
                }
                else {
                    $info['female_older_15'] = 1;
                }
            }
        }
        
        return $info;
    }
    
    private function getMinimumCareOtherInfo($db, $monthIndex, $pharmacyId, $patientId)
    {
        $info = array(
            'male_younger_15' => 0,
            'male_older_15' => 0,
            'female_younger_15' => 0,
            'female_older_15' => 0,
        );
        
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $result1 = $db->fetchAll("
    select distinct
      is_patient_male as is_male,
      is_patient_younger_15 as is_younger_15
    from
      frm_community
    where
      id_pharmacy=? and
      date_of_visit_month_index=? and
      (hiv_status='' or hiv_status is NULL) and
      id_patient=?;", array($pharmacyId, $monthIndex, $patientId));
        
        foreach($result1 as $row) {
            $isMale = !empty($row['is_male']) ? true : false;
            $isYounger15 =!empty($row['is_younger_15']) ? true : false;
            if($isMale) {
                if($isYounger15) {
                    $info['male_younger_15'] = 1;
                }
                else {
                    $info['male_older_15'] = 1;
                }
            }
            else {
                if($isYounger15) {
                    $info['female_younger_15'] = 1;
                }
                else {
                    $info['female_older_15'] = 1;
                }
            }
        }
        
        return $info;
    }
    
    private function isPreventive1($db, $monthIndex, $pharmacyId, $patientId, $isFirstVisit=true)
    {
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $result1 = $db->fetchAll("
            select
                id
            from
                frm_community
            where
                id_pharmacy=? and
                date_of_visit_month_index=? and
                id_patient=? and
                is_first_visit_to_pharmacy=? and
                (
                    is_hiv_risk_assesment_done=1
                );", array($pharmacyId, $monthIndex, $patientId, ($isFirstVisit ? 1 : 0)));
        return count($result1) ? 1 : 0;
    }
    
    private function isPreventive2($db, $monthIndex, $pharmacyId, $patientId, $isFirstVisit=true)
    {
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $result1 = $db->fetchAll("
            select
                id
            from
                frm_community
            where
                id_pharmacy=? and
                date_of_visit_month_index=? and
                id_patient=? and
                is_first_visit_to_pharmacy=? and
                (
                    is_htc_done=1
                );", array($pharmacyId, $monthIndex, $patientId, ($isFirstVisit ? 1 : 0)));
        return count($result1) ? 1 : 0;
    }
    
    private function isPreventive3($db, $monthIndex, $pharmacyId, $patientId, $isFirstVisit=true)
    {
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $result1 = $db->fetchAll("
            select
                id
            from
                frm_community
            where
                id_pharmacy=? and
                date_of_visit_month_index=? and
                id_patient=? and
                is_first_visit_to_pharmacy=? and
                (
                    is_client_received_htc=1
                );", array($pharmacyId, $monthIndex, $patientId, ($isFirstVisit ? 1 : 0)));
        return count($result1) ? 1 : 0;
    }

    
    private function getReproductiveHealthServices($db, $monthIndex, $pharmacyId, $patientId)
    {
        $list = array();
        
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $result1 = $db->fetchAll("
            select
                id_pharmacy_dictionary
            from
                frm_community_reproductive_health_type
            where
                id_frm_community in (
                    select
                        id
                    from
                        frm_community
                    where
                      id_pharmacy=? and
                      date_of_visit_month_index=? and
                      id_patient=?                
                );", array($pharmacyId, $monthIndex, $patientId));
        
        foreach($result1 as $row) {
            $list[] = $row['id_pharmacy_dictionary'];
        }
        
        return $list;
    }
    
    private function getReferredOutServices($db, $monthIndex, $pharmacyId, $patientId)
    {
        $list = array();
        
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $result1 = $db->fetchAll("
            select
                id_pharmacy_dictionary
            from
                frm_community_referred_out
            where
                id_frm_community in (
                    select
                        id
                    from
                        frm_community
                    where
                      id_pharmacy=? and
                      date_of_visit_month_index=? and
                      id_patient=?                
                );", array($pharmacyId, $monthIndex, $patientId));
        
        foreach($result1 as $row) {
            $list[] = $row['id_pharmacy_dictionary'];
        }
        
        return $list;
    }
    
    private function getReferredInServices($db, $monthIndex, $pharmacyId, $patientId)
    {
        $list = array();
        
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $result1 = $db->fetchAll("
            select
                id_pharmacy_dictionary
            from
                frm_community_referred_in
            where
                id_frm_community in (
                    select
                        id
                    from
                        frm_community
                    where
                      id_pharmacy=? and
                      date_of_visit_month_index=? and
                      id_patient=?                
                );", array($pharmacyId, $monthIndex, $patientId));
        
        foreach($result1 as $row) {
            $list[] = $row['id_pharmacy_dictionary'];
        }
        
        return $list;
    }
    
    private function getStiServices($db, $monthIndex, $pharmacyId, $patientId)
    {
        $list = array();
        
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $result1 = $db->fetchAll("
            select
                id_pharmacy_dictionary
            from
                frm_community_sti_type
            where
                id_frm_community in (
                    select
                        id
                    from
                        frm_community
                    where
                      id_pharmacy=? and
                      date_of_visit_month_index=? and
                      id_patient=?                
                );", array($pharmacyId, $monthIndex, $patientId));
        
        foreach($result1 as $row) {
            $list[] = $row['id_pharmacy_dictionary'];
        }
        
        return $list;
    }
    
    private function getMalariaServices($db, $monthIndex, $pharmacyId, $patientId)
    {
        $list = array();
        
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $result1 = $db->fetchAll("
            select
                id_pharmacy_dictionary
            from
                frm_community_malaria_type
            where
                id_frm_community in (
                    select
                        id
                    from
                        frm_community
                    where
                      id_pharmacy=? and
                      date_of_visit_month_index=? and
                      id_patient=?                
                );", array($pharmacyId, $monthIndex, $patientId));
        
        foreach($result1 as $row) {
            $list[] = $row['id_pharmacy_dictionary'];
        }
        
        return $list;
    }
    
    private function getTuberculosisServices($db, $monthIndex, $pharmacyId, $patientId)
    {
        $list = array();
        
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $result1 = $db->fetchAll("
            select
                id_pharmacy_dictionary
            from
                frm_community_tuberculosis_type
            where
                id_frm_community in (
                    select
                        id
                    from
                        frm_community
                    where
                      id_pharmacy=? and
                      date_of_visit_month_index=? and
                      id_patient=?                
                );", array($pharmacyId, $monthIndex, $patientId));
        
        foreach($result1 as $row) {
            $list[] = $row['id_pharmacy_dictionary'];
        }
        
        return $list;
    }
    
    private function getPalliativeCareServices($db, $monthIndex, $pharmacyId, $patientId)
    {
        $list = array();
        
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $result1 = $db->fetchAll("
            select
                id_pharmacy_dictionary
            from
                frm_community_palliative_care_type
            where
                id_frm_community in (
                    select
                        id
                    from
                        frm_community
                    where
                      id_pharmacy=? and
                      date_of_visit_month_index=? and
                      id_patient=?                
                );", array($pharmacyId, $monthIndex, $patientId));
        
        foreach($result1 as $row) {
            $list[] = $row['id_pharmacy_dictionary'];
        }
        
        return $list;
    }
    
    private function getAdrInterventions($db, $monthIndex, $pharmacyId, $patientId)
    {
        $list = array();
    
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $result1 = $db->fetchAll("
            select
                id_pharmacy_dictionary
            from
                frm_community_adr_intervention
            where
                id_frm_community in (
                    select
                        id
                    from
                        frm_community
                    where
                        id_pharmacy=? and
                        date_of_visit_month_index=? and
                        id_patient=?
        );", array($pharmacyId, $monthIndex, $patientId));
    
        foreach($result1 as $row) {
            $list[] = $row['id_pharmacy_dictionary'];
        }
    
        return $list;
    }
    
    private function isScreenedForAdr($db, $monthIndex, $pharmacyId, $patientId)
    {
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $result1 = $db->fetchAll("
            select
                id
            from
                frm_community
            where
                id_pharmacy=? and
                date_of_visit_month_index=? and
                id_patient=? and
                is_adr_screened=1;", array($pharmacyId, $monthIndex, $patientId));
        return count($result1) ? 1 : 0;
    }
    
    private function isAdrDetected($db, $monthIndex, $pharmacyId, $patientId)
    {
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $result1 = $db->fetchAll("
            select
                id
            from
                frm_community
            where
                id_pharmacy=? and
                date_of_visit_month_index=? and
                id_patient=? and
                is_adr_symptoms=1;", array($pharmacyId, $monthIndex, $patientId));
        return count($result1) ? 1 : 0;
    }
    
    private function isNafdacFilled($db, $monthIndex, $pharmacyId, $patientId)
    {
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $result1 = $db->fetchAll("
            select
                id
            from
                frm_community
            where
                id_pharmacy=? and
                date_of_visit_month_index=? and
                id_patient=? and
                id_nafdac is not null;", array($pharmacyId, $monthIndex, $patientId));
        return count($result1) ? 1 : 0;
    }
}
