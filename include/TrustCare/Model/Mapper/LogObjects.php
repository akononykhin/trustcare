<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 *
 */
 

class TrustCare_Model_Mapper_LogObjects extends TrustCare_Model_Mapper_Abstract
{

    /**
     * @param  TrustCare_Model_LogObjects $model 
     * @return void
     */
    public function save(TrustCare_Model_LogObjects &$model)
    {
        $data = array(
            'timestamp'   => $model->getTimestamp(),
            'author' => $model->getAuthor(),
            'from_ip' => $model->getFromIp(),
            'stack' => $model->getStack(),
            'action' => $model->getAction(),
            'object_name' => $model->getObjectName(),
            'key_info' => $model->getKeyInfo()
        );
        
        if (null === ($id = $model->getId())) {
            unset($data['id']);
            $primaryKey = $this->getDbTable()->insert($data);
            $model->id = $primaryKey;
        } else {
            throw new Exception(sprintf("Forbidden to update %s object.", get_class($model)));
        }
    }
    
    public function delete(TrustCare_Model_LogObjects $model)
    {
        throw new Exception(sprintf("Forbidden to delete %s object.", get_class($model)));
    }

    
    /**
     * @param  string $id 
     * @param  TrustCare_Model_LogObjects $model 
     * @return void
     */
    public function find($id, TrustCare_Model_LogObjects $model)
    {
        $select = $this->getDbTable()->select();
        $select->from($this->getDbTable(), array('id',
                                                 'timestamp' => new Zend_Db_Expr("date_format(timestamp, '%Y-%m-%d %H:%i:%s')"),
                                                 'author',
                                                 'from_ip',
                                                 'stack',
                                                 'action',
                                                 'object_name',
                                                 'key_info'))
               ->where("id=?", $id, Zend_Db::INT_TYPE);
        
        $result = $this->getDbTable()->fetchAll($select);
        if (0 == count($result)) {
            return false;
        }
        $row = $result->current();
        $model->setId($row->id)
              ->setTimestamp($row->timestamp)
              ->setAuthor($row->author)
              ->setFromIp($row->from_ip)
              ->setStack($row->stack)
              ->setAction($row->action)
              ->setObjectName($row->object_name)
              ->setKeyInfo($row->key_info);
              
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
        
        
        $query = sprintf("select id from %s where %s order by date_of_change desc;", $this->getDbTable()->info(Zend_Db_Table_Abstract::NAME), join(' and ', $where));
        $this->getDbAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);
        $resultSet = $this->getDbAdapter()->fetchAll($query);
        foreach ($resultSet as $row) {
            $entry = new TrustCare_Model_LogObjects(array('mapperOptions' => array('adapter' => $this->getDbAdapter())));
            $this->find($row->id, $entry);
            
            $entries[] = $entry;
        }
        return $entries;
    }
}
