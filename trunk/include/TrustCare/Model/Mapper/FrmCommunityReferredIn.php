<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */
 

class TrustCare_Model_Mapper_FrmCommunityReferredIn extends TrustCare_Model_Mapper_Abstract
{
    /**
     * @param  TrustCare_Model_FrmCommunityReferredIn $model 
     * @return void
     */
    public function save(TrustCare_Model_FrmCommunityReferredIn &$model)
    {
        $data = array();
        if(!$model->isExists() || $model->isParameterChanged('id_frm_community')) {
            $data['id_frm_community'] = $model->getIdFrmCommunity();
        }
        if(!$model->isExists() || $model->isParameterChanged('id_pharmacy_dictionary')) {
            $data['id_pharmacy_dictionary'] = $model->getIdPharmacyDictionary();
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
        $model->setObjectKeyInfo(
            array(
                'id' => $model->getId(),
                'id_frm_community' => $model->getIdFrmCommunity()
            )
        );
    }
    
    public function delete(TrustCare_Model_FrmCommunityReferredIn $model)
    {
        $model->setObjectKeyInfo(
            array(
                'id' => $model->getId(),
                'id_frm_community' => $model->getIdFrmCommunity()
            )
        );
        if(!is_null($model->getId())) {
            $this->getDbTable()->delete(sprintf("id=%d", $model->getId()));
        }
    }

    
    
    private function _fillModelForFind(TrustCare_Model_FrmCommunityReferredIn $model, $row)
    {
        $model->setSkipTrackChanges(true);
        $model->setId($row->id)
              ->setIdFrmCommunity($row->id_frm_community)
              ->setIdPharmacyDictionary($row->id_pharmacy_dictionary);
        $model->setSkipTrackChanges(false);
    }
    
    /**
     * @param  int $id 
     * @param  TrustCare_Model_FrmCommunityReferredIn $model 
     * @return void
     */
    public function find($id, TrustCare_Model_FrmCommunityReferredIn $model)
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
     * @return array
     */
    public function fetchAllForFrmCommunity($value)
    {
        $entries   = array();
        $query = sprintf("select * from %s where id_frm_community=?;", $this->getDbTable()->info(Zend_Db_Table_Abstract::NAME));
        $this->getDbAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);
        $resultSet = $this->getDbAdapter()->fetchAll($query, (int)$value, Zend_Db::INT_TYPE);
        foreach ($resultSet as $row) {
            $entry = new TrustCare_Model_FrmCommunityReferredIn(array('mapperOptions' => array('adapter' => $this->getDbAdapter())));
            $this->_fillModelForFind($entry, $row);
            
            $entries[] = $entry;
        }
        return $entries;
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
        
        $query = sprintf("select * from %s where %s order by id desc;", $this->getDbTable()->info(Zend_Db_Table_Abstract::NAME), join(' and ', $where));
        $this->getDbAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);
        $resultSet = $this->getDbAdapter()->fetchAll($query);
        foreach ($resultSet as $row) {
            $entry = new TrustCare_Model_FrmCommunityReferredIn(array('mapperOptions' => array('adapter' => $this->getDbAdapter())));
            $this->_fillModelForFind($entry, $row);
                        
            $entries[] = $entry;
        }
        return $entries;
    }
}
