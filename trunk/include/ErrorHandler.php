<?php
    function errorHandler($errno, $errstr, $errfile, $errline, $errorContext = array())
    {
        $errortype = array (
                    E_ERROR           => "Error",
                    E_WARNING         => "Warning",
                    E_PARSE           => "Parsing Error",
                    E_NOTICE          => "Notice",
                    E_CORE_ERROR      => "Core Error",
                    E_CORE_WARNING    => "Core Warning",
                    E_COMPILE_ERROR   => "Compile Error",
                    E_COMPILE_WARNING => "Compile Warning",
                    E_USER_ERROR      => "User Error",
                    E_USER_WARNING    => "User Warning",
                    E_USER_NOTICE     => "User Notice",
                    E_STRICT          => "Runtime Notice"
                    );
      
        if ($errno & ini_get('error_reporting')) {
            $logger = LoggerManager::getLogger("PHP");
            $dt = gmdate("Y-m-d H:i:s O");
            
            $full_trace = debug_backtrace();
            $trace = array();
            for($i = 1; $i < count($full_trace); $i++) {
                $trace[] = sprintf("#%d: %s (%s, %s)", $i, $full_trace[$i]['file'], $full_trace[$i]['line'], $full_trace[$i]['function']);
                
                if ('exceptionHandler' == $full_trace[$i]['function']) {
                    $exception_trace = $full_trace[$i]['args'][0]->getTrace();
                    for($j = 0; $j < count($exception_trace); $j++) {
                        $trace[] = sprintf("#%d-%d: %s (%s, %s)", $i, $j, $exception_trace[$j]['file'], $exception_trace[$j]['line'], $exception_trace[$j]['function']);
                    }
                }
            }
          
            $err = sprintf("%s : %.20s %s(line=%s)\n\t%s\n\tTrace:\n\t\t%s\n\n", $dt, $errortype[$errno], $errfile, $errline, $errstr, join("\n\t\t", $trace));
            switch($errno) {
                case E_ERROR:
                case E_PARSE:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                case E_STRICT:
                    $logger->error($err);
                    break;
                default:
                    $logger->warn($err);
                    break;
            }
            if (in_array($errno, array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR))) {
                die("Internal Error");
            }
        }
    }
  
    /**
     * Exception handler, passes them to billing_errorHandler to do the actual work
     *
     * @access private
     */
    function exceptionHandler($ex)
    {
        errorHandler(E_ERROR, $ex->getMessage(),$ex->getFile(),$ex->getLine());
    }
      
