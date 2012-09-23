<?php
/**
 * Alexey Kononykhin (alexey.kononykhin@gmail.com)
 * 10.12.2010
 */

class ZendX_Translate extends Zend_Translate
{
    const CONST_DEF_LANG = 'en';
    const CONST_LANG_SEPARATOR = '||';
    const CONST_KEY_VALUE_SEPARATOR = ':=';

    public function packMultilanguageString($data)
    {
        $result = '';

        if (is_array($data)) {
            if (!array_key_exists(self::CONST_DEF_LANG, $data)) {
            	throw new Exception(sprintf("No data for default language %s", self::CONST_DEF_LANG));
            }
            foreach($data as $lang => $value) {
                if (!empty($result)) {
                    $result .= self::CONST_LANG_SEPARATOR;
                }
                $result .= $lang . self::CONST_KEY_VALUE_SEPARATOR . $value;
            }
        }
        else {
        	throw new Exception("Incorrect data structure");
        }
        return $result;
    }


    public function unPackMultilanguageString($data)
    {
        $result = array();
        $data = explode(self::CONST_LANG_SEPARATOR, $data);
        for ($i = 0;$i < count($data); $i++) {
            $temp = explode(self::CONST_KEY_VALUE_SEPARATOR, $data[$i]);
            
            $temp[0] = trim($temp[0]);
            $temp[1] = trim($temp[1]);
            
            $result[$temp[0]] = $temp[1];
        }
        return $result;
    }

    public function getTextInSpecifiedLanguage($data, $lang, $useDefaultIfNotFound = true)
    {
        $result = '';
        $default = $data;
        
        $langData = $this->unPackMultilanguageString($data);
        foreach($langData as $key => $text) {
            if (self::CONST_DEF_LANG == $key) {
                $default = $text;
            }
            if ($lang == $key) {
                $result = $text;
                break;
            }
        }
        if (empty($result) && ($useDefaultIfNotFound || $lang == self::CONST_DEF_LANG)) {
            $result = $default;
        }
        return $result;
    }
}
 


