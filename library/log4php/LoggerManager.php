<?php
/**
 * Copyright 2004 The Apache Software Foundation.
 *
 * This software is published under the terms of the Apache Software
 * License version 2.0, a copy of which has been included with this
 * distribution in the LICENSE file.
 * 
 * @package log4php
 */

/**
 * LOG4PHP_DIR points to the log4php root directory.
 *
 * If not defined it will be set automatically when the first package classfile 
 * is included
 * 
 * @var string 
 */
if (!defined('LOG4PHP_DIR')) define('LOG4PHP_DIR', dirname(__FILE__));

require_once(LOG4PHP_DIR . '/LoggerHierarchy.php');

/**
 * Use the LoggerManager to get Logger instances.
 *
 * @author  Marco Vassura
 * @version $Revision: 556784 $
 * @package log4php
 * @see Logger
 * @todo create a configurator selector  
 */
class LoggerManager {
	/**
	 * 
	 * @var LoggerManager
	 */
    private static $instance = null;
    private $_repository = null;
    
    /**
     * 
     * @return LoggerManager
     */
    public static function singleton()
    {
        if (null === self::$instance) {
            self::$instance = new self();
            self::$instance->init();
        }

        return self::$instance;
    	
    }
	
	public function init()
	{
		if (!defined('LOG4PHP_DEFAULT_INIT_OVERRIDE')) {
			if (isset($_ENV['log4php.defaultInitOverride'])) {
				define('LOG4PHP_DEFAULT_INIT_OVERRIDE', LoggerOptionConverter::toBoolean($_ENV['log4php.defaultInitOverride'], false));
			} elseif (isset($GLOBALS['log4php.defaultInitOverride'])) {
				define('LOG4PHP_DEFAULT_INIT_OVERRIDE', LoggerOptionConverter::toBoolean($GLOBALS['log4php.defaultInitOverride'], false));
			} else {
				/**
				 * Controls init execution
				 *
				 * With this constant users can skip the default init procedure that is
				 * called when this file is included.
				 *
				 * <p>If it is not user defined, log4php tries to autoconfigure using (in order):</p>
				 *
				 * - the <code>$_ENV['log4php.defaultInitOverride']</code> variable.
				 * - the <code>$GLOBALS['log4php.defaultInitOverride']</code> global variable.
				 * - defaults to <i>false</i>
				 *
				 * @var boolean
				 */
				define('LOG4PHP_DEFAULT_INIT_OVERRIDE', false);
			}
		}

		if (!defined('LOG4PHP_CONFIGURATION')) {
			if (isset($_ENV['log4php.configuration'])) {
				define('LOG4PHP_CONFIGURATION', trim($_ENV['log4php.configuration']));
			} else {
				/**
				 * Configuration file.
				 *
				 * <p>This constant tells configurator classes where the configuration
				 * file is located.</p>
				 * <p>If not set by user, log4php tries to set it automatically using
				 * (in order):</p>
				 *
				 * - the <code>$_ENV['log4php.configuration']</code> enviroment variable.
				 * - defaults to 'log4php.properties'.
				 *
				 * @var string
				 */
				define('LOG4PHP_CONFIGURATION', 'log4php.properties');
			}
		}

		if (!defined('LOG4PHP_CONFIGURATOR_CLASS')) {
			if ( strtolower(substr( LOG4PHP_CONFIGURATION, -4 )) == '.xml') {
				define('LOG4PHP_CONFIGURATOR_CLASS', LOG4PHP_DIR . '/xml/LoggerDOMConfigurator');
			} else {
				/**
				 * Holds the configurator class name.
				 *
				 * <p>This constant is set with the fullname (path included but non the
				 * .php extension) of the configurator class that init procedure will use.</p>
				 * <p>If not set by user, log4php tries to set it automatically.</p>
				 * <p>If {@link LOG4PHP_CONFIGURATION} has '.xml' extension set the
				 * constants to '{@link LOG4PHP_DIR}/xml/{@link LoggerDOMConfigurator}'.</p>
				 * <p>Otherwise set the constants to
				 * '{@link LOG4PHP_DIR}/{@link LoggerPropertyConfigurator}'.</p>
				 *
				 * <p><b>Security Note</b>: classfile pointed by this constant will be brutally
				 * included with a:
				 * <code>@include_once(LOG4PHP_CONFIGURATOR_CLASS . ".php");</code></p>
				 *
				 * @var string
				 */
				define('LOG4PHP_CONFIGURATOR_CLASS', LOG4PHP_DIR . '/LoggerPropertyConfigurator');
			}
		}

		if (!LOG4PHP_DEFAULT_INIT_OVERRIDE) {
			$initialized = true;
			
			$configuratorClass = basename(LOG4PHP_CONFIGURATOR_CLASS);
			if (!class_exists($configuratorClass, false)) { /* to prevent autoload if enabled */
				include_once(LOG4PHP_CONFIGURATOR_CLASS . ".php");
			}
			if (class_exists($configuratorClass)) {

				$ret = call_user_func(array($configuratorClass, 'configure'), LOG4PHP_CONFIGURATION, $this->getLoggerRepository());
				if(!$ret) {
					$initialized = false;
				}

			} else {
				LoggerLog::warn("LoggerManagerDefaultInit() Configurator '{$configuratorClass}' doesnt exists");
				$initialized = false;
			}
				
			if (!$initialized) {
				LoggerLog::warn("LOG4PHP main() Default Init failed.");
			}
		}
	}

    /**
     * check if a given logger exists.
     * 
     * @param string $name logger name 
     * @static
     * @return boolean
     */
    public static function exists($name)
    {
        return self::singleton()->getLoggerRepository()->exists($name);
    }

    /**
     * Returns an array this whole Logger instances.
     * 
     * @static
     * @see Logger
     * @return array
     */
    public static function getCurrentLoggers()
    {
        return self::singleton()->getLoggerRepository()->getCurrentLoggers();
    }
    
    /**
     * Returns the root logger.
     * 
     * @static
     * @return object
     * @see LoggerRoot
     */
    public static function getRootLogger()
    {
        return self::singleton()->getLoggerRepository()->getRootLogger();
    }
    
    /**
     * Returns the specified Logger.
     * 
     * @param string $name logger name
     * @param LoggerFactory $factory a {@link LoggerFactory} instance or null
     * @static
     * @return Logger
     */
    public static function getLogger($name, $factory = null)
    {
        return self::singleton()->getLoggerRepository()->getLogger($name, $factory);
    }
    
    /**
     * Returns the LoggerHierarchy.
     * 
     * @static
     * @return LoggerHierarchy
     */
    public function getLoggerRepository()
    {
    	if(is_null($this->_repository)) {
    		$this->_repository = LoggerHierarchy::singleton();
    	}
    	return $this->_repository;    
    }
    

    /**
     * Destroy loggers object tree.
     * 
     * @static
     * @return boolean 
     */
    public static function resetConfiguration()
    {
        return self::singleton()->getLoggerRepository()->resetConfiguration();    
    }
    
    /**
     * Does nothing.
     * @static
     */
    public static function setRepositorySelector($selector, $guard)
    {
        return;
    }
    
    /**
     * Safely close all appenders.
     * @static
     */
    public static function shutdown()
    {
        return self::singleton()->getLoggerRepository()->shutdown();    
    }
}


