<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */

class ZendX_Form extends Zend_Form {
    public function init()
    {
        $this->addPrefixPath('ZendX_Form_Decorator',
                             'ZendX/Form/Decorator/',
                             self::DECORATOR);
        $this->addPrefixPath('ZendX_Form_Element',
                             'ZendX/Form/Element/',
                             self::ELEMENT);
                             
        $this->addElementPrefixPath('ZendX_Validate',
                                    'ZendX/Validate/',
                                    'validate');
    }
    
    /**
     * Load the default decorators
     * 
     * @return void
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('FormErrors')
                 ->addDecorator('FormElements')
                 ->addDecorator('HtmlTag', array('tag' => 'table'))
                 ->addDecorator('Form', array('class' => 'zend_form'));
        }
    }
    
    
    /**
     * Create an element and add customized decorators
     *
     * 
     * @param  string $type 
     * @param  string $name 
     * @param  array|Zend_Config $options 
     * @return Zend_Form_Element
     */
    public function createElement($type, $name, $options = null)
    {
        $element = parent::createElement($type, $name, $options);
        
        $element->clearDecorators();
        $element->addDecorator('Tooltip');
        if($element instanceof Zend_Form_Element_File) {
            $element->addDecorator('File');
        }
        else {
            $element->addDecorator('ViewHelper');
        }
        $element->addDecorator('TrTdWrapper');
        return $element;
    }
}

