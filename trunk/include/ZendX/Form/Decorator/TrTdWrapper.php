<?php
/**
 *
 * Alexey Kononykhin
 * akononyhin@list.ru
 *
 */
require_once 'Zend/Form/Decorator/Abstract.php';

class ZendX_Form_Decorator_TrTdWrapper extends Zend_Form_Decorator_Abstract
{
    public function buildLabel()
    {
        $element = $this->getElement();
        $label = $element->getLabel();
        if ($translator = $element->getTranslator()) {
            $label = $translator->translate($label);
        }
        
        return $element->getView()->formLabel($element->getName(), $label);
    }

    public function buildErrors()
    {
        $element  = $this->getElement();
        $messages = $element->getMessages();
        if (empty($messages)) {
            return '';
        }
        return '<div class="errors">' . implode("<br/>", $messages) . '</div>';
    }

    public function buildDescription()
    {
        $element = $this->getElement();
        $desc    = $element->getDescription();
        if (empty($desc)) {
            return '';
        }
        return '<div class="description">' . $element->getView()->escape($desc) . '</div>';
    }

    public function render($content)
    {
        $element = $this->getElement();
        if (!$element instanceof Zend_Form_Element) {
            return $content;
        }
        if (null === $element->getView()) {
            return $content;
        }

        $label     = ($element instanceof Zend_Form_Element_Submit) ? '' : $this->buildLabel();
        $errors    = $this->buildErrors();
        $desc      = $this->buildDescription();

        $output = '<tr>';
        if(!empty($label)) {
            $output .= sprintf("<td><div class=\"%s\">%s</td>",
                            $element->isRequired() ? "required" : "normal",
                            $label);
        }
        $output .= sprintf("<td colspan=\"%s\">%s%s%s</td>",
                        !empty($label) ? "1" : "2",
                        $content,
                        $errors,
                        $desc);
        $output .= '</tr>';
        
        return $output;
    }
}
