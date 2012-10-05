<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */

class TrustCare_Model_FrmCareSuspectedAdrCardiovascular extends TrustCare_Model_Abstract
{
    protected $_id;
    protected $_id_frm_care;
    protected $_id_pharmacy_dictionary;
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_FrmCareSuspectedAdrCardiovascular
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
     * @return TrustCare_Model_FrmCareSuspectedAdrCardiovascular
     */
    public function setIdFrmCare($value)
    {
        $this->_parameterChanged('id_frm_care', $value);
        $this->_id_frm_care = (int) $value;
        return $this;
    }

    /**
     * @return null|int
     */
    public function getIdFrmCare()
    {
        return $this->_id_frm_care;
    }
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_FrmCareSuspectedAdrCardiovascular
     */
    public function setIdPharmacyDictionary($value)
    {
        $this->_parameterChanged('id_pharmacy_dictionary', $value);
        $this->_id_pharmacy_dictionary = (int) $value;
        return $this;
    }

    /**
     * @return null|int
     */
    public function getIdPharmacyDictionary()
    {
        return $this->_id_pharmacy_dictionary;
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
     * @return TrustCare_Model_FrmCareSuspectedAdrCardiovascular
     */
    public static function find($id, array $options = null)
    {
        $newEntity = new TrustCare_Model_FrmCareSuspectedAdrCardiovascular($options);
        $result = $newEntity->getMapper()->find($id, $newEntity);
        
        if(!$result) {
            unset($newEntity);
            $newEntity = null;
        }
        
        return $newEntity;
    }

    
    /**
     * Fetch all for specified $id_frm_care
     *
     * @param  int $id_frm_care
     * @return TrustCare_Model_FrmCareSuspectedAdrCardiovascular
     */
    public function fetchAllForFrmCare($id_frm_care)
    {
        return $this->getMapper()->fetchAllForFrmCare($id_frm_care);
    }
    
    public function delete()
    {
        parent::delete();
        $this->id = null;
    }
}