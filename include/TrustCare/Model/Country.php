<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */

class TrustCare_Model_Country extends TrustCare_Model_Abstract
{
    protected $_id;
    protected $_iso_3166;
    protected $_name;
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_Country
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
     * @return TrustCare_Model_Country
     */
    public function setIso3166($value)
    {
        $this->_parameterChanged('iso_3166', $value);
        $this->_iso_3166 = (string) $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getIso3166()
    {
        return $this->_iso_3166;
    }
    
    /**
     * @param  string $value 
     * @return TrustCare_Model_Country
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
    

    
    public function isExists() {
        return !is_null($this->getId());
    }

    
    /**
     * Find an entry by id
     *
     * @param  string $id 
     * @param array|null $options
     * @return TrustCare_Model_Country
     */
    public static function find($id, array $options = null)
    {
        $newEntity = new TrustCare_Model_Country($options);
        $result = $newEntity->getMapper()->find($id, $newEntity);
        
        if(!$result) {
            unset($newEntity);
            $newEntity = null;
        }
        
        return $newEntity;
    }

    /**
     * Find an entry by iso_3166
     *
     * @param  string $value - login 
     * @param array|null $options
     * @return TrustCare_Model_Country
     */
    public static function findByIso($value, array $options = null)
    {
        $newEntity = new TrustCare_Model_Country($options);
        $result = $newEntity->getMapper()->findByIso($value, $newEntity);
        
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