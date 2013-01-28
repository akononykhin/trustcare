<?php
/**
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 */

abstract class TrustCare_SystemInterface_ReportGenerator_Abstract
{
    const CODE_CARE = 'care';
    const CODE_COMMUNITY = 'community';
    const CODE_NAFDAC = 'nafdac';
    
    protected $_runTimePrefix = '';
    
    
    /**
     * @return TrustCare_SystemInterface_ReportGenerator_Abstract
     */
    public static function factory($code) {
        $object = null;
        
        try {
            $className = 'TrustCare_SystemInterface_ReportGenerator_' . str_replace(' ', '', ucwords(str_replace('_', ' ', $code)));
            $object = new $className();
        }
        catch(Exception $ex) {
            $logger = LoggerManager::getLogger("General");
            $logger->error($ex->getMessage());
        }
        
        return $object;
    }
    
    public static function getAvailableCodes()
    {
        $codes = array(TrustCare_SystemInterface_ReportGenerator_Abstract::CODE_CARE,
                       TrustCare_SystemInterface_ReportGenerator_Abstract::CODE_COMMUNITY,
        );
        
        return $codes;
    }
    
    /**
     * Generate report, save information to the database
     * 
     * @param array $params
     * @param string $format
     * 
     * @return object
     */
    abstract public function generate($params, $format = '');
    
    public static function reportsDirectory()
    {
        return realpath(APPLICATION_PATH . "/../reports/results");
    }
    
    public static function removeReportFile($fileName)
    {
        $fullFileName = self::reportsDirectory() . '/' . $fileName;
        unlink($fullFileName);
    }
    
    protected function _generateReportFile($designFile, $fileReportOutput, $parameters, $format)
    {
        $fileReportDesign = sprintf("%s/%s", realpath(APPLICATION_PATH . '/../reports'), $designFile);
        
        set_time_limit(0);
        $BIRT_RE_HOME = realpath (APPLICATION_PATH . "/../external/birt-runtime" . $this->_runTimePrefix);
        $BIRTCLASSPATH = "";
        
        $libDir = $BIRT_RE_HOME . "/lib";
        if ($handle = opendir($libDir)) {
            while (false !== ($file = readdir($handle))) {
                $fullFileName = $libDir . "/" . $file;
        
                $path_parts = pathinfo($fullFileName);
                if(array_key_exists('extension', $path_parts) && 'jar' == strtolower($path_parts['extension'])) {
                    $jar_separator = (substr(PHP_OS, 0, 3) == "WIN") ? ";" : ":";
                    $BIRTCLASSPATH .= $fullFileName . $jar_separator;
                }
            }
            closedir($handle);
        }
        
        
        $commandParams = array();
        $commandParams[] = sprintf("-Xmx1024m");
        //$commandParams[] = sprintf("-Xmx8192m");
        $commandParams[] = sprintf("-cp \"$BIRTCLASSPATH\"");
        $commandParams[] = sprintf("-DBIRT_HOME=\"%s\"", $BIRT_RE_HOME);
        $commandParams[] = "org.eclipse.birt.report.engine.api.ReportRunner";
        $commandParams[] = "-m runrender";
        foreach($parameters as $parameter) {
            $commandParams[] = "-p " . $parameter;
        }
        $commandParams[] = sprintf("-f %s", $format);
        $commandParams[] = sprintf("-o %s", $fileReportOutput);
        $commandParams[] = $fileReportDesign;
        
        $command = "java " . join(" ", $commandParams);
        exec($command, $output, $ret);
        
        if(0 != $ret) {
            throw new Exception(sprintf ("Error while preparing BIRT Report. Command: \r\n%s\n\nOutput: \n%s", $command, print_r($output, true)));
        }
        
        return true;
    }

    abstract public function getDefaultFormat();
} 
 


