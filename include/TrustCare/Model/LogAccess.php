<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */

class TrustCare_Model_LogAccess extends TrustCare_Model_Abstract
{
    protected $_id;
    protected $_author;
    protected $_time;
    protected $_ip;
    protected $_action;
    
    public function __construct(array $options = null) {
        parent::__construct($options);
        $this->_logObjectChanges = false;
    }
    
    
    /**
     * @param  int $id 
     * @return TrustCare_Model_LogAccess
     */
    public function setId($id)
    {
        $this->_id = (int) $id;
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
     * @return TrustCare_Model_LogAccess
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
     * @return TrustCare_Model_LogAccess
     */
    public function setTime($value)
    {
        $this->_time = (string) $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getTime()
    {
        return $this->_time;
    }
    
    /**
     * @param  string $value 
     * @return TrustCare_Model_LogAccess
     */
    public function setIp($value)
    {
        $this->_ip = (string) $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getIp()
    {
        return $this->_ip;
    }
    
    /**
     * @param  string $value 
     * @return TrustCare_Model_LogAccess
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
     * Find an entry
     *
     * Resets entry state if matching id found.
     * 
     * @param  string $id 
     * @param array|null $options
     * @return TrustCare_Model_LogAccess
     */
    public static function find($id, array $options = null)
    {
        $newEntity = new self($options);
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