<?php
/**
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 */

class TrustCare_SystemInterface_ReportGenerator_Community extends TrustCare_SystemInterface_ReportGenerator_Abstract
{
    public function getCode()
    {
        return self::CODE_COMMUNITY;
    }
    
    public function generate($params, $format = '')
    {
        $id_user = array_key_exists('id_user', $params) ? $params['id_user'] : null;
        $year = array_key_exists('year', $params) ? $params['year'] : -1;
        $month = array_key_exists('month', $params) ? $params['month'] : -1;
        $id_pharmacy = array_key_exists('id_pharmacy', $params) ? $params['id_pharmacy'] : -1;

        if(empty($format)) {
            $format = $this->getDefaultFormat();
        }
        
        
        $dbOptions = Zend_Registry::get('dbOptions');
        $db = Zend_Db::factory($dbOptions['adapter'], $dbOptions['params']);
        
        $fileName = sprintf("%s_%s%s_%s_%s.%s", $this->getCode(), $year, $month, gmdate("Ymd"), rand(0, 1000), strtolower($format));
        $fileReportOutput = sprintf("%s/%s", $this->reportsDirectory(), $fileName);
        
        $designFile= "community.rptdesign";
        $parameters = array();
        $parameters[] = sprintf('jdbc_driver_url=jdbc:mysql://%s/%s', $dbOptions['params']['host'], $dbOptions['params']['dbname']);
        $parameters[] = sprintf('jdbc_username=%s', $dbOptions['params']['username']);
        $parameters[] = sprintf('jdbc_password=%s', $dbOptions['params']['password']);
        $parameters[] = sprintf("month=%s", $month);
        $parameters[] = sprintf("year=%s", $year);
        $parameters[] = sprintf("id_pharmacy=%s", $id_pharmacy);

        $this->_generateReportFile($designFile, $fileReportOutput, $parameters, $format);

        $obj = new TrustCare_Model_ReportCommunity(array(
                'id_user' => $id_user,
                'generation_date' => ZendX_Db_Table_Abstract::LABEL_NOW,
        		'period' => sprintf("%04d%02d", $year, $month),
                'id_pharmacy' => $id_pharmacy,
                'filename' => $fileName,
        ));
        $obj->save();

        return $obj;
    }
    
    public function getDefaultFormat()
    {
        return "PDF";
    }
}
