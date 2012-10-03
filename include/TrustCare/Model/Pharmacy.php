<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */

class TrustCare_Model_Pharmacy extends TrustCare_Model_Abstract
{
    protected $_id;
    protected $_name;
    protected $_is_active;
    protected $_address;
    protected $_id_lga;
    protected $_id_country;
    protected $_id_state;
    protected $_id_facility;
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_Pharmacy
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
     * @return TrustCare_Model_Pharmacy
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
     * @return TrustCare_Model_Pharmacy
     */
    public function setIsActive($value)
    {
        $this->_parameterChanged('is_active', $value, true);
        $this->_is_active = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getIsActive()
    {
        return !empty($this->_is_active) ? true : false;
    }
    
    
    /**
     * @param  string $value 
     * @return TrustCare_Model_Pharmacy
     */
    public function setAddress($value)
    {
        $this->_parameterChanged('address', $value);
        $this->_address = (string) $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getAddress()
    {
        return $this->_address;
    }
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_Pharmacy
     */
    public function setIdLga($value)
    {
        $this->_parameterChanged('id_lga', $value);
        $this->_id_lga = (int) $value;
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
     * @param  int $value 
     * @return TrustCare_Model_Pharmacy
     */
    public function setIdCountry($value)
    {
        $this->_parameterChanged('id_country', $value);
        $this->_id_country = (int) $value;
        return $this;
    }

    /**
     * @return null|int
     */
    public function getIdCountry()
    {
        return $this->_id_country;
    }
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_Pharmacy
     */
    public function setIdState($value)
    {
        $this->_parameterChanged('id_state', $value);
        $this->_id_state = (int) $value;
        return $this;
    }

    /**
     * @return null|int
     */
    public function getIdState()
    {
        return $this->_id_state;
    }

    
    /**
     * @param  int $value 
     * @return TrustCare_Model_Pharmacy
     */
    public function setIdFacility($value)
    {
        $this->_parameterChanged('id_facility', $value);
        $this->_id_facility = (int) $value;
        return $this;
    }

    /**
     * @return null|int
     */
    public function getIdFacility()
    {
        return $this->_id_facility;
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
     * @return TrustCare_Model_Pharmacy
     */
    public static function find($id, array $options = null)
    {
        $newEntity = new TrustCare_Model_Pharmacy($options);
        $result = $newEntity->getMapper()->find($id, $newEntity);
        
        if(!$result) {
            unset($newEntity);
            $newEntity = null;
        }
        
        return $newEntity;
    }

    /**
     * Find an entry by name
     *
     * @param  string $value - name 
     * @param array|null $options
     * @return TrustCare_Model_Pharmacy
     */
    public static function findByName($value, array $options = null)
    {
        $newEntity = new TrustCare_Model_Pharmacy($options);
        $result = $newEntity->getMapper()->findByName($value, $newEntity);
        
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