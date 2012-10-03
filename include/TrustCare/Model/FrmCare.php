<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */

class TrustCare_Model_FrmCare extends TrustCare_Model_Abstract
{
    protected $_id;
    protected $_id_patient;
    protected $_date_of_visit;
    protected $_is_pregnant;
    protected $_is_receive_prescription;
    protected $_is_med_error_screened;
    protected $_is_med_error_identified;
    protected $_is_med_adh_problem_screened;
    protected $_is_med_adh_problem_identified;
    protected $_is_adh_intervention_provided;
    protected $_is_adr_screened;
    protected $_is_adr_symptoms;
    protected $_adr_start_date;
    protected $_adr_stop_date;
    protected $_is_adr_intervention_provided;
    protected $_is_nafdac_adr_filled;
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_FrmCare
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
     * @return TrustCare_Model_FrmCare
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
     * @return TrustCare_Model_FrmCare
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
     * @param  bool $value 
     * @return TrustCare_Model_FrmCare
     */
    public function setIsPregnant($value)
    {
        $this->_parameterChanged('is_pregnant', $value, true);
    	$this->_is_pregnant = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getIsPregnant()
    {
        return !empty($this->_is_pregnant) ? true : false;
    }
    
    /**
     * @param  bool $value 
     * @return TrustCare_Model_FrmCare
     */
    public function setIsReceivePrescription($value)
    {
        $this->_parameterChanged('is_receive_prescription', $value, true);
        $this->_is_receive_prescription = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getIsReceivePrescription()
    {
        return !empty($this->_is_receive_prescription) ? true : false;
    }
    
    /**
     * @param  bool $value 
     * @return TrustCare_Model_FrmCare
     */
    public function setIsMedErrorScreened($value)
    {
        $this->_parameterChanged('is_med_error_screened', $value, true);
        $this->_is_med_error_screened = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getIsMedErrorScreened()
    {
        return !empty($this->_is_med_error_screened) ? true : false;
    }
    
    /**
     * @param  bool $value 
     * @return TrustCare_Model_FrmCare
     */
    public function setIsMedErrorIdentified($value)
    {
        $this->_parameterChanged('is_med_error_identified', $value, true);
        $this->_is_med_error_identified = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getIsMedErrorIdentified()
    {
        return !empty($this->_is_med_error_identified) ? true : false;
    }
    
    /**
     * @param  bool $value 
     * @return TrustCare_Model_FrmCare
     */
    public function setIsMedAdhProblemScreened($value)
    {
        $this->_parameterChanged('is_med_adh_problem_screened', $value, true);
        $this->_is_med_adh_problem_screened = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|bool
     */
    public function getIsMedAdhProblemScreened()
    {
        return !empty($this->_is_med_adh_problem_screened) ? true : false;
    }
    
    /**
     * @param  bool $value 
     * @return TrustCare_Model_FrmCare
     */
    public function setIsMedAdhProblemIdentified($value)
    {
        $this->_parameterChanged('is_med_adh_problem_identified', $value, true);
        $this->_is_med_adh_problem_identified = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|bool
     */
    public function getIsMedAdhProblemIdentified()
    {
        return !empty($this->_is_med_adh_problem_identified) ? true : false;
    }
    
    /**
     * @param  bool $value 
     * @return TrustCare_Model_FrmCare
     */
    public function setIsAdhInterventionProvided($value)
    {
        $this->_parameterChanged('is_adh_intervention_provided', $value, true);
        $this->_is_adh_intervention_provided = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|bool
     */
    public function getIsAdhInterventionProvided()
    {
        return !empty($this->_is_adh_intervention_provided) ? true : false;
    }
    
    /**
     * @param  bool $value 
     * @return TrustCare_Model_FrmCare
     */
    public function setIsAdrScreened($value)
    {
        $this->_parameterChanged('is_adr_screened', $value, true);
        $this->_is_adr_screened = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|bool
     */
    public function getIsAdrScreened()
    {
        return !empty($this->_is_adr_screened) ? true : false;
    }
    
    /**
     * @param  bool $value 
     * @return TrustCare_Model_FrmCare
     */
    public function setIsAdrSymptoms($value)
    {
        $this->_parameterChanged('is_adr_symptoms', $value, true);
        $this->_is_adr_symptoms = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|bool
     */
    public function getIsAdrSymptoms()
    {
        return !empty($this->_is_adr_symptoms) ? true : false;
    }
    
    /**
     * @param  string $value 
     * @return TrustCare_Model_FrmCare
     */
    public function setAdrStartDate($value)
    {
        $this->_parameterChanged('adr_start_date', $value);
        $this->_adr_start_date = (string) $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getAdrStartDate()
    {
        return $this->_adr_start_date;
    }
    
    /**
     * @param  string $value 
     * @return TrustCare_Model_FrmCare
     */
    public function setAdrStopDate($value)
    {
        $this->_parameterChanged('adr_stop_date', $value);
        $this->_adr_stop_date = (string) $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getAdrStopDate()
    {
        return $this->_adr_stop_date;
    }
    
    /**
     * @param  bool $value 
     * @return TrustCare_Model_FrmCare
     */
    public function setIsAdrInterventionProvided($value)
    {
        $this->_parameterChanged('is_adr_intervention_provided', $value, true);
        $this->_is_adr_intervention_provided = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|bool
     */
    public function getIsAdrInterventionProvided()
    {
        return !empty($this->_is_adr_intervention_provided) ? true : false;
    }
    
    /**
     * @param  bool $value 
     * @return TrustCare_Model_FrmCare
     */
    public function setIsNafdacAdrFilled($value)
    {
        $this->_parameterChanged('is_nafdac_adr_filled', $value, true);
        $this->_is_nafdac_adr_filled = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|bool
     */
    public function getIsNafdacAdrFilled()
    {
        return !empty($this->_is_nafdac_adr_filled) ? true : false;
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
     * @return TrustCare_Model_FrmCare
     */
    public static function find($id, array $options = null)
    {
        $newEntity = new TrustCare_Model_FrmCare($options);
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