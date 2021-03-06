<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */

class TrustCare_Model_FacilityType extends TrustCare_Model_Abstract
{
    protected $_id;
    protected $_name;
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_FacilityType
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
     * @return TrustCare_Model_FacilityType
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
     * @return TrustCare_Model_FacilityType
     */
    public static function find($id, array $options = null)
    {
        $newEntity = new TrustCare_Model_FacilityType($options);
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
     * @param  string $value
     * @param array|null $options
     * @return TrustCare_Model_FacilityType
     */
    public static function findByName($value, array $options = null)
    {
        $newEntity = new TrustCare_Model_FacilityType($options);
        $result = $newEntity->getMapper()->findByName($value, $newEntity);
    
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