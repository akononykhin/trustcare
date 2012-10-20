<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */
 

class TrustCare_Model_Mapper_Pharmacy extends TrustCare_Model_Mapper_Abstract
{
    /**
     * @param  TrustCare_Model_Pharmacy $model 
     * @return void
     */
    public function save(TrustCare_Model_Pharmacy &$model)
    {
        $data = array();
        if(!$model->isExists() || $model->isParameterChanged('name')) {
            $data['name'] = $model->getName();
        }
        if(!$model->isExists() || $model->isParameterChanged('is_active')) {
            $data['is_active'] = $model->getIsActive() ? 1 : 0;
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
    
    public function delete(TrustCare_Model_Pharmacy $model)
    {
        $model->setObjectKeyInfo(array('id' => $model->getId()));
        if(!is_null($model->getId())) {
            $this->getDbTable()->delete(sprintf("id=%d", $model->getId()));
        }
    }

    /**
     * @param  int $id 
     * @param  TrustCare_Model_Pharmacy $model 
     * @return void
     */
    public function find($id, TrustCare_Model_Pharmacy $model)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return false;
        }
        $row = $result->current();
        $model->setSkipTrackChanges(true);
        $model->setId($row->id)
              ->setName($row->name)
              ->setAddress($row->address)
              ->setIdLga($row->id_lga)
              ->setIdCountry($row->id_country)
              ->setIdState($row->id_state)
              ->setIdFacility($row->id_facility)
              ->setIsActive($row->is_active);
        $model->setSkipTrackChanges(false);
              
        return true;
    }

    /**
     * @param  string $value 
     * @param  TrustCare_Model_Pharmacy $model 
     * @return void
     */
    public function findByName($value, TrustCare_Model_Pharmacy $model)
    {
        $select = $this->getDbTable()->select();
        $select->from($this->getDbTable(), array('id'))
               ->where("name=?", $value);
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
            $entry = new TrustCare_Model_Pharmacy(array('mapperOptions' => array('adapter' => $this->getDbAdapter())));
            $this->find($row->id, $entry);
            
            $entries[] = $entry;
        }
        return $entries;
    }
}
