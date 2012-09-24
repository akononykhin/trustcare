<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 *
 */

class TrustCare_Model_LogObjects extends TrustCare_Model_Abstract
{
    protected $_id;
    protected $_timestamp;
    protected $_author;
    protected $_from_ip;
    protected $_stack;
    protected $_action;
    protected $_object_name;
    protected $_key_info;
    
    public function __construct(array $options = null) {
        parent::__construct($options);
        $this->_logObjectChanges = false;
    }
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_LogObjects
     */
    public function setId($value)
    {
        $this->_id = (int)$value;
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
     * @return TrustCare_Model_LogObjects
     */
    public function setTimestamp($value)
    {
        $this->_timestamp = $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getTimestamp()
    {
        return $this->_timestamp;
    }
    
    /**
     * @param  string $value 
     * @return TrustCare_Model_LogObjects
     */
    public function setAuthor($value)
    {
        $this->_author = (string) $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getAuthor()
    {
        return $this->_author;
    }
    
    /**
     * @param  string $value 
     * @return TrustCare_Model_LogObjects
     */
    public function setFromIp($value)
    {
        $this->_from_ip = (string) $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getFromIp()
    {
        return $this->_from_ip;
    }
    
    
    /**
     * @param  string $value 
     * @return TrustCare_Model_LogObjects
     */
    public function setStack($value)
    {
        $this->_stack = (string) $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getStack()
    {
        return $this->_stack;
    }
    
    /**
     * @param  string $value 
     * @return TrustCare_Model_LogObjects
     */
    public function setAction($value)
    {
        $this->_action = (string) $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getAction()
    {
        return $this->_action;
    }
    
    /**
     * @param  string $value 
     * @return TrustCare_Model_LogObjects
     */
    public function setObjectName($value)
    {
        $this->_object_name = (string) $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getObjectName()
    {
        return $this->_object_name;
    }
    
    /**
     * @param  string $value 
     * @return TrustCare_Model_LogObjects
     */
    public function setKeyInfo($value)
    {
        $this->_key_info = (string) $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getKeyInfo()
    {
        return $this->_key_info;
    }
    
    
    /**
     * Find an entry by id
     *
     * @param  string $id 
     * @param array|null $options
     * @return TrustCare_Model_LogObjects
     */
    public static function find($id, array $options = null)
    {
        $newEntity = new TrustCare_Model_LogObjects($options);
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