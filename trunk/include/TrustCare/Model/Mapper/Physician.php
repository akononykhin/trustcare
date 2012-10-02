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
        $data = array(
            'identifier'   => $model->getIdentifier(),
            'first_name' => $model->getFirstName(),
            'last_name' => $model->getLastName(),
            'address' => $model->getAddress(),
            'id_lga' => $model->getIdLga(),
            'id_country' => $model->getIdCountry(),
            'id_state' => $model->getIdState(),
            'id_facility' => $model->getIdFacility(),
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
    public function fetchAll()
    {
        $entries   = array();
        
        $select = $this->getDbTable()->select();
        $select->from($this->getDbTable(), array('id'));
        $resultSet = $this->getDbTable()->fetchAll($select);
        foreach ($resultSet as $row) {
            $entry = new TrustCare_Model_Physician(array('mapperOptions' => array('adapter' => $this->getDbAdapter())));
            $this->find($row['id'], $entry);
            
            $entries[] = $entry;
        }
        return $entries;
    }
}
