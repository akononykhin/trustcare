<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */

class TrustCare_Model_ReportCommunity extends TrustCare_Model_Abstract
{
    protected $_id;
    protected $_generation_date;
    protected $_period;
    protected $_id_pharmacy;
    protected $_content;
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_ReportCommunity
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
     * @return TrustCare_Model_ReportCommunity
     */
    public function setGenerationDate($value)
    {
        $this->_parameterChanged('generation_date', $value);
        $this->_generation_date = (string) $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getGenerationDate()
    {
        return $this->_generation_date;
    }
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_ReportCommunity
     */
    public function setPeriod($value)
    {
        $this->_parameterChanged('period', $value);
        $this->_period = (int) $value;
        return $this;
    }

    /**
     * @return null|int
     */
    public function getPeriod()
    {
        return $this->_period;
    }
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_ReportCommunity
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
     * @param  string $value 
     * @return TrustCare_Model_ReportCommunity
     */
    public function setContent($value)
    {
        $this->_parameterChanged('content', $value);
        $this->_content = (string) $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getContent()
    {
        return $this->_content;
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
     * @return TrustCare_Model_ReportCommunity
     */
    public static function find($id, array $options = null)
    {
        $newEntity = new TrustCare_Model_ReportCommunity($options);
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