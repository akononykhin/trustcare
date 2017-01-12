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
    const OUTCOME_REACTION_RECOVERING = 2;
    const OUTCOME_REACTION_NOT_RECOVERED = 4;
    const OUTCOME_REACTION_DEATH = 3;
    const OUTCOME_REACTION_UNKNOWN = 6;
    
    const SUBSIDED_YES = 'yes';
    const SUBSIDED_NO = 'no';
    const SUBSIDED_UNKNOWN = 'unknown';
    const SUBSIDED_NA = 'na';

    const REAPPEARED_YES = 'yes';
    const REAPPEARED_NO = 'no';
    const REAPPEARED_UNKNOWN = 'unknown';
    const REAPPEARED_NA = 'na';

    const EXTENT_MILD = 'mild';
    const EXTENT_MODERATE = 'moderate';
    const EXTENT_SEVERE = 'severe';
    
    const SERIOUSNESS_LIFE_THREAT = 1;
    const SERIOUSNESS_HOSPITAL = 2;
    const SERIOUSNESS_DISABILITY = 3;
    const SERIOUSNESS_BIRTH_DEFECT = 4;
    const SERIOUSNESS_NA = 5;

    const DRUG_REACTION_CERTAIN = 'certain';
    const DRUG_REACTION_PROBABLE = 'probable';
    const DRUG_REACTION_POSSIBLE = 'possible';
    const DRUG_REACTION_UNLIKELY = 'unlikely';
    const DRUG_REACTION_UNCLASSIFIED = 'unclassified';
    
    protected $_id;
    protected $_id_user;
    protected $_id_patient;
    protected $_id_pharmacy;
    protected $_generation_date;
    protected $_date_of_visit;
    protected $_filename;
    protected $_adr_start_date;
    protected $_adr_stop_date;
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
    protected $_reporter_email;
    protected $_onset_time;
    protected $_onset_type;
    protected $_subsided;
    protected $_reappeared;
    protected $_extent;
    protected $_seriousness;
    protected $_relationship;
    protected $_relevant_data;
    protected $_relevant_history;
    
    
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
     * @param  string $value
     * @return TrustCare_Model_Nafdac
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
     * @return TrustCare_Model_Nafdac
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
     * @param  int $value
     * @return TrustCare_Model_Nafdac
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
     * @param  int $value
     * @return TrustCare_Model_Nafdac
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
     * @param  string $value
     * @return TrustCare_Model_Nafdac
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
     * @return TrustCare_Model_Nafdac
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
    

    /**
     * @param  string $value
     * @return TrustCare_Model_Nafdac
     */
    public function setReporterEmail($value)
    {
        $this->_parameterChanged('reporter_email', $value);
        $this->_reporter_email = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getReporterEmail()
    {
        return $this->_reporter_email;
    }

    /**
     * @param  null|int $value
     * @return TrustCare_Model_Nafdac
     */
    public function setOnsetTime($value)
    {
        $this->_parameterChanged('onset_time', $value);
        $this->_onset_time = !is_null($value) ? (int) $value : null;
        return $this;
    }
    
    /**
     * @return null|int
     */
    public function getOnsetTime()
    {
        return $this->_onset_time;
    }

    /**
     * @param  string $value
     * @return TrustCare_Model_Nafdac
     */
    public function setOnsetType($value)
    {
        $this->_parameterChanged('onset_type', $value);
        $this->_onset_type = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getOnsetType()
    {
        return $this->_onset_type;
    }
    

    /**
     * @param  string $value
     * @return TrustCare_Model_Nafdac
     */
    public function setSubsided($value)
    {
        $this->_parameterChanged('subsided', $value);
        $this->_subsided = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getSubsided()
    {
        return $this->_subsided;
    }

    /**
     * @param  string $value
     * @return TrustCare_Model_Nafdac
     */
    public function setReappeared($value)
    {
        $this->_parameterChanged('reappeared', $value);
        $this->_reappeared = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getReappeared()
    {
        return $this->_reappeared;
    }


    /**
     * @param  string $value
     * @return TrustCare_Model_Nafdac
     */
    public function setExtent($value)
    {
        $this->_parameterChanged('extent', $value);
        $this->_extent = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getExtent()
    {
        return $this->_extent;
    }

    /**
     * @param  null|int $value
     * @return TrustCare_Model_Nafdac
     */
    public function setSeriousness($value)
    {
        $this->_parameterChanged('seriousness', $value);
        $this->_seriousness = !is_null($value) ? (int) $value : null;
        return $this;
    }
    
    /**
     * @return null|int
     */
    public function getSeriousness()
    {
        return $this->_seriousness;
    }

    /**
     * @param  string $value
     * @return TrustCare_Model_Nafdac
     */
    public function setRelationship($value)
    {
        $this->_parameterChanged('relationship', $value);
        $this->_relationship = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getRelationship()
    {
        return $this->_relationship;
    }

    /**
     * @param  string $value
     * @return TrustCare_Model_Nafdac
     */
    public function setRelevantData($value)
    {
        $this->_parameterChanged('relevant_data', $value);
        $this->_relevant_data = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getRelevantData()
    {
        return $this->_relevant_data;
    }

    /**
     * @param  string $value
     * @return TrustCare_Model_Nafdac
     */
    public function setRelevantHistory($value)
    {
        $this->_parameterChanged('relevant_history', $value);
        $this->_relevant_history = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getRelevantHistory()
    {
        return $this->_relevant_history;
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

    
    public function delete()
    {
        parent::delete();
        $this->id = null;
    }
    
    public static function getOutcomeReactionTypes()
    {
        $types = array();
        
        $types[TrustCare_Model_Nafdac::OUTCOME_REACTION_RECOVERED_FULLY] = Zend_Registry::get("Zend_Translate")->_("Recovered Fully");
        $types[TrustCare_Model_Nafdac::OUTCOME_REACTION_RECOVERING] = Zend_Registry::get("Zend_Translate")->_("Recovering");
        $types[TrustCare_Model_Nafdac::OUTCOME_REACTION_NOT_RECOVERED] = Zend_Registry::get("Zend_Translate")->_("Not recovered");
        $types[TrustCare_Model_Nafdac::OUTCOME_REACTION_UNKNOWN] = Zend_Registry::get("Zend_Translate")->_("Unknown");
        $types[TrustCare_Model_Nafdac::OUTCOME_REACTION_DEATH] = Zend_Registry::get("Zend_Translate")->_("Fatal");
        
        return $types;
    }
    
    public static function getSubsidedValues()
    {
        $values = array();
        
        $values[TrustCare_Model_Nafdac::SUBSIDED_YES] = Zend_Registry::get("Zend_Translate")->_("Yes");
        $values[TrustCare_Model_Nafdac::SUBSIDED_NO] = Zend_Registry::get("Zend_Translate")->_("No");
        $values[TrustCare_Model_Nafdac::SUBSIDED_UNKNOWN] = Zend_Registry::get("Zend_Translate")->_("Unknown");
        $values[TrustCare_Model_Nafdac::SUBSIDED_NA] = Zend_Registry::get("Zend_Translate")->_("N/A (drug continued)");
        
        return $values;
    }
    
    public static function getReappearedValues()
    {
        $values = array();
        
        $values[TrustCare_Model_Nafdac::REAPPEARED_YES] = Zend_Registry::get("Zend_Translate")->_("Yes");
        $values[TrustCare_Model_Nafdac::REAPPEARED_NO] = Zend_Registry::get("Zend_Translate")->_("No");
        $values[TrustCare_Model_Nafdac::REAPPEARED_UNKNOWN] = Zend_Registry::get("Zend_Translate")->_("Unknown");
        $values[TrustCare_Model_Nafdac::REAPPEARED_NA] = Zend_Registry::get("Zend_Translate")->_("N/A (not reintroduced)");
        
        return $values;
    }
    
    public static function getExtentValues()
    {
        $values = array();
        
        $values[TrustCare_Model_Nafdac::EXTENT_MILD] = Zend_Registry::get("Zend_Translate")->_("Mild");
        $values[TrustCare_Model_Nafdac::EXTENT_MODERATE] = Zend_Registry::get("Zend_Translate")->_("Moderate");
        $values[TrustCare_Model_Nafdac::EXTENT_SEVERE] = Zend_Registry::get("Zend_Translate")->_("Severe");
        
        return $values;
    }
    
    public static function getSeriousnessValues()
    {
        $values = array();
        
        $values[TrustCare_Model_Nafdac::SERIOUSNESS_LIFE_THREAT] = Zend_Registry::get("Zend_Translate")->_("Life threatening");
        $values[TrustCare_Model_Nafdac::SERIOUSNESS_HOSPITAL] = Zend_Registry::get("Zend_Translate")->_("Caused or prolonged hospitalisation");
        $values[TrustCare_Model_Nafdac::SERIOUSNESS_DISABILITY] = Zend_Registry::get("Zend_Translate")->_("Caused disability or incapacity");
        $values[TrustCare_Model_Nafdac::SERIOUSNESS_BIRTH_DEFECT] = Zend_Registry::get("Zend_Translate")->_("Caused birth defect");
        $values[TrustCare_Model_Nafdac::SERIOUSNESS_NA] = Zend_Registry::get("Zend_Translate")->_("N/A (not serious)");
        
        return $values;
    }
    
    public static function getRelationshipValues()
    {
        $values = array();
        
        $values[TrustCare_Model_Nafdac::DRUG_REACTION_CERTAIN] = Zend_Registry::get("Zend_Translate")->_("Certain");
        $values[TrustCare_Model_Nafdac::DRUG_REACTION_PROBABLE] = Zend_Registry::get("Zend_Translate")->_("Probable");
        $values[TrustCare_Model_Nafdac::DRUG_REACTION_POSSIBLE] = Zend_Registry::get("Zend_Translate")->_("Possible");
        $values[TrustCare_Model_Nafdac::DRUG_REACTION_UNLIKELY] = Zend_Registry::get("Zend_Translate")->_("Unlikely");
        $values[TrustCare_Model_Nafdac::DRUG_REACTION_UNCLASSIFIED] = Zend_Registry::get("Zend_Translate")->_("Unclassifiable");
        
        return $values;
    }
}