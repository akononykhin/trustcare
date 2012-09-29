<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */
 

class TrustCare_Model_Mapper_User extends TrustCare_Model_Mapper_Abstract
{
    /**
     * @param  TrustCare_Model_User $model 
     * @return void
     */
    public function save(TrustCare_Model_User &$model)
    {
        $data = array(
            'login'   => $model->getLogin(),
            'password' => $model->getPassword(),
            'is_active' => $model->getIsActive() ? 1 : 0,
            'role' => $model->getRole()
        );

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
    
    public function delete(TrustCare_Model_User $model)
    {
        $model->setObjectKeyInfo(array('id' => $model->getId()));
    	if(!is_null($model->getId())) {
            $this->getDbTable()->delete(sprintf("id=%d", $model->getId()));
        }
    }

    /**
     * @param  int $id 
     * @param  TrustCare_Model_User $model 
     * @return void
     */
    public function find($id, TrustCare_Model_User $model)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return false;
        }
        $row = $result->current();
        $model->setSkipTrackChanges(true);
        $model->setId($row->id)
              ->setLogin($row->login)
              ->setPassword($row->password)
              ->setIsActive($row->is_active)
              ->setRole($row->role);
        $model->setSkipTrackChanges(false);
              
        return true;
    }

    /**
     * @param  string $value 
     * @param  TrustCare_Model_User $model 
     * @return void
     */
    public function findByLogin($value, TrustCare_Model_User $model)
    {
        $select = $this->getDbTable()->select();
        $select->from($this->getDbTable(), array('id'))
               ->where("login=?", $value);
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
    public function fetchAll()
    {
        $entries   = array();
        
        $select = $this->getDbTable()->select();
        $select->from($this->getDbTable(), array('id'));
        $resultSet = $this->getDbTable()->fetchAll($select);
        foreach ($resultSet as $row) {
            $entry = new TrustCare_Model_User(array('mapperOptions' => array('adapter' => $this->getDbAdapter())));
            $this->find($row['id'], $entry);
            
            $entries[] = $entry;
        }
        return $entries;
    }
}