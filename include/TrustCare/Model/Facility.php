<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */

class TrustCare_Model_Facility extends TrustCare_Model_Abstract
{
    protected $_id;
    protected $_name;
    protected $_sn;
    protected $_id_lga;
    protected $_id_facility_type;
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_Facility
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
     * @return TrustCare_Model_Facility
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
     * @param  string $value
     * @return TrustCare_Model_Facility
     */
    public function setSn($value)
    {
        $this->_parameterChanged('sn', $value);
        $this->_sn = (string) $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getSn()
    {
        return $this->_sn;
    }
    
    
    /**
     * @param  int|null $value
     * @return TrustCare_Model_Lga
     */
    public function setIdLga($value)
    {
        $this->_parameterChanged('id_lga', $value);
        $this->_id_lga = !is_null($value) ? (int) $value : null;
        return $this;
    }
    
    /**
     * @return null|int
     */
    public function getIdLga()
    {
        return $this->_id_lga;
    }
    

    
    /**
     * @param  int|null $value
     * @return TrustCare_Model_Lga
     */
    public function setIdFacilityType($value)
    {
        $this->_parameterChanged('id_facility_type', $value);
        $this->_id_facility_type = !is_null($value) ? (int) $value : null;
        return $this;
    }
    
    /**
     * @return null|int
     */
    public function getIdFacilityType()
    {
        return $this->_id_facility_type;
    }
    
    /**
     * @param  int|null $value
     * @return TrustCare_Model_Lga
     */
    public function setIdFacilityLevel($value)
    {
        $this->_parameterChanged('id_facility_level', $value);
        $this->_id_facility_level = !is_null($value) ? (int) $value : null;
        return $this;
    }
    
    /**
     * @return null|int
     */
    public function getIdFacilityLevel()
    {
        return $this->_id_facility_level;
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
     * @return TrustCare_Model_Facility
     */
    public static function find($id, array $options = null)
    {
        $newEntity = new TrustCare_Model_Facility($options);
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