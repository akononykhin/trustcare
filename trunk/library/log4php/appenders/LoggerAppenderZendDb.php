<?php
/**
 * @ignore 
 */
if (!defined('LOG4PHP_DIR')) define('LOG4PHP_DIR', dirname(__FILE__) . '/..');

require_once(LOG4PHP_DIR . '/LoggerAppenderSkeleton.php');
require_once(LOG4PHP_DIR . '/helpers/LoggerOptionConverter.php');
require_once(LOG4PHP_DIR . '/LoggerLog.php');

/**
 * Appends log events to a db table with predifined structure:
 * CREATE TABLE log4php (
 *    `id` bigint(20) NOT NULL,
 *    `timestamp` datetime NOT NULL,
 *    `logger` varchar(32),
 *    `level` varchar(32),
 *    `message` text,
 *    `thread` varchar(32),
 *    `file` varchar(255),
 *    `line` varchar(4),
 *  PRIMARY KEY  (`id`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 * 
 *
 * <p>This appender uses a table in a database to log events.</p>
 * <p>Parameters are {@link $dsn}, {@link $createTable}, {@link table}
 * <p>See examples in test directory.</p>
 *
 */
class LoggerAppenderZendDb extends LoggerAppenderSkeleton {

    /**
     * Create the log table if it does not exists (optional).
     * @var boolean
     */
    private $createTable = true;

    /**
     * 
     * @var Zend_Db
     */
    private $dbAdapter;
    
    /**
     * Last time when tried to open dbAdapter connection
     * @var int
     */
    private $_lastActivateTime = null;
    
    /**
     * A {@link LoggerPatternLayout} string used to format a valid insert query (mandatory).
     * @var string
     */
    private $sql;
    
    /**
     * Table name to write events. Used only if {@link $createTable} is true.
     * @var string
     */    
    private $table;
    
    /**
     * @var boolean used to check if all conditions to append are true
     * @access private
     */
    private $canAppend = true;
    
    /**    
     * @access private
     */
    protected $requiresLayout = false;
	
	
    
    

    /**
     * Setup db connection.
     * Based on defined options, this method connects to db defined in {@link $dsn}
     * and creates a {@link $table} table if {@link $createTable} is true.
     * @return boolean true if all ok.
     */
    public function activateOptions()
    {
        $this->dbAdapter = Zend_Registry::get("Storage")->getPersistantDb(); 
    	
    	try {
    		$this->dbAdapter->getConnection();
    		
    		$this->layout = LoggerLayout::factory('LoggerPatternLayout');
    		$this->layout->setConversionPattern($this->getSql());

    		// test if log table exists
            $this->dbAdapter->setFetchMode(Zend_Db::FETCH_OBJ);
            try {
            	$result = $this->dbAdapter->fetchRow("select current_timestamp();");
            	$exists = true;
            }
            catch(Exception $ex) {
            	$exists = false;
            }
    		
    		if (!$exists && $this->getCreateTable()) {
    			$query = "CREATE TABLE {$this->table} (timestamp varchar(32),logger varchar(32),level varchar(32),message varchar(64),thread varchar(32),file varchar(255),line varchar(4) );";

    			LoggerLog::debug("LoggerAppenderDb::activateOptions() creating table '{$this->table}'... using sql='$query'");
    			 
    			$stmt = $this->dbAdapter->query($query);
    		}
    		$this->canAppend = true;
    		$this->closed = false;

    	} catch (Exception $ex) {
            LoggerLog::debug(sprintf("LoggerAppenderDb::activateOptions() Error: %s", $ex->getMessage()));            
            $this->dbAdapter = null;
            $this->closed = true;
            $this->canAppend = false;
    	}
    	$this->_lastActivateTime = time();
    }
    
    function close()
    {
        if ($this->dbAdapter !== null) {
            $this->dbAdapter->closeConnection();
            $this->dbAdapter = null;
        }
        $this->closed = true;
    }
    
    /**
     * @return boolean
     */
    function getCreateTable()
    {
        return $this->createTable;
    }
    
    /**
     * @return string the sql pattern string
     */
    function getSql()
    {
        return $this->sql;
    }
    
    /**
     * @return string the table name to create
     */
    function getTable()
    {
        return $this->table;
    }
    
    function setCreateTable($flag)
    {
        $this->createTable = LoggerOptionConverter::toBoolean($flag, true);
    }
    
    function setSql($sql)
    {
        $this->sql = $sql;    
    }
    
    function setTable($table)
    {
        $this->table = $table;
    }
	
    /**
     * @param LoggerLoggingEvent $event
     */
    public function append($event)
    {
        if ($this->canAppend) {
            $message = $event->getRenderedMessage();
            $file = $event->getLocationInformation()->getFileName();
            $stack = '';
            if ($event->getLevel()->isGreaterOrEqual(LoggerLevel::getLevelError())) {
                $traceStack = $event->getLocationStack();

                $trace = array();
                for($i = 0; $i < count($traceStack); $i++) {
                    $trace[] = sprintf("\t\t#%d: %s (%s, %s)", $i, $traceStack[$i]['file'], $traceStack[$i]['line'], $traceStack[$i]['function']);
                }
                $stack = LOG4PHP_LINE_SEP.join(LOG4PHP_LINE_SEP, $trace).LOG4PHP_LINE_SEP;
            }

            $query = sprintf("insert into %s(id, timestamp,logger,level,message,thread,file,line) values(%d,str_to_date('%s', '%%Y-%%m-%%d %%H:%%i:%%s'),'%s','%s','%s','%s','%s','%s');",
                             $this->getTable(),
                             $this->dbAdapter->nextSequenceId('log4php_id_seq'),
                             gmdate("Y-m-d H:i:s"),
                             $event->getLoggerName(),
                             $event->getLevel()->toString(),
                             addslashes($message).addslashes($stack),
                             $event->getThreadName(),
                             addslashes($file),
                             $event->getLocationInformation()->getLineNumber());
            
            LoggerLog::debug("LoggerAppenderCustomDb::append() query='$query'");

            try {
                $this->dbAdapter->logSQLErrors(false);
            	$stmt = $this->dbAdapter->query($query);
            }
            catch(Exception $ex) {
            	// Try to reopen connection
            	if(time() > ($this->_lastActivateTime + 10)) { /* to prevent deadlock on queris by broken connection */
            		$this->close();
            		$this->activateOptions();
            		try {
            			if(!is_null($this->dbAdapter)) {
            				$stmt = $this->dbAdapter->query($query);
            				return;
            			}
            		}
            		catch(Exception $ex1) {}
            	}
            	$message = sprintf("CAN'T SAVE LOG MESSAGE to DB:\n\t%s\nError: %s", $query, $ex->getMessage());
            	$logger = LoggerManager::getLogger('DB_ERROR');
            	$logger->error($message);
            }
        }
    }
}
