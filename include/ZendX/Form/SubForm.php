<?php
/**
 * 
 * Alexey Kononykhin
 * akononyhin@list.ru
 * 
 */

/** Zend_Form */
require_once 'ZendX/Form.php';

class ZendX_Form_SubForm extends ZendX_Form
{
    /**
     * Whether or not form elements are members of an array
     * @var bool
     */
    protected $_isArray = true;

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
                 ->addDecorator('HtmlTag', array('tag' => 'table'));
        }
    }
}
