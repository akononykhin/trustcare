<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 *
 */

abstract class TrustCare_Model_Abstract
{
    /**
     * Options specific for logging of changes
     * @var unknown_type
     */
    protected $_logOptions = array();
    /**
     * @var TrustCare_Model_Mapper_Abstract
     */
    protected $_mapper;
    protected $_mapperOptions = array();
    
    /**
     * @var Logger
     */
    private $_logger;
    
    protected $_skipTrackChanges = false;
    protected $_logObjectChanges = true;
    private $_changedObjectParameters = array();
    private $_objectKeyInfo;
    
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
     * 
     * @return Logger
     */
    public function getLogger()
    {
        if(is_null($this->_logger)) {
            $this->_logger = LoggerManager::getLogger(__CLASS__);
        }
        return $this->_logger;
    }
    
    /**
     * Overloading: allow property access
     * 
     * @param  string $name 
     * @param  mixed $value 
     * @return void
     */
    public function __set($name, $value)
    {
        $method = 'set' . $this->prepareMethodName($name);
        if ('mapper' == $name || !method_exists($this, $method)) {
            throw new Exception(sprintf("Invalid property specified: %s", $name));
        }
        $this->$method($value);
    }

    /**
     * Overloading: allow property access
     * 
     * @param  string $name 
     * @return mixed
     */
    public function __get($name)
    {
        $method = 'get' . $this->prepareMethodName($name);
        if ('mapper' == $name || !method_exists($this, $method)) {
            throw new Exception(sprintf("Invalid property specified: %s", $name));
        }
        return $this->$method();
    }
    
    /**
     * Set object state
     * 
     * @param  array $options 
     * @return TrustCare_Model_Abstract
     */
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            if('mapperOptions' == $key && is_array($value)) {
                $this->_mapperOptions = $value;
                continue;
            }
            if('logOptions' == $key && is_array($value)) {
                $this->_logOptions = $value;
                continue;
            }
            $method = 'set' . $this->prepareMethodName($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }
    
    
    /**
     * Set data mapper
     * 
     * @param  mixed $mapper 
     * @return TrustCare_Model_Abstract
     */
    public function setMapper($mapper)
    {
        $this->_mapper = $mapper;
        return $this;
    }


    /**
     * Get data mapper
     *
     * Lazy loads of mapper instance if no mapper registered.
     * 
     * @return TrustCare_Model_Mapper_Abstract
     */
    public function getMapper()
    {
        if (null === $this->_mapper) {
            $className = get_class($this);
            if(preg_match("/^TrustCare_Model_(.*)$/", $className, $matches)) {
                $mapperClassName = "TrustCare_Model_Mapper_" . $matches[1];
                $this->setMapper(new $mapperClassName($this->_mapperOptions));
            }
        }
        return $this->_mapper;
    }
    
    public function save()
    {
        $this->getMapper()->save($this);
        if($this->_logObjectChanges) {
            if($this->getMapper()->isLastOperationInsert()) {
                $this->logObjectInsert();
            }
            else {
                $this->logObjectUpdate();
            }
        }
        $this->clearChangedParameters();
    }
    
    
    public function delete()
    {
        $this->getMapper()->delete($this);
        $this->logObjectDelete();
    }
    
    
    /**
     * Fetch all entries
     * 
     * @return array
     */
    public function fetchAll($clauses = array())
    {
        if(!is_array($clauses)) {
            $clauses = array($clauses);
        }
        return $this->getMapper()->fetchAll($clauses);
    }
    
    private function prepareMethodName($name)
    {
        $methodName = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
        return $methodName;
    }
    
    protected function isSkipTrackChanges()
    {
        return !empty($this->_skipTrackChanges) ? true : false;
    }
    
    public function setSkipTrackChanges($value)
    {
        $this->_skipTrackChanges = !empty($value) ? true : false;
    }
    
