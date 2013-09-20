<?php
/**
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 */

class TrustCare_SystemInterface_ReportGenerator_Nafdac extends TrustCare_SystemInterface_ReportGenerator_Abstract
{
    protected $_runTimePrefix = '';
    
    public function getCode()
    {
        return self::CODE_NAFDAC;
    }
    
    public function generate($params, $format = '')
    {
        $id = array_key_exists('id', $params) ? $params['id'] : -1;

        if(empty($format)) {
            $format = $this->getDefaultFormat();
        }
        
        
        $dbOptions = Zend_Registry::get('dbOptions');
        $db = Zend_Db::factory($dbOptions['adapter'], $dbOptions['params']);
        
        $fileName = sprintf("%s_%s_%s.%s", $this->getCode(), $id, rand(0, 1000), strtolower($format));
        $fileReportOutput = sprintf("%s/%s", $this->reportsDirectory(), $fileName);
        
        $designFile= "nafdac.rptdesign";
        $parameters = array();
        $parameters[] = sprintf('jdbc_driver_url=jdbc:mysql://%s/%s', $dbOptions['params']['host'], $dbOptions['params']['dbname']);
        $parameters[] = sprintf('jdbc_username=%s', $dbOptions['params']['username']);
        $parameters[] = sprintf('jdbc_password=%s', $dbOptions['params']['password']);
        $parameters[] = sprintf("id=%s", $id);

        $this->_generateReportFile($designFile, $fileReportOutput, $parameters, $format);

        return $fileName;
    }
    
    public function getDefaultFormat()
    {
        return "PDF";
    }
}
