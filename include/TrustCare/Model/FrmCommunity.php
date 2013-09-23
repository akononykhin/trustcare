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
    protected $_generation_date;
    protected $_id_user;
    protected $_is_commited;
    protected $_id_pharmacy;
    protected $_id_patient;
    protected $_date_of_visit;
    protected $_date_of_visit_month_index;
    protected $_is_first_visit_to_pharmacy;
    protected $_is_referred_in;
    protected $_is_referred_out;
    protected $_is_referral_completed;
    protected $_is_hiv_risk_assesment_done;
    protected $_is_htc_done;
    protected $_htc_result_id;
    protected $_is_client_received_htc;
    protected $_is_htc_done_in_current_pharmacy;
    protected $_is_palliative_services_to_plwha;
    protected $_is_sti_services;
    protected $_is_reproductive_health_services;
    protected $_is_tuberculosis_services;
    protected $_is_ovc_services;
    protected $_is_patient_younger_15;
    protected $_is_patient_male;
    protected $_id_nafdac;
    
    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->_logObjectChanges = false;
    
        if(is_null($this->getId())) {
            if(!is_array($options) || !array_key_exists('generation_date', $options)) {
                $this->_generation_date = ZendX_Db_Table_Abstract::LABEL_NOW;
            }
        }
    }
    
    
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
     * @param  string $value
     * @return TrustCare_Model_FrmCare
     */
    public function setGenerationDate($value)
    {
        $this->_parameterChanged('generation_date', $value);
        $this->_generation_date = (string) $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getGenerationDate()
    {
        return $this->_generation_date;
    }
    
    
    /**
     * @param  int $value
     * @return TrustCare_Model_FrmCommunity
     */
    public function setIdUser($value)
    {
        $this->_parameterChanged('id_user', $value);
        $this->_id_user = (int) $value;
        return $this;
    }
    
    /**
     * @return null|int
     */
    public function getIdUser()
    {
        return $this->_id_user;
    }
    
    
    /**
     * @param  bool $value
     * @return TrustCare_Model_FrmCare
     */
    public function setIsCommited($value)
    {
        $this->_parameterChanged('is_commited', $value, true);
        $this->_is_commited = !empty($value) ? true : false;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getIsCommited()
    {
        return !empty($this->_is_commited) ? true : false;
    }
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_FrmCommunity
     */
    public function setIdPharmacy($value)
    {
        $this->_parameterChanged('id_pharmacy', $value);
    	$this->_id_pharmacy = (int) $value;
        return $this;
    }

    /**
     * @return null|int
     */
    public function getIdPharmacy()
    {
        return $this->_id_pharmacy;
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
    public function setIsFirstVisitToPharmacy($value)
    {
        $this->_parameterChanged('is_first_visit_to_pharmacy', $value, true);
    	$this->_is_first_visit_to_pharmacy = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|bool
     */
    public function getIsFirstVisitToPharmacy()
    {
        return !empty($this->_is_first_visit_to_pharmacy) ? true : false;
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
     * @param  int $value 
     * @return TrustCare_Model_FrmCommunity
     */
    public function setHtcResultId($value)
    {
        $this->_parameterChanged('htc_result_id', $value);
    	$this->_htc_result_id = (int) $value;
        return $this;
    }

    /**
     * @return null|int
     */
    public function getHtcResultId()
    {
        return $this->_htc_result_id;
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
    
    
    /**
     * @param  int $value
     * @return TrustCare_Model_FrmCommunity
     */
    public function setIdNafdac($value)
    {
        $this->_parameterChanged('id_nafdac', $value);
        $this->_id_nafdac = (int) $value;
        return $this;
    }
    
    /**
     * @return null|int
     */
    public function getIdNafdac()
    {
        return $this->_id_nafdac;
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


    /**
     * 
     * Get the number of forms generated for specified patient
     * 
     * @param int $patientId
     * @param array $options
     */
    public static function getNumberOfFormsForPatient($patientId, array $options = null)
    {
        $model = new TrustCare_Model_FrmCommunity($options);
        $foundNum = $model->getMapper()->getNumberOfFormsForPatient($patientId);
        
        return $foundNum;
        
    }
    
    
    /**
     * 
     * Check either specified patient has already visited specified pharmacy
     * @param int $patientId
     * @param int $pharmacyId
     * @param array $options
     * 
     * @return bool
     */
    public static function isFirstVisitOfPatientToPharmacy($patientId, $pharmacyId, array $options = null)
    {
        $model = new TrustCare_Model_FrmCommunity($options);
        return $model->getMapper()->isFirstVisitOfPatientToPharmacy($patientId, $pharmacyId);
    }
    
    public function delete()
    {
    	parent::delete();
    	$this->id = null;
    }
}