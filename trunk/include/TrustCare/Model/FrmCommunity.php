<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */

class TrustCare_Model_FrmCommunity extends TrustCare_Model_Abstract
{
    protected $_id;
    protected $_id_patient;
    protected $_date_of_visit;
    protected $_date_of_visit_month_index;
    protected $_is_referred_in;
    protected $_is_referred_out;
    protected $_is_referral_completed;
    protected $_is_hiv_risk_assesment_done;
    protected $_is_htc_done;
    protected $_is_client_received_htc;
    protected $_is_htc_done_in_current_pharmacy;
    protected $_is_palliative_services_to_plwha;
    protected $_is_sti_services;
    protected $_is_reproductive_health_services;
    protected $_is_tuberculosis_services;
    protected $_is_ovc_services;
    protected $_is_patient_younger_15;
    protected $_is_patient_male;
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_FrmCommunity
     */
    public function setId($value)
    {
        $this->_parameterChanged('id', $value);
    	$this->_id = (int) $value;
        return $this;
    }

    /**
     * @return null|int
     */
    public function getId()
    {
        return $this->_id;
    }
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_FrmCommunity
     */
    public function setIdPatient($value)
    {
        $this->_parameterChanged('id_patient', $value);
    	$this->_id_patient = (int) $value;
        return $this;
    }

    /**
     * @return null|int
     */
    public function getIdPatient()
    {
        return $this->_id_patient;
    }
    
