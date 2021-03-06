<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */
 

class TrustCare_Model_Mapper_Facility extends TrustCare_Model_Mapper_Abstract
{
    /**
     * @param  TrustCare_Model_Facility $model 
     * @return void
     */
    public function save(TrustCare_Model_Facility &$model)
    {
        $data = array();
        if(!$model->isExists() || $model->isParameterChanged('name')) {
            $data['name'] = $model->getName();
        }
        if(!$model->isExists() || $model->isParameterChanged('sn')) {
            $data['sn'] = $model->getSn();
        }
        if(!$model->isExists() || $model->isParameterChanged('id_lga')) {
            $data['id_lga'] = $model->getIdLga();
        }
        if(!$model->isExists() || $model->isParameterChanged('id_facility_type')) {
            $data['id_facility_type'] = $model->getIdFacilityType();
        }
        if(!$model->isExists() || $model->isParameterChanged('id_facility_level')) {
            $data['id_facility_level'] = $model->getIdFacilityLevel();
        }
        
        if(!count($data)) {
            return;
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
    
    public function delete(TrustCare_Model_Facility $model)
    {
        $model->setObjectKeyInfo(array('id' => $model->getId()));
        if(!is_null($model->getId())) {
            $this->getDbTable()->delete(sprintf("id=%d", $model->getId()));
        }
    }

    /**
     * @param  int $id 
     * @param  TrustCare_Model_Facility $model 
     * @return void
     */
    public function find($id, TrustCare_Model_Facility $model)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return false;
        }
        $row = $result->current();
        $model->setSkipTrackChanges(true);
        $model->setId($row->id)
              ->setSn($row->sn)
              ->setIdFacilityType($row->id_facility_type)
              ->setIdFacilityLevel($row->id_facility_level)
              ->setIdLga($row->id_lga)
              ->setName($row->name);
        $model->setSkipTrackChanges(false);
              
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
            $entry = new TrustCare_Model_Facility(array('mapperOptions' => array('adapter' => $this->getDbAdapter())));
            $this->find($row->id, $entry);
            
            $entries[] = $entry;
        }
        return $entries;
    }
}
