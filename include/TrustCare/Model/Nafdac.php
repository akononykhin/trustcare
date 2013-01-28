<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */

class TrustCare_Model_Nafdac extends TrustCare_Model_Abstract
{
    const OUTCOME_REACTION_RECOVERED_FULLY = 1;
    const OUTCOME_REACTION_CONGENITAL_ABNORMALITY = 2;
    const OUTCOME_REACTION_DEATH = 3;
    const OUTCOME_REACTION_RECOVERED_WITH_DISABILITY = 4;
    const OUTCOME_REACTION_LIFE_THREATENING = 5;
    const OUTCOME_REACTION_OTHER = 6;
    
    
    protected $_id;
    protected $_id_frm_care;
    protected $_generation_date;
    protected $_filename;
    protected $_adr_description;
    protected $_was_admitted;
    protected $_was_hospitalization_prolonged;
    protected $_duration_of_admission;
    protected $_treatment_of_reaction;
    protected $_outcome_of_reaction_type;
    protected $_outcome_of_reaction_desc;
    protected $_drug_brand_name;
    protected $_drug_generic_name;
    protected $_drug_batch_number;
    protected $_drug_nafdac_number;
    protected $_drug_expiry_name;
    protected $_drug_manufactor;
    protected $_drug_indication_for_use;
    protected $_drug_dosage;
    protected $_drug_route_of_administration;
    protected $_drug_date_started;
    protected $_drug_date_stopped;
    protected $_reporter_name;
    protected $_reporter_address;
    protected $_reporter_profession;
    protected $_reporter_contact;

    
    public function __construct($options = array()) {
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
     * @return TrustCare_Model_Nafdac
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
     * @return TrustCare_Model_Nafdac
     */
    public function setIdFrmCare($value)
    {
        $this->_parameterChanged('id_frm_care', $value);
        $this->_id_frm_care = (int) $value;
        return $this;
    }

    /**
     * @return null|int
     */
    public function getIdFrmCare()
    {
        return $this->_id_frm_care;
    }
    
    /**
     * @param  string $value 
     * @return TrustCare_Model_Nafdac
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
     * @param  value $value
     * @return TrustCare_Model_Nafdac
     */
    public function setFilename($value)
    {
        $this->_parameterChanged('filename', $value);
        $this->_filename = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getFilename()
    {
        return $this->_filename;
    }
    
    
    /**
     * @param  value $value
     * @return TrustCare_Model_Nafdac
     */
    public function setAdrDescription($value)
    {
        $this->_parameterChanged('adr_description', $value);
        $this->_adr_description = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getAdrDescription()
    {
        return $this->_adr_description;
    }
    
    /**
     * @param  bool $value
     * @return TrustCare_Model_Nafdac
     */
    public function setWasAdmitted($value)
    {
        $this->_parameterChanged('was_admitted', $value, true);
        $this->_was_admitted = !empty($value) ? true : false;
        return $this;
    }
    
    /**
     * @return null|bool
     */
    public function getWasAdmitted()
    {
        return !empty($this->_was_admitted) ? true : false;
    }
    
    
    /**
     * @param  bool $value
     * @return TrustCare_Model_Nafdac
     */
    public function setWasHospitalizationProlonged($value)
    {
        $this->_parameterChanged('was_hospitalization_prolonged', $value, true);
        $this->_was_hospitalization_prolonged = !empty($value) ? true : false;
        return $this;
    }
    
    /**
     * @return null|bool
     */
    public function getWasHospitalizationProlonged()
    {
        return !empty($this->_was_hospitalization_prolonged) ? true : false;
    }
    
    /**
     * @param  value $value
     * @return TrustCare_Model_Nafdac
     */
    public function setDurationOfAdmission($value)
    {
        $this->_parameterChanged('duration_of_admission', $value);
        $this->_duration_of_admission = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getDurationOfAdmission()
    {
        return $this->_duration_of_admission;
    }
    
    /**
     * @param  value $value
     * @return TrustCare_Model_Nafdac
     */
    public function setTreatmentOfReaction($value)
    {
        $this->_parameterChanged('treatment_of_reaction', $value);
        $this->_treatment_of_reaction = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getTreatmentOfReaction()
    {
        return $this->_treatment_of_reaction;
    }
    
    
    /**
     * @param  int $value
     * @return TrustCare_Model_Nafdac
     */
    public function setOutcomeOfReactionType($value)
    {
        $this->_parameterChanged('outcome_of_reaction_type', $value);
        $this->_outcome_of_reaction_type = (int) $value;
        return $this;
    }
    
    /**
     * @return null|int
     */
    public function getOutcomeOfReactionType()
    {
        return $this->_outcome_of_reaction_type;
    }
    
    /**
     * @param  string $value
     * @return TrustCare_Model_Nafdac
     */
    public function setOutcomeOfReactionDesc($value)
    {
        $this->_parameterChanged('outcome_of_reaction_desc', $value);
        $this->_outcome_of_reaction_desc = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getOutcomeOfReactionDesc()
    {
        return $this->_outcome_of_reaction_desc;
    }
    
    /**
     * @param  string $value
     * @return TrustCare_Model_Nafdac
     */
    public function setDrugBrandName($value)
    {
        $this->_parameterChanged('drug_brand_name', $value);
        $this->_drug_brand_name = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getDrugBrandName()
    {
        return $this->_drug_brand_name;
    }
    
    /**
     * @param  string $value
     * @return TrustCare_Model_Nafdac
     */
    public function setDrugGenericName($value)
    {
        $this->_parameterChanged('drug_generic_name', $value);
        $this->_drug_generic_name = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getDrugGenericName()
    {
        return $this->_drug_generic_name;
    }
    
    /**
     * @param  string $value
     * @return TrustCare_Model_Nafdac
     */
    public function setDrugBatchNumber($value)
    {
        $this->_parameterChanged('drug_batch_number', $value);
        $this->_drug_batch_number = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getDrugBatchNumber()
    {
        return $this->_drug_batch_number;
    }
    
    /**
     * @param  string $value
     * @return TrustCare_Model_Nafdac
     */
    public function setDrugNafdacNumber($value)
    {
        $this->_parameterChanged('drug_nafdac_number', $value);
        $this->_drug_nafdac_number = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getDrugNafdacNumber()
    {
        return $this->_drug_nafdac_number;
    }
    
    /**
     * @param  string $value
     * @return TrustCare_Model_Nafdac
     */
    public function setDrugExpiryName($value)
    {
        $this->_parameterChanged('drug_expiry_name', $value);
        $this->_drug_expiry_name = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getDrugExpiryName()
    {
        return $this->_drug_expiry_name;
    }
    
    /**
     * @param  string $value
     * @return TrustCare_Model_Nafdac
     */
    public function setDrugManufactor($value)
    {
        $this->_parameterChanged('drug_manufactor', $value);
        $this->_drug_manufactor = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getDrugManufactor()
    {
        return $this->_drug_manufactor;
    }
    
    /**
     * @param  string $value
     * @return TrustCare_Model_Nafdac
     */
    public function setDrugIndicationForUse($value)
    {
        $this->_parameterChanged('drug_indication_for_use', $value);
        $this->_drug_indication_for_use = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getDrugIndicationForUse()
    {
        return $this->_drug_indication_for_use;
    }
    
    /**
     * @param  string $value
     * @return TrustCare_Model_Nafdac
     */
    public function setDrugDosage($value)
    {
        $this->_parameterChanged('drug_dosage', $value);
        $this->_drug_dosage = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getDrugDosage()
    {
        return $this->_drug_dosage;
    }
    
    /**
     * @param  string $value
     * @return TrustCare_Model_Nafdac
     */
    public function setDrugRouteOfAdministration($value)
    {
        $this->_parameterChanged('drug_route_of_administration', $value);
        $this->_drug_route_of_administration = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getDrugRouteOfAdministration()
    {
        return $this->_drug_route_of_administration;
    }
    
    /**
     * @param  string $value
     * @return TrustCare_Model_Nafdac
     */
    public function setDrugDateStarted($value)
    {
        $this->_parameterChanged('drug_date_started', $value);
        $this->_drug_date_started = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getDrugDateStarted()
    {
        return $this->_drug_date_started;
    }
    
    /**
     * @param  string $value
     * @return TrustCare_Model_Nafdac
     */
    public function setDrugDateStopped($value)
    {
        $this->_parameterChanged('drug_date_stopped', $value);
        $this->_drug_date_stopped = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getDrugDateStopped()
    {
        return $this->_drug_date_stopped;
    }
    
    /**
     * @param  string $value
     * @return TrustCare_Model_Nafdac
     */
    public function setReporterName($value)
    {
        $this->_parameterChanged('reporter_name', $value);
        $this->_reporter_name = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getReporterName()
    {
        return $this->_reporter_name;
    }
    
    /**
     * @param  string $value
     * @return TrustCare_Model_Nafdac
     */
    public function setReporterAddress($value)
    {
        $this->_parameterChanged('reporter_address', $value);
        $this->_reporter_address = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getReporterAddress()
    {
        return $this->_reporter_address;
    }
    
    /**
     * @param  string $value
     * @return TrustCare_Model_Nafdac
     */
    public function setReporterProfession($value)
    {
        $this->_parameterChanged('reporter_profession', $value);
        $this->_reporter_profession = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getReporterProfession()
    {
        return $this->_reporter_profession;
    }
    
    /**
     * @param  string $value
     * @return TrustCare_Model_Nafdac
     */
    public function setReporterContact($value)
    {
        $this->_parameterChanged('reporter_contact', $value);
        $this->_reporter_contact = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getReporterContact()
    {
        return $this->_reporter_contact;
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
     * @return TrustCare_Model_Nafdac
     */
    public static function find($id, array $options = null)
    {
        $newEntity = new TrustCare_Model_Nafdac($options);
        $result = $newEntity->getMapper()->find($id, $newEntity);
        
        if(!$result) {
            unset($newEntity);
            $newEntity = null;
        }
        
        return $newEntity;
    }

    
    /**
     * Find an entry by id_frm_care
     *
     * @param  string $value
     * @param array|null $options
     * @return TrustCare_Model_Nafdac
     */
    public static function findByIdFrmCare($value, array $options = null)
    {
        $newEntity = new TrustCare_Model_Nafdac($options);
        $result = $newEntity->getMapper()->findByIdFrmCare($value, $newEntity);
    
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
    
    public static function getOutcomeReactionTypes()
    {
        $types = array();
        
        $types[TrustCare_Model_Nafdac::OUTCOME_REACTION_RECOVERED_FULLY] = Zend_Registry::get("Zend_Translate")->_("Recovered Fully");
        $types[TrustCare_Model_Nafdac::OUTCOME_REACTION_CONGENITAL_ABNORMALITY] = Zend_Registry::get("Zend_Translate")->_("Congenital Abnormality");
        $types[TrustCare_Model_Nafdac::OUTCOME_REACTION_DEATH] = Zend_Registry::get("Zend_Translate")->_("Death");
        $types[TrustCare_Model_Nafdac::OUTCOME_REACTION_RECOVERED_WITH_DISABILITY] = Zend_Registry::get("Zend_Translate")->_("Recovered with Disability");
        $types[TrustCare_Model_Nafdac::OUTCOME_REACTION_LIFE_THREATENING] = Zend_Registry::get("Zend_Translate")->_("Life Threatening");
        $types[TrustCare_Model_Nafdac::OUTCOME_REACTION_OTHER] = Zend_Registry::get("Zend_Translate")->_("Other");
        
        return $types;
    }
}