<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */

class TrustCare_Model_PharmacyDictionaryType extends TrustCare_Model_Abstract
{
    protected $_id;
    protected $_ordernum;
    protected $_name;
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_PharmacyDictionaryType
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
     * @return TrustCare_Model_PharmacyDictionaryType
     */
    public function setOrdernum($value)
    {
        $this->_parameterChanged('ordernum', $value);
        $this->_ordernum = (int) $value;
        return $this;
    }

    /**
     * @return null|int
     */
    public function getOrdernum()
    {
        return $this->_ordernum;
    }
    
    /**
     * @param  string $value 
     * @return TrustCare_Model_PharmacyDictionaryType
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

    
    public function isExists()
    {
        return !is_null($this->getId());
    }
    
    
    /**
     * Find an entry by id
     *
     * @param  string $id 
     * @param array|null $options
     * @return TrustCare_Model_PharmacyDictionaryType
     */
    public static function find($id, array $options = null)
    {
        $newEntity = new TrustCare_Model_PharmacyDictionaryType($options);
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