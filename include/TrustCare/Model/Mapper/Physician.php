<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */
 

class TrustCare_Model_Mapper_Physician extends TrustCare_Model_Mapper_Abstract
{
    /**
     * @param  TrustCare_Model_Physician $model 
     * @return void
     */
    public function save(TrustCare_Model_Physician &$model)
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
        if(!$model->isExists() || $model->isParameterChanged('address')) {
            $data['address'] = $model->getAddress();
        }
        if(!$model->isExists() || $model->isParameterChanged('id_country')) {
            $data['id_country'] = $model->getIdCountry();
        }
        if(!$model->isExists() || $model->isParameterChanged('id_state')) {
            $data['id_state'] = $model->getIdState();
        }
        if(!$model->isExists() || $model->isParameterChanged('id_lga')) {
            $data['id_lga'] = $model->getIdLga();
        }
        if(!$model->isExists() || $model->isParameterChanged('id_facility')) {
            $data['id_facility'] = $model->getIdFacility();
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
    
    public function delete(TrustCare_Model_Physician $model)
    {
        $model->setObjectKeyInfo(array('id' => $model->getId()));
        if(!is_null($model->getId())) {
            $this->getDbTable()->delete(sprintf("id=%d", $model->getId()));
        }
    }

    /**
     * @param  int $id 
     * @param  TrustCare_Model_Physician $model 
     * @return void
     */
    public function find($id, TrustCare_Model_Physician $model)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return false;
        }
        $row = $result->current();
        $model->setSkipTrackChanges(true);
        $model->setId($row->id)
              ->setIdentifier($row->identifier)
              ->setFirstName($row->first_name)
              ->setLastName($row->last_name)
              ->setAddress($row->address)
              ->setIdLga($row->id_lga)
              ->setIdCountry($row->id_country)
              ->setIdState($row->id_state)
              ->setIdFacility($row->id_facility);
        $model->setSkipTrackChanges(false);
              
        return true;
    }

    /**
     * @param  string $value 
     * @param  TrustCare_Model_Physician $model 
     * @return void
     */
    public function findByIdentifier($value, TrustCare_Model_Physician $model)
    {
        $select = $this->getDbTable()->select();
        $select->from($this->getDbTable(), array('id'))
               ->where("identifier=?", $value);
        $result = $this->getDbTable()->fetchAll($select);
        if (0 == count($result)) {
            return false;
        }
        $row = $result->current();
        $this->find($row['id'], $model);
        
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
            $entry = new TrustCare_Model_Physician(array('mapperOptions' => array('adapter' => $this->getDbAdapter())));
            $this->find($row->id, $entry);
            
            $entries[] = $entry;
        }
        return $entries;
    }
}
