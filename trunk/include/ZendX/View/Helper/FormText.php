<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 * Textbox may have special 'text_htmlsuf' attribute to be added after textbox control
 */

require_once 'Zend/View/Helper/FormText.php';


class ZendX_View_Helper_FormText extends Zend_View_Helper_FormText
{
    public function formText($name, $value = null, $attribs = null)
    {
        $xhtml = parent::formText($name, $value, $attribs);
        
        if(empty($xhtml)) {
            return $xhtml;
        }
        
        if(array_key_exists('text_htmlsuf', $attribs)) {
            $xhtml .= "&nbsp;".$attribs['text_htmlsuf'];
        }

        return $xhtml;
    }
}
