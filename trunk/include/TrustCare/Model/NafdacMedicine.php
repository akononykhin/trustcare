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