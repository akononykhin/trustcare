<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */

class TrustCare_Model_Physician extends TrustCare_Model_Abstract
{
    protected $_id;
    protected $_identifier;
    protected $_address;
    protected $_first_name;
    protected $_last_name;
    protected $_id_lga;
    protected $_id_country;
    protected $_id_state;
    protected $_id_facility;
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_Physician
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
     * @return TrustCare_Model_Physician
     */
    public function setIdentifier($value)
    {
        $this->_parameterChanged('identifier', $value);
    	$this->_identifier = (string) $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getIdentifier()
    {
        return $this->_identifier;
    }

    
    /**
     * @param  string $value 
     * @return TrustCare_Model_Physician
     */
    public function setFirstName($value)
    {
        $this->_parameterChanged('first_name', $value);
        $this->_first_name = (string) $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getFirstName()
    {
        return $this->_first_name;
    }
    
    /**
     * @param  string $value 
     * @return TrustCare_Model_Physician
     */
    public function setLastName($value)
    {
        $this->_parameterChanged('last_name', $value);
        $this->_last_name = (string) $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getLastName()
    {
        return $this->_last_name;
    }
    
    /**
     * @param  string $value 
     * @return TrustCare_Model_Physician
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
     * @return TrustCare_Model_Physician
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
     * @return TrustCare_Model_Physician
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
     * @return TrustCare_Model_Physician
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
     * @return TrustCare_Model_Physician
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
    
    /**
     * Find an entry by id
     *
     * @param  string $id 
     * @param array|null $options
     * @return TrustCare_Model_Physician
     */
    public static function find($id, array $options = null)
    {
        $newEntity = new TrustCare_Model_Physician($options);
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
     * @return TrustCare_Model_Physician
     */
    public static function findByIdentifier($value, array $options = null)
    {
        $newEntity = new TrustCare_Model_Physician($options);
        $result = $newEntity->getMapper()->findByIdentifier($value, $newEntity);
        
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