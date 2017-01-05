<?php

class Adr_LanguageController extends ZendX_Controller_Action
{

    public function loadDictForJsAction()
    {
        $o = new stdClass();
        
        $language = $this->_getParam('language');
        $namespace = $this->_getParam('namespace');
        
        try {
            $locale = new Zend_Locale($language);
            
            $cache = Zend_Cache::factory('Core',
                'File',
                array(
                    'lifetime' => 120,
                    'automatic_serialization' => true
                ),
                array()
            );
            Zend_Translate::setCache($cache);
            
            $translate = new ZendX_Translate(
                'gettext',
                APPLICATION_PATH . '/language/' . str_replace("_", "/", $namespace),
                $locale,
                array(
                    'scan' => Zend_Translate::LOCALE_FILENAME,
                )
            );
            
            if (!$translate->isAvailable($locale->getLanguage())) {
                $locale = new Zend_Locale('en');
            }
            
            $messages = $translate->getMessages();
            
            $o->{$language}[$namespace] = $messages;
            
        }
        catch(Exception $ex) {
            $exMessage = $ex->getMessage();
            if(!empty($exMessage)) {
                $this->getLogger()->error($exMessage);
            }
        }
    
    
        $this->_helper->json($o);
    }

}

