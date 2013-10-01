<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */
 

class TrustCare_Model_Mapper_ReportCommunityServices extends TrustCare_Model_Mapper_Abstract
{
    /**
     * @param  TrustCare_Model_ReportCommunityServices $model 
     * @return void
     */
    public function save(TrustCare_Model_ReportCommunityServices &$model)
    {
        $data = array();
        if(!$model->isExists() || $model->isParameterChanged('generation_date')) {
            $data['generation_date'] = $model->getGenerationDate();
        }
        if(!$model->isExists() || $model->isParameterChanged('period')) {
            $data['period'] = $model->getPeriod();
        }
        if(!$model->isExists() || $model->isParameterChanged('id_pharmacy')) {
            $data['id_pharmacy'] = $model->getIdPharmacy();
        }
        if(!$model->isExists() || $model->isParameterChanged('id_user')) {
            $data['id_user'] = $model->getIdUser();
        }
        if(!$model->isExists() || $model->isParameterChanged('filename')) {
            $data['filename'] = $model->getFilename();
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
    
    public function delete(TrustCare_Model_ReportCommunityServices $model)
    {
        $model->setObjectKeyInfo(array('id' => $model->getId()));
        if(!is_null($model->getId())) {
            $this->getDbTable()->delete(sprintf("id=%d", $model->getId()));
        }
    }

    /**
     * @param  int $id 
     * @param  TrustCare_Model_ReportCommunityServices $model 
     * @return void
     */
    public function find($id, TrustCare_Model_ReportCommunityServices $model)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return false;
        }
        $row = $result->current();
        $model->setSkipTrackChanges(true);
        $model->setId($row->id)
              ->setGenerationDate($row->generation_date)
              ->setPeriod($row->period)
              ->setIdUser($row->id_user)
              ->setIdPharmacy($row->id_pharmacy)
              ->setFilename($row->filename);
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
            $entry = new TrustCare_Model_ReportCommunityServices(array('mapperOptions' => array('adapter' => $this->getDbAdapter())));
            $this->find($row->id, $entry);
            
            $entries[] = $entry;
        }
        return $entries;
    }
}
