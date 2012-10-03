<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */

class TrustCare_Model_Patient extends TrustCare_Model_Abstract
{
    protected $_id;
    protected $_identifier;
    protected $_is_active;
    protected $_first_name;
    protected $_last_name;
    protected $_id_country;
    protected $_id_state;
    protected $_city;
    protected $_address;
    protected $_phone;
    protected $_zip;
    protected $_birthdate;
    protected $_is_mail;
    protected $_id_physician;
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_Patient
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
     * @return TrustCare_Model_Patient
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
     * @return TrustCare_Model_Patient
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
     * @return TrustCare_Model_Patient
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
     * @return TrustCare_Model_Patient
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
     * @return TrustCare_Model_Patient
     */
    public function setCity($value)
    {
        $this->_parameterChanged('city', $value);
        $this->_city = (string) $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getCity()
    {
        return $this->_city;
    }
    
    /**
     * @param  string $value 
     * @return TrustCare_Model_Patient
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
     * @param  string $value 
     * @return TrustCare_Model_Patient
     */
    public function setZip($value)
    {
        $this->_parameterChanged('zip', $value);
        $this->_zip = (string) $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getZip()
    {
        return $this->_zip;
    }
    
    /**
     * @param  string $value 
     * @return TrustCare_Model_Patient
     */
    public function setPhone($value)
    {
        $this->_parameterChanged('phone', $value);
        $this->_phone = (string) $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getPhone()
    {
        return $this->_phone;
    }

    
    /**
     * @param  string $value 
     * @return TrustCare_Model_Patient
     */
    public function setBirthdate($value)
    {
        $this->_parameterChanged('birthdate', $value);
        $this->_birthdate = (string) $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getBirthdate()
    {
        return $this->_birthdate;
    }
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_Patient
     */
    public function setIdPhysician($value)
    {
        $this->_parameterChanged('id_physician', $value);
        $this->_id_physician = (int) $value;
        return $this;
    }

    /**
     * @return null|int
     */
    public function getIdPhysician()
    {
        return $this->_id_physician;
    }
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_Patient
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
     * @return TrustCare_Model_Patient
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
     * @param  string $value 
     * @return TrustCare_Model_Patient
     */
    public function setIsMale($value)
    {
        $this->_parameterChanged('is_male', $value, true);
        $this->_is_male = !empty($value) ? true : false;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getIsMale()
    {
        return !empty($this->_is_male) ? true : false;
    }

    
    public function isExists() {
        return !is_null($this->getId());
    }
    
    /**
     * Find an entry by id
     *
     * @param  string $id 
     * @param array|null $options
     * @return TrustCare_Model_Patient
     */
    public static function find($id, array $options = null)
    {
        $newEntity = new TrustCare_Model_Patient($options);
        $result = $newEntity->getMapper()->find($id, $newEntity);
        
        if(!$result) {
            unset($newEntity);
            $newEntity = null;
        }
        
        return $newEntity;
    }

    /**
     * Find an entry by login
     *
     * @param  string $value - login 
     * @param array|null $options
     * @return TrustCare_Model_Patient
     */
    public static function findByIdentifier($value, array $options = null)
    {
        $newEntity = new TrustCare_Model_Patient($options);
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