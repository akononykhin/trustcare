<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */

class TrustCare_Model_User extends TrustCare_Model_Abstract
{
    protected $_id;
    protected $_login;
    protected $_password;
    protected $_first_name;
    protected $_last_name;
    protected $_is_active;
    protected $_role;
    protected $_city;
    protected $_address;
    protected $_phone;
    protected $_zip;
    protected $_id_pharmacy;
    protected $_id_country;
    protected $_id_state;
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_User
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
     * @return TrustCare_Model_User
     */
    public function setLogin($value)
    {
        $this->_parameterChanged('login', $value);
        $this->_login = (string) $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getLogin()
    {
        return $this->_login;
    }
    
    /**
     * @param  string $value 
     * @return TrustCare_Model_User
     */
    public function setPassword($value)
    {
        $this->_parameterChanged('password', $value, false, true);
        $this->_password = (string) $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getPassword()
    {
        return $this->_password;
    }
    
    /**
     * @param  string $value 
     * @return TrustCare_Model_User
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
     * @return TrustCare_Model_User
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
     * @return TrustCare_Model_User
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
     * @return TrustCare_Model_User
     */
    public function setRole($value)
    {
        $this->_parameterChanged('role', $value);
        $this->_role = $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getRole()
    {
        return $this->_role;
    }
    
    
    /**
     * @param  string $value 
     * @return TrustCare_Model_User
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
     * @return TrustCare_Model_User
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
     * @return TrustCare_Model_User
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
     * @return TrustCare_Model_User
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
     * @param  int $value 
     * @return TrustCare_Model_User
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
     * @param  int $value 
     * @return TrustCare_Model_User
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
     * @return TrustCare_Model_User
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
    
    public function isExists()
    {
        return !is_null($this->getId());
    }
    
    /**
     * Find an entry by id
     *
     * @param  string $id 
     * @param array|null $options
     * @return TrustCare_Model_User
     */
    public static function find($id, array $options = null)
    {
        $newEntity = new TrustCare_Model_User($options);
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
     * @return TrustCare_Model_User
     */
    public static function findByLogin($value, array $options = null)
    {
        $newEntity = new TrustCare_Model_User($options);
        $result = $newEntity->getMapper()->findByLogin($value, $newEntity);
        
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