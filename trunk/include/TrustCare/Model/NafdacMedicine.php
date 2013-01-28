<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */

class TrustCare_Model_NafdacMedicine extends TrustCare_Model_Abstract
{
    protected $_id;
    protected $_id_nafdac;
    protected $_name;
    protected $_dosage;
    protected $_route;
    protected $_started;
    protected $_stopped;
    protected $_reason;
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_NafdacMedicine
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
     * @return TrustCare_Model_NafdacMedicine
     */
    public function setIdNafdac($value)
    {
        $this->_parameterChanged('id_nafdac', $value);
        $this->_id_nafdac = (int) $value;
        return $this;
    }

    /**
     * @return null|int
     */
    public function getIdNafdac()
    {
        return $this->_id_nafdac;
    }
    
    /**
     * @param  string $value
     * @return TrustCare_Model_NafdacMedicine
     */
    public function setName($value)
    {
        $this->_parameterChanged('name', $value);
        $this->_name = $value;
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
     * @return TrustCare_Model_NafdacMedicine
     */
    public function setDosage($value)
    {
        $this->_parameterChanged('dosage', $value);
        $this->_dosage = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getDosage()
    {
        return $this->_dosage;
    }
    
    /**
     * @param  string $value
     * @return TrustCare_Model_NafdacMedicine
     */
    public function setRoute($value)
    {
        $this->_parameterChanged('route', $value);
        $this->_route = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getRoute()
    {
        return $this->_route;
    }
    
    /**
     * @param  string $value
     * @return TrustCare_Model_NafdacMedicine
     */
    public function setStarted($value)
    {
        $this->_parameterChanged('started', $value);
        $this->_started = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getStarted()
    {
        return $this->_started;
    }
    
    /**
     * @param  string $value
     * @return TrustCare_Model_NafdacMedicine
     */
    public function setStopped($value)
    {
        $this->_parameterChanged('stopped', $value);
        $this->_stopped = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getStopped()
    {
        return $this->_stopped;
    }
    
    /**
     * @param  string $value
     * @return TrustCare_Model_NafdacMedicine
     */
    public function setReason($value)
    {
        $this->_parameterChanged('reason', $value);
        $this->_reason = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getReason()
    {
        return $this->_reason;
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
     * @return TrustCare_Model_NafdacMedicine
     */
    public static function find($id, array $options = null)
    {
        $newEntity = new TrustCare_Model_NafdacMedicine($options);
        $result = $newEntity->getMapper()->find($id, $newEntity);
        
        if(!$result) {
            unset($newEntity);
            $newEntity = null;
        }
        
        return $newEntity;
    }

    
    /**
     * Fetch all for specified $id_nafdac
     *
     * @param  int $value
     * @return TrustCare_Model_NafdacMedicine
     */
    public function fetchAllByIdNafdac($value)
    {
        return $this->getMapper()->fetchAllByIdNafdac($value);
    }
    
    public function delete()
    {
        parent::delete();
        $this->id = null;
    }
}