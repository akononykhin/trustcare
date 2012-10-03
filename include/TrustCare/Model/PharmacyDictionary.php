<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */

class TrustCare_Model_PharmacyDictionary extends TrustCare_Model_Abstract
{
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