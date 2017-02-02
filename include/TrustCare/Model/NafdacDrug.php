<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */

class TrustCare_Model_NafdacDrug extends TrustCare_Model_Abstract
{
    protected $_id;
    protected $_id_nafdac;
    protected $_name;
    protected $_generic_name;
    protected $_dosage;
    protected $_batch;
    protected $_started;
    protected $_stopped;
    protected $_reason;
    protected $_nafdac_number;
    protected $_expiry_date;
    protected $_manufactor;
    protected $_route_of_administration;
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_NafdacDrug
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
     * @return TrustCare_Model_NafdacDrug
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
     * @return TrustCare_Model_NafdacDrug
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
     * @return TrustCare_Model_NafdacDrug
     */
    public function setGenericName($value)
    {
        $this->_parameterChanged('generic_name', $value);
        $this->_generic_name = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getGenericName()
    {
        return $this->_generic_name;
    }
    
    /**
     * @param  string $value
     * @return TrustCare_Model_NafdacDrug
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
     * @return TrustCare_Model_NafdacDrug
     */
    public function setBatch($value)
    {
        $this->_parameterChanged('batch', $value);
        $this->_batch = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getBatch()
    {
        return $this->_batch;
    }
    
    /**
     * @param  string $value
     * @return TrustCare_Model_NafdacDrug
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
     * @return TrustCare_Model_NafdacDrug
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
     * @return TrustCare_Model_NafdacDrug
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
    

    /**
     * @param  string $value
     * @return TrustCare_Model_NafdacDrug
     */
    public function setNafdacNumber($value)
    {
        $this->_parameterChanged('nafdac_number', $value);
        $this->_nafdac_number = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getNafdacNumber()
    {
        return $this->_nafdac_number;
    }
    

    /**
     * @param  string $value
     * @return TrustCare_Model_NafdacDrug
     */
    public function setExpiryDate($value)
    {
        $this->_parameterChanged('expiry_date', $value);
        $this->_expiry_date = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getExpiryDate()
    {
        return $this->_expiry_date;
    }
    

    /**
     * @param  string $value
     * @return TrustCare_Model_NafdacDrug
     */
    public function setManufactor($value)
    {
        $this->_parameterChanged('manufactor', $value);
        $this->_manufactor = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getManufactor()
    {
        return $this->_manufactor;
    }


    /**
     * @param  string $value
     * @return TrustCare_Model_NafdacDrug
     */
    public function setRouteOfAdministration($value)
    {
        $this->_parameterChanged('route_of_administration', $value);
        $this->_route_of_administration = $value;
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function getRouteOfAdministration()
    {
        return $this->_route_of_administration;
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
     * @return TrustCare_Model_NafdacDrug
     */
    public static function find($id, array $options = null)
    {
        $newEntity = new TrustCare_Model_NafdacDrug($options);
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
     * @return TrustCare_Model_NafdacDrug
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