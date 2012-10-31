<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */

class TrustCare_Model_ReportCare extends TrustCare_Model_Abstract
{
    protected $_id;
    protected $_generation_date;
    protected $_period;
    protected $_id_user;
    protected $_id_pharmacy;
    protected $_number_of_clients_with_prescription_male_younger_15;
    protected $_number_of_clients_with_prescription_female_younger_15;
    protected $_number_of_clients_with_prescription_male_from_15;
    protected $_number_of_clients_with_prescription_female_from_15;
    protected $_number_of_dispensed_drugs;
    protected $_filename;
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_ReportCare
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
     * @return TrustCare_Model_ReportCare
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
     * @return TrustCare_Model_ReportCare
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
     * @return TrustCare_Model_ReportCare
     */
    public function setIdUser($value)
    {
        $this->_parameterChanged('id_user', $value);
        $this->_id_user = (int) $value;
        return $this;
    }

    /**
     * @return null|int
     */
    public function getIdUser()
    {
        return $this->_id_user;
    }
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_ReportCare
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
     * @param  int $value 
     * @return TrustCare_Model_ReportCare
     */
    public function setNumberOfClientsWithPrescriptionMaleYounger15($value)
    {
        $this->_parameterChanged('number_of_clients_with_prescription_male_younger_15', $value);
        $this->_number_of_clients_with_prescription_male_younger_15 = (int) $value;
        return $this;
    }

    /**
     * @return null|int
     */
    public function getNumberOfClientsWithPrescriptionMaleYounger15()
    {
        return $this->_number_of_clients_with_prescription_male_younger_15;
    }
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_ReportCare
     */
    public function setNumberOfClientsWithPrescriptionFemaleYounger15($value)
    {
        $this->_parameterChanged('number_of_clients_with_prescription_female_younger_15', $value);
        $this->_number_of_clients_with_prescription_female_younger_15 = (int) $value;
        return $this;
    }

    /**
     * @return null|int
     */
    public function getNumberOfClientsWithPrescriptionFemaleYounger15()
    {
        return $this->_number_of_clients_with_prescription_female_younger_15;
    }
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_ReportCare
     */
    public function setNumberOfClientsWithPrescriptionMaleFrom15($value)
    {
        $this->_parameterChanged('number_of_clients_with_prescription_male_from_15', $value);
        $this->_number_of_clients_with_prescription_male_from_15 = (int)$value;
        return $this;
    }

    /**
     * @return null|int
     */
    public function getNumberOfClientsWithPrescriptionMaleFrom15()
    {
        return $this->_number_of_clients_with_prescription_male_from_15;
    }
    
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_ReportCare
     */
    public function setNumberOfClientsWithPrescriptionFemaleFrom15($value)
    {
        $this->_parameterChanged('number_of_clients_with_prescription_female_from_15', $value);
        $this->_number_of_clients_with_prescription_female_from_15 = (int) $value;
        return $this;
    }

    /**
     * @return null|int
     */
    public function getNumberOfClientsWithPrescriptionFemaleFrom15()
    {
        return $this->_number_of_clients_with_prescription_female_from_15;
    }
    
    /**
     * @param  int $value 
     * @return TrustCare_Model_ReportCare
     */
    public function setNumberOfDispensedDrugs($value)
    {
        $this->_parameterChanged('number_of_dispensed_drugs', $value);
        $this->_number_of_dispensed_drugs = (int) $value;
        return $this;
    }

    /**
     * @return null|int
     */
    public function getNumberOfDispensedDrugs()
    {
        return $this->_number_of_dispensed_drugs;
    }
    
    /**
     * @param  string $value 
     * @return TrustCare_Model_ReportCare
     */
    public function setFilename($value)
    {
        $this->_parameterChanged('filename', $value);
        $this->_filename = (string) $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getFilename()
    {
        return $this->_filename;
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
     * @return TrustCare_Model_ReportCare
     */
    public static function find($id, array $options = null)
    {
        $newEntity = new TrustCare_Model_ReportCare($options);
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