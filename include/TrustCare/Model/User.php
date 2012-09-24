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
    protected $_is_active;
    protected $_role;
    
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