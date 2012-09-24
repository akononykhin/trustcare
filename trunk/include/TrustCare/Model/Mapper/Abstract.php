<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */
 
abstract class TrustCare_Model_Mapper_Abstract
{
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_dbAdapter = null;
    
    /**
     * @var Zend_Db_Table_Abstract
     */
    protected $_dbTable;

    /**
     * Flag either the last save operation was insert or update
     * @var bool
     */
    protected $_isLastOperationInsert = false;

    /**
     * Constructor
     *
     * @param  array|null $options
     * @return void
     */
    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }
    
    /**
     * @param  array $options 
     * @return void
     */
    public function setOptions(array $options)
    {
        if(array_key_exists('adapter', $options) && !is_null($options['adapter'])) {
            $this->setDbAdapter($options['adapter']);
        }

    }
    
    public function setDbAdapter(Zend_Db_Adapter_Abstract &$dbAdapter)
    {
        $this->_dbAdapter = $dbAdapter;
    }
    
    /**
     * @return Zend_Db_Adapter_Abstract
     */
    public function getDbAdapter()
    {
        if(is_null($this->_dbAdapter)) {
            $this->setDbAdapter(Zend_Registry::get('dbAdapter'));
        }
        return $this->_dbAdapter;
    }
    
    /**
     * Specify Zend_Db_Table instance to use for data operations
     * 
     * @param  Zend_Db_Table_Abstract $dbTable 
     * @return TrustCare_Model_Mapper_Abstract
     */
    public function setDbTable($dbTable)
    {
        $db = $this->getDbAdapter();
        
        if (is_string($dbTable)) {
            $dbTable = new $dbTable(array('db' => $db));
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
    
    /**
     * Get registered Zend_Db_Table instance
     *
     * Lazy loads TrustCare_Model_DbTable_* if no instance registered
     * 
     * @return Zend_Db_Table_Abstract
     */
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $className = get_class($this);
            if(preg_match("/^TrustCare_Model_Mapper_(.*)$/", $className, $matches)) {
                $dbTable = "TrustCare_Model_DbTable_" . $matches[1];
                $this->setDbTable($dbTable);
            }
        }
        return $this->_dbTable;
    }
    
    public function getInternalObjectName()
    {
        return $this->getDbTable()->info(Zend_Db_Table_Abstract::NAME);
    }
    
    public function isLastOperationInsert()
    {
        return $this->_isLastOperationInsert;
    }
    
    public function setLastOperationInsert($value)
    {
        $this->_isLastOperationInsert = !empty($value) ? true : false;
    }
}