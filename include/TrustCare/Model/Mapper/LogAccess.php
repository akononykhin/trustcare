<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */
 
class TrustCare_Model_Mapper_LogAccess extends TrustCare_Model_Mapper_Abstract
{
    /**
     * @param  TrustCare_Model_LogAccess $model 
     * @return void
     */
    public function save(TrustCare_Model_LogAccess &$model)
    {
        $data = array(
            'author'   => $model->getAuthor(),
            'time' => $model->getTime(),
            'ip' => $model->getIp(),
            'action' => $model->getAction(),
        );

        if (null === ($id = $model->getId())) {
            unset($data['id']);
            $primaryKey = $this->getDbTable()->insert($data);
            $model->id = $primaryKey;
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
    
    public function delete(TrustCare_Model_LogAccess $model)
    {
        if(!is_null($model->getId())) {
            $this->getDbTable()->delete(sprintf("id=%d", $model->getId()));
        }
    }

    /**
     * @param  int $id 
     * @param  TrustCare_Model_LogAccess $model 
     * @return void
     */
    public function find($id, TrustCare_Model_LogAccess $model)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return false;
        }
        $row = $result->current();
        $model->setId($row->id)
              ->setAuthor($row->author)
              ->setTime($row->time)
              ->setIp($row->ip)
              ->setAction($row->action);
        return true;
    }

    /**
     * @return array
     */
    public function fetchAll()
    {
        $entries   = array();
        
        $select = $this->getDbTable()->select();
        $select->from($this->getDbTable(), array('id'));
        $select->order("time desc");
        
        $resultSet = $this->getDbTable()->fetchAll($select);
        foreach ($resultSet as $row) {
            $entry = new TrustCare_Model_LogAccess(array('mapperOptions' => array('adapter' => $this->getDbAdapter())));
            $this->find($row['id'], $entry);
            
            $entries[] = $entry;
        }
        return $entries;
    	    }
}
