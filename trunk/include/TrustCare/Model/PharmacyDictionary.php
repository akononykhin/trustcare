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
    const DTYPE_ADH_INTERVENTION_PROVIDED = 3;
    const DTYPE_ADH_INTERVENTION_OUTCOME = 4;
    const DTYPE_ADR_SEVERITY_GRADE = 5;
    const DTYPE_ADR_INTERVENTION_TYPE = 6;
    const DTYPE_HEPATIC = 7;
    const DTYPE_NERVOUS = 8;
    const DTYPE_CARDIOVASCULAR = 9;
    const DTYPE_SKIN = 10;
    const DTYPE_METABOLIC = 11;
    const DTYPE_MUSCULOSKELETAL = 12;
    const DTYPE_GENERAL = 13;
    
    const DTYPE_REFERRED_IN = 14;
    const DTYPE_REFERRED_OUT = 15;
    const DTYPE_HTC_RESULT = 16;
    
    protected $_id;
    protected $_id_pharmacy_dictionary_type;
    protected $_name;
    
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