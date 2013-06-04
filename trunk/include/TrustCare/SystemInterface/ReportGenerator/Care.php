<?php
/**
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 */

class TrustCare_SystemInterface_ReportGenerator_Care extends TrustCare_SystemInterface_ReportGenerator_Abstract
{
    public function getCode()
    {
        return self::CODE_CARE;
    }
    
    public function generate($params, $format = '')
    {
        $id_user = array_key_exists('id_user', $params) ? $params['id_user'] : null;
        $year = array_key_exists('year', $params) ? $params['year'] : -1;
        $month = array_key_exists('month', $params) ? $params['month'] : -1;
        $month_index = array_key_exists('month_index', $params) ? $params['month_index'] : -1;
        $id_pharmacy = array_key_exists('id_pharmacy', $params) ? $params['id_pharmacy'] : -1;
        $male_younger_15 = array_key_exists('male_younger_15', $params) ? $params['male_younger_15'] : 0;
        $female_younger_15 = array_key_exists('female_younger_15', $params) ? $params['female_younger_15'] : 0;
        $male_from_15 = array_key_exists('male_from_15', $params) ? $params['male_from_15'] : 0;
        $female_from_15 = array_key_exists('female_from_15', $params) ? $params['female_from_15'] : 0;
        $drugs = array_key_exists('drugs', $params) ? $params['drugs'] : 0;

        if(empty($format)) {
            $format = $this->getDefaultFormat();
        }
        
        
        $dbOptions = Zend_Registry::get('dbOptions');
        $db = Zend_Db::factory($dbOptions['adapter'], $dbOptions['params']);
        
        $fileName = sprintf("%s_%s%s_%s_%s.%s", $this->getCode(), $year, $month, gmdate("Ymd"), rand(0, 1000), strtolower($format));
        $fileReportOutput = sprintf("%s/%s", $this->reportsDirectory(), $fileName);
        
        $designFile= "care.rptdesign";
        $parameters = array();
        $parameters[] = sprintf('jdbc_driver_url=jdbc:mysql://%s/%s', $dbOptions['params']['host'], $dbOptions['params']['dbname']);
        $parameters[] = sprintf('jdbc_username=%s', $dbOptions['params']['username']);
        $parameters[] = sprintf('jdbc_password=%s', $dbOptions['params']['password']);
        $parameters[] = sprintf("month_index=%s", $month_index);
        $parameters[] = sprintf("month=%s", $month);
        $parameters[] = sprintf("year=%s", $year);
        $parameters[] = sprintf("id_pharmacy=%s", $id_pharmacy);
        $parameters[] = sprintf("male_younger_15=%s", $male_younger_15);
        $parameters[] = sprintf("female_younger_15=%s", $female_younger_15);
        $parameters[] = sprintf("male_from_15=%s", $male_from_15);
        $parameters[] = sprintf("female_from_15=%s", $female_from_15);
        $parameters[] = sprintf("drugs=%s", $drugs);

        $this->_generateReportFile($designFile, $fileReportOutput, $parameters, $format);

        $obj = new TrustCare_Model_ReportCare(array(
                'id_user' => $id_user,
                'generation_date' => ZendX_Db_Table_Abstract::LABEL_NOW,
                'period' => sprintf("%04d%02d", $year, $month),
                'id_pharmacy' => $id_pharmacy,
                'number_of_clients_with_prescription_male_younger_15' => $male_younger_15,
                'number_of_clients_with_prescription_female_younger_15' => $female_younger_15,
                'number_of_clients_with_prescription_male_from_15' => $male_from_15,
                'number_of_clients_with_prescription_female_from_15' => $female_from_15,
                'number_of_dispensed_drugs' => $drugs,
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