    /**
     * @param  string $value 
     * @return TrustCare_Model_FrmCommunity
     */
    public function setDateOfVisit($value)
    {
        $this->_parameterChanged('date_of_visit', $value);
        $this->_date_of_visit = (string) $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getDateOfVisit()
    {
        return $this->_date_of_visit;
    }
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_FrmCare
     */
    public function setDateOfVisitMonthIndex($value)
    {
        $this->_parameterChanged('date_of_visit_month_index', $value);
        $this->_date_of_visit_month_index = (int) $value;
        return $this;
    }
    
    /**
     * @return null|int
     */
    public function getDateOfVisitMonthIndex()
    {
        return $this->_date_of_visit_month_index;
    }
    
    
    /**
     * @param  bool $value 
     * @return TrustCare_Model_FrmCommunity
     */
    public function setIsReferredIn($value)
    {
        $this->_parameterChanged('is_referred_in', $value, true);
    	$this->_is_referred_in = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getIsReferredIn()
    {
        return !empty($this->_is_referred_in) ? true : false;
    }
    
    /**
     * @param  bool $value 
     * @return TrustCare_Model_FrmCommunity
     */
    public function setIsReferredOut($value)
    {
        $this->_parameterChanged('is_referred_out', $value, true);
        $this->_is_referred_out = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getIsReferredOut()
    {
        return !empty($this->_is_referred_out) ? true : false;
    }
    
    /**
     * @param  bool $value 
     * @return TrustCare_Model_FrmCommunity
     */
    public function setIsReferralCompleted($value)
    {
        $this->_parameterChanged('is_referral_completed', $value, true);
        $this->_is_referral_completed = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getIsReferralCompleted()
    {
        return !empty($this->_is_referral_completed) ? true : false;
    }
    
    /**
     * @param  bool $value 
     * @return TrustCare_Model_FrmCommunity
     */
    public function setIsHivRiskAssesmentDone($value)
    {
        $this->_parameterChanged('is_hiv_risk_assesment_done', $value, true);
        $this->_is_hiv_risk_assesment_done = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getIsHivRiskAssesmentDone()
    {
        return !empty($this->_is_hiv_risk_assesment_done) ? true : false;
    }
    
    /**
     * @param  bool $value 
     * @return TrustCare_Model_FrmCommunity
     */
    public function setIsHtcDone($value)
    {
        $this->_parameterChanged('is_htc_done', $value, true);
        $this->_is_htc_done = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|bool
     */
    public function getIsHtcDone()
    {
        return !empty($this->_is_htc_done) ? true : false;
    }
    
    /**
     * @param  bool $value 
     * @return TrustCare_Model_FrmCommunity
     */
    public function setIsClientReceivedHtc($value)
    {
        $this->_parameterChanged('is_client_received_htc', $value, true);
        $this->_is_client_received_htc = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|bool
     */
    public function getIsClientReceivedHtc()
    {
        return !empty($this->_is_client_received_htc) ? true : false;
    }
    
    /**
     * @param  bool $value 
     * @return TrustCare_Model_FrmCommunity
     */
    public function setIsHtcDoneInCurrentPharmacy($value)
    {
        $this->_parameterChanged('is_htc_done_in_current_pharmacy', $value, true);
        $this->_is_htc_done_in_current_pharmacy = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|bool
     */
    public function getIsHtcDoneInCurrentPharmacy()
    {
        return !empty($this->_is_htc_done_in_current_pharmacy) ? true : false;
    }
    
    /**
     * @param  bool $value 
     * @return TrustCare_Model_FrmCommunity
     */
    public function setIsPalliativeServicesToPlwha($value)
    {
        $this->_parameterChanged('is_palliative_services_to_plwha', $value, true);
        $this->_is_palliative_services_to_plwha = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|bool
     */
    public function getIsPalliativeServicesToPlwha()
    {
        return !empty($this->_is_palliative_services_to_plwha) ? true : false;
    }
    
    /**
     * @param  bool $value 
     * @return TrustCare_Model_FrmCommunity
     */
    public function setIsStiServices($value)
    {
        $this->_parameterChanged('is_sti_services', $value, true);
        $this->_is_sti_services = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|bool
     */
    public function getIsStiServices()
    {
        return !empty($this->_is_sti_services) ? true : false;
    }
    
    
    /**
     * @param  bool $value 
     * @return TrustCare_Model_FrmCommunity
     */
    public function setIsReproductiveHealthServices($value)
    {
        $this->_parameterChanged('is_reproductive_health_services', $value, true);
        $this->_is_reproductive_health_services = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|bool
     */
    public function getIsReproductiveHealthServices()
    {
        return !empty($this->_is_reproductive_health_services) ? true : false;
    }
    
    /**
     * @param  bool $value 
     * @return TrustCare_Model_FrmCommunity
     */
    public function setIsTuberculosisServices($value)
    {
        $this->_parameterChanged('is_tuberculosis_services', $value, true);
        $this->_is_tuberculosis_services = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|bool
     */
    public function getIsTuberculosisServices()
    {
        return !empty($this->_is_tuberculosis_services) ? true : false;
    }
    
    /**
     * @param  bool $value 
     * @return TrustCare_Model_FrmCommunity
     */
    public function setIsOvcServices($value)
    {
        $this->_parameterChanged('is_ovc_services', $value, true);
        $this->_is_ovc_services = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|bool
     */
    public function getIsOvcServices()
    {
        return !empty($this->_is_ovc_services) ? true : false;
    }
    
    /**
     * @param  bool $value 
     * @return TrustCare_Model_FrmCare
     */
    public function setIsPatientYounger15($value)
    {
        $this->_parameterChanged('is_patient_younger_15', $value, true);
        $this->_is_patient_younger_15 = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|bool
     */
    public function getIsPatientYounger15()
    {
        return !empty($this->_is_patient_younger_15) ? true : false;
    }
    
    
    /**
     * @param  bool $value 
     * @return TrustCare_Model_FrmCare
     */
    public function setIsPatientMale($value)
    {
        $this->_parameterChanged('is_patient_male', $value, true);
        $this->_is_patient_male = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|bool
     */
    public function getIsPatientMale()
    {
        return !empty($this->_is_patient_male) ? true : false;
    }
    
    
    public function isExists()
    {
        return !is_null($this->getId());
    }
    
    /**
     * Find an entry by id
     *
     * @param  string $id 
     * @param array|null $options
     * @return TrustCare_Model_FrmCommunity
     */
    public static function find($id, array $options = null)
    {
        $newEntity = new TrustCare_Model_FrmCommunity($options);
        $result = $newEntity->getMapper()->find($id, $newEntity);
        
        if(!$result) {
            unset($newEntity);
            $newEntity = null;
        }
        
        return $newEntity;
    }

    
    public function delete()
    {
    	parent::delete();
    	$this->id = null;
    }
}