    protected function _parameterChanged($paramName, $newValue, $is_bool = false, $is_password = false)
    {
        if($this->isSkipTrackChanges()) {
            return;
        }
        
        $propertyName = '_' . $paramName;
        $oldValue = $this->{$propertyName};
        if($is_bool) {
            $newValue = !empty($newValue) ? 1 : 0;
            $oldValue = !empty($oldValue) ? 1 : 0;
        }
        
        if(!array_key_exists($paramName, $this->_changedObjectParameters)) {
            $this->_changedObjectParameters[$paramName] = array('old' => $oldValue, 'new' => $newValue, 'is_password' => $is_password);
        }
        else {
            $this->_changedObjectParameters[$paramName]['new'] = $newValue;
        }
    }
    
    public function isParameterChanged($paramName)
    {
        return array_key_exists($paramName, $this->_changedObjectParameters);
    }
    
    public function setObjectKeyInfo($keyInfo) {
        $key = '';
        if(is_array($keyInfo)) {
            $info = array();
            foreach($keyInfo as $index=>$value) {
                $info[] = sprintf("%s=%s", $index, $value);
            }
            $key = join(",", $info);
        }
        else {
            $key = $keyInfo;
        }
        $this->_objectKeyInfo = $key;
    }
    
    public function logObjectInsert() {
        $this->logObjectChanges("Created", true, false);
    }
    
    public function logObjectUpdate() {
        $this->logObjectChanges("Modified");
    }
    
    public function logObjectDelete() {
        $this->logObjectChanges("Deleted", false, false);
    }
    
    
    private function logObjectChanges($comment, $logForInsert = false, $checkParameters = true)
    {
        if(!$this->_logObjectChanges) {
            return;
        }
        

        $actionRows = array();
        $actionRows[] = $comment;
        foreach($this->_changedObjectParameters as $name => $param) {
            if($logForInsert) {
                $actionRows[] = sprintf("\t%s: '%s'",
                                    $name,
                                    (empty($param['is_password']) ? $param['new'] : "********"));
            }
            else if($param['old'] != $param['new']){
                $actionRows[] = sprintf("\t%s: '%s' -> '%s'",
                                    $name,
                                    (empty($param['is_password']) ? $param['old'] : "********"),
                                    (empty($param['is_password']) ? $param['new'] : "********"));
            }
        }
        if($checkParameters && count($actionRows) < 2) { /* always have at least one row - comment */
            return;
        }
        $action = join("\n", $actionRows);
        
        if(array_key_exists('author', $this->_logOptions)) {
            $author = $this->_logOptions['author'];
        }
        else {
            if(Zend_Session::isStarted()) {
                $author = sprintf("admin: %s", Zend_Auth::getInstance()->getIdentity());
            }
            else {
                $author = "unknown";
            }
        }
        if(array_key_exists('from_ip', $this->_logOptions)) {
            $from_ip = $this->_logOptions['from_ip'];
        }
        else {
            $from_ip =  $_SERVER["REMOTE_ADDR"];
        }
        $objectName = $this->getMapper()->getInternalObjectName();
        if(empty($objectName)) {
            $objectName = get_class($this);
        }
        $keyInfo = $this->_objectKeyInfo;
        
        $trace = array();
        if (function_exists('debug_backtrace')) {
            $traceStack = debug_backtrace();
            for($i = 0; $i < count($traceStack); $i++) {
                $trace[] = sprintf("#%d: %s (%s, %s)", $i, $traceStack[$i]['file'], $traceStack[$i]['line'], $traceStack[$i]['function']);
            }
        }
        $stack = join("\n", $trace);
        
        
        $obj = new TrustCare_Model_LogObjects(array('mapperOptions' => $this->_mapperOptions));
        $obj->setAuthor($author);
        $obj->setFromIp($from_ip);
        $obj->setStack($stack);
        $obj->setAction($action);
        $obj->setObjectName($objectName);
        $obj->setKeyInfo($keyInfo);
        $obj->setTimestamp(gmdate("Y-m-d H:i:s"));
        $obj->save();
        
    }
    
    private function clearChangedParameters()
    {
        $this->_changedObjectParameters = array();
    }
}