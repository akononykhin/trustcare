<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */

class TrustCare_Model_PharmacyDictionary extends TrustCare_Model_Abstract
{
    const DTYPE_MEDICATION_ERROR_TYPE = 1;
    const DTYPE_MEDICATION_ADH_PROBLEM = 2;
    const DTYPE_MED_ERROR_INTERVENTION_PROVIDED = 3;
    const DTYPE_ADH_INTERVENTION_PROVIDED = 4;
    const DTYPE_MED_ERROR_INTERVENTION_OUTCOME = 5;
    const DTYPE_ADH_INTERVENTION_OUTCOME = 6;
    const DTYPE_ADR_SEVERITY_GRADE = 7;
    const DTYPE_ADR_INTERVENTION_TYPE = 8;
    const DTYPE_HEPATIC = 9;
    const DTYPE_NERVOUS = 10;
    const DTYPE_CARDIOVASCULAR = 11;
    const DTYPE_SKIN = 12;
    const DTYPE_METABOLIC = 13;
    const DTYPE_MUSCULOSKELETAL = 14;
    const DTYPE_GENERAL = 15;
    
    const DTYPE_REFERRED_FROM = 16;
    const DTYPE_REFERRED_OUT = 17;
    const DTYPE_HTC_RESULT = 18;
    const DTYPE_PALLIATIVE_CARE_TYPE = 19;
    const DTYPE_REPRODUCTIVE_HEALTH_TYPE = 20;
    const DTYPE_STI_TYPE = 21;
    const DTYPE_TUBERCULOSIS_TYPE = 22;
    const DTYPE_OVC_TYPE = 23;
    const DTYPE_REFERRED_IN = 24;
    const DTYPE_MALARIA_TYPE = 25;
    const DTYPE_COMMUNITY_ADR_INTERVENTION_TYPE = 26;
    
    protected $_id;
    protected $_id_pharmacy_dictionary_type;
    protected $_name;
    protected $_is_active;
    
    public function __construct(array $options = null)
    {
        parent::__construct($options);
        
        if(!$this->isExists()) {
            if(!is_array($options) || !array_key_exists('is_active', $options)) {
                $this->_is_active = true;
            }
        }
    }
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_PharmacyDictionary
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
     * @return TrustCare_Model_PharmacyDictionary
     */
    public function setIdPharmacyDictionaryType($value)
    {
        $this->_parameterChanged('id_pharmacy_dictionary_type', $value);
        $this->_id_pharmacy_dictionary_type = (int) $value;
        return $this;
    }

    /**
     * @return null|int
     */
    public function getIdPharmacyDictionaryType()
    {
        return $this->_id_pharmacy_dictionary_type;
    }
    
    /**
     * @param  string $value 
     * @return TrustCare_Model_PharmacyDictionary
     */
    public function setName($value)
    {
        $this->_parameterChanged('name', $value);
        $this->_name = (string) $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * @param  bool $value
     * @return TrustCare_Model_PharmacyDictionary
     */
    public function setIsActive($value)
    {
        $this->_parameterChanged('is_active', $value, true);
        $this->_is_active = !empty($value) ? true : false;
        return $this;
    }
    
    /**
     * @return null|bool
     */
    public function getIsActive()
    {
        return !empty($this->_is_active) ? true : false;
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
     * @return TrustCare_Model_PharmacyDictionary
     */
    public static function find($id, array $options = null)
    {
        $newEntity = new TrustCare_Model_PharmacyDictionary($options);
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