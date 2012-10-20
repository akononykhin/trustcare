<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */
 

class TrustCare_Model_Mapper_Patient extends TrustCare_Model_Mapper_Abstract
{
    /**
     * @param  TrustCare_Model_Patient $model 
     * @return void
     */
    public function save(TrustCare_Model_Patient &$model)
    {
        $data = array();
        if(!$model->isExists() || $model->isParameterChanged('identifier')) {
            $data['identifier'] = $model->getIdentifier();
        }
        if(!$model->isExists() || $model->isParameterChanged('first_name')) {
            $data['first_name'] = $model->getFirstName();
        }
        if(!$model->isExists() || $model->isParameterChanged('last_name')) {
            $data['last_name'] = $model->getLastName();
        }
        if(!$model->isExists() || $model->isParameterChanged('is_active')) {
            $data['is_active'] = $model->getIsActive() ? 1 : 0;
        }
        if(!$model->isExists() || $model->isParameterChanged('id_country')) {
            $data['id_country'] = $model->getIdCountry();
        }
        if(!$model->isExists() || $model->isParameterChanged('id_state')) {
            $data['id_state'] = $model->getIdState();
        }
        if(!$model->isExists() || $model->isParameterChanged('city')) {
            $data['city'] = $model->getCity();
        }
        if(!$model->isExists() || $model->isParameterChanged('zip')) {
            $data['zip'] = $model->getZip();
        }
        if(!$model->isExists() || $model->isParameterChanged('address')) {
            $data['address'] = $model->getAddress();
        }
        if(!$model->isExists() || $model->isParameterChanged('phone')) {
            $data['phone'] = $model->getPhone();
        }
        if(!$model->isExists() || $model->isParameterChanged('id_physician')) {
            $data['id_physician'] = $model->getIdPhysician();
        }
        if(!$model->isExists() || $model->isParameterChanged('birthdate')) {
            $data['birthdate'] = $model->getBirthdate();
        }
        if(!$model->isExists() || $model->isParameterChanged('is_male')) {
            $data['is_male'] = $model->getIsMale() ? 1 : 0;
        }

        if (null === ($id = $model->getId())) {
            unset($data['id']);
            $primaryKey = $this->getDbTable()->insert($data);
            $model->id = $primaryKey;
            $this->setLastOperationInsert(true);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
            $this->setLastOperationInsert(false);
        }
        $model->setObjectKeyInfo(array('id' => $model->getId()));
    }
    
    public function delete(TrustCare_Model_Patient $model)
    {
        $model->setObjectKeyInfo(array('id' => $model->getId()));
        if(!is_null($model->getId())) {
            $this->getDbTable()->delete(sprintf("id=%d", $model->getId()));
        }
    }

    
    private function _fillModelForFind(TrustCare_Model_Patient &$model, $row)
    {
        $model->setSkipTrackChanges(true);
        $model->setId($row->id)
              ->setIdentifier($row->identifier)
              ->setFirstName($row->first_name)
              ->setLastName($row->last_name)
              ->setIdCountry($row->id_country)
              ->setIdState($row->id_state)
              ->setCity($row->city)
              ->setAddress($row->address)
              ->setZip($row->zip)
              ->setPhone($row->phone)
              ->setBirthdate($row->birthdate)
              ->setIdPhysician($row->id_physician)
              ->setIsMale($row->is_male)
              ->setIsActive($row->is_active);
        $model->setSkipTrackChanges(false);
    }

    /**
     * @param  int $id 
     * @param  TrustCare_Model_Patient $model 
     * @return void
     */
    public function find($id, TrustCare_Model_Patient $model)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return false;
        }
        $row = $result->current();
        $this->_fillModelForFind($model, $row);
              
        return true;
    }
    
    /**
     * @param  string $value 
     * @param  TrustCare_Model_Patient $model 
     * @return void
     */
    public function findByIdentifier($value, TrustCare_Model_Patient $model)
    {
        $where = array();
        $where[] = sprintf("identifier=%s", $this->getDbAdapter()->quote($value));
        $query = sprintf("select * from %s where %s;", $this->getDbTable()->info(Zend_Db_Table_Abstract::NAME), join(' and ', $where));
        
        $this->getDbAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);
        $result = $this->getDbAdapter()->fetchAll($query);
        if (0 == count($result)) {
            return false;
        }
        $row = $result[0];
        
        $this->_fillModelForFind($model, $row);
        return true;
    }
    
    /**
     * @return array
     */
    public function fetchAll(array $clauses = array())
    {
        $entries   = array();
        
        $where = array();
        $where[] = '1=1';
        foreach($clauses as $clause) {
            $where[] = $clause;
        }
        
        
        $query = sprintf("select id from %s where %s;", $this->getDbTable()->info(Zend_Db_Table_Abstract::NAME), join(' and ', $where));
        $this->getDbAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);
        $resultSet = $this->getDbAdapter()->fetchAll($query);
        foreach ($resultSet as $row) {
            $entry = new TrustCare_Model_Patient(array('mapperOptions' => array('adapter' => $this->getDbAdapter())));
            $this->find($row->id, $entry);
            
            $entries[] = $entry;
        }
        return $entries;
    }
}
