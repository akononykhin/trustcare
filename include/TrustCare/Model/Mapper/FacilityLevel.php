<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */
 

class TrustCare_Model_Mapper_FacilityLevel extends TrustCare_Model_Mapper_Abstract
{
    /**
     * @param  TrustCare_Model_FacilityLevel $model 
     * @return void
     */
    public function save(TrustCare_Model_FacilityLevel &$model)
    {
        $data = array();
        if(!$model->isExists() || $model->isParameterChanged('name')) {
            $data['name'] = $model->getName();
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
    
    public function delete(TrustCare_Model_FacilityLevel $model)
    {
        $model->setObjectKeyInfo(array('id' => $model->getId()));
        if(!is_null($model->getId())) {
            $this->getDbTable()->delete(sprintf("id=%d", $model->getId()));
        }
    }

    
    private function _fillModelForFind(TrustCare_Model_FacilityLevel $model, $row)
    {
        $model->setSkipTrackChanges(true);
        $model->setId($row->id)
              ->setName($row->name);
        $model->setSkipTrackChanges(false);
    }
    
    /**
     * @param  int $id 
     * @param  TrustCare_Model_FacilityLevel $model 
     * @return void
     */
    public function find($id, TrustCare_Model_FacilityLevel $model)
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
     * @param  TrustCare_Model_FacilityLevel $model
     * @return void
     */
    public function findByName($value, TrustCare_Model_FacilityLevel $model)
    {
        $select = $this->getDbTable()->select();
        $select->from($this->getDbTable(), array('*'))
        ->where("name=?", $value);
        $result = $this->getDbTable()->fetchAll($select);
        if (0 == count($result)) {
            return false;
        }
        $row = $result->current();
        $this->_fillModelForFind($model, $row);
    
        return true;
    }
    
    /**
     * @return array
     */
    public function fetchAll(array $clauses = array(), $orderClause = '')
    {
        $entries   = array();
        
        $where = array();
        $where[] = '1=1';
        foreach($clauses as $clause) {
            $where[] = $clause;
        }
        
        
        $query = sprintf("select id from %s where %s", $this->getDbTable()->info(Zend_Db_Table_Abstract::NAME), join(' and ', $where));
        if(!empty($orderClause)) {
            $query .= ' order by ' . $orderClause;
        }
        $query .= ';';
        
        $this->getDbAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);
        $resultSet = $this->getDbAdapter()->fetchAll($query);
        foreach ($resultSet as $row) {
            $entry = new TrustCare_Model_FacilityLevel(array('mapperOptions' => array('adapter' => $this->getDbAdapter())));
            $this->find($row->id, $entry);
            
            $entries[] = $entry;
        }
        return $entries;
    }
}
