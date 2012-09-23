<?php
/**
 * 
 * Alexey Kononykhin
 * akononyhin@list.ru
 * 
 */
require_once 'Zend/Form/Decorator/Abstract.php';

class ZendX_Form_Decorator_FormErrors extends Zend_Form_Decorator_Abstract
{

    /**
     * Render errors
     * 
     * @param  string $content 
     * @return string
     */
    public function render($content)
    {
        $form = $this->getElement();
        if (!$form instanceof Zend_Form) {
            return $content;
        }

        $view = $form->getView();
        if (null === $view) {
            return $content;
        }
        
        $messages = $form->getErrorMessages();
        if(empty($messages)) {
            return $content;
        }
        
        $markup = '<div class="form_errors">' . join($messages, "<br/>") . "</div>";

        switch ($this->getPlacement()) {
            case self::APPEND:
                return $content . $this->getSeparator() . $markup;
            case self::PREPEND:
                return $markup . $this->getSeparator() . $content;
        }
    }
}


