<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */

require_once 'Zend/View/Helper/Navigation/Menu.php';

class ZendX_View_Helper_Navigation_DropDownMenu
    extends Zend_View_Helper_Navigation_Menu
{
    private $_subIndicatorClass = "sf-sub-indicator";
    private $_menuItemClass = "sf-with-ul";
    
    /**
     * View helper entry point:
     * Retrieves helper and optionally sets container to operate on
     *
     * @param  Zend_Navigation_Container $container  [optional] container to
     *                                               operate on
     * @return Zend_View_Helper_Navigation_Menu      fluent interface,
     *                                               returns self
     */
    public function dropDownMenu(Zend_Navigation_Container $container = null)
    {
        if (null !== $container) {
            $this->setContainer($container);
        }

        return $this;
    }
    
    /**
     * @param Zend_Navigation_Page $page
     */
    public function htmlify(Zend_Navigation_Page $page)
    {
        // get label and title for translating
        $label = $page->getLabel();
        $title = $page->getTitle();

        // translate label and title?
        if ($this->getUseTranslator() && $t = $this->getTranslator()) {
            if (is_string($label) && !empty($label)) {
                $label = $t->translate($label);
            }
            if (is_string($title) && !empty($title)) {
                $title = $t->translate($title);
            }
        }

        // get attribs for element
        $attribs = array(
            'id'     => $page->getId(),
            'title'  => $title,
            'class'  => $page->getClass() . " " . $this->_menuItemClass,
        );

        // does page have a href?
        if ($href = $page->getHref()) {
            $element = 'a';
            $attribs['href'] = $href;
            $attribs['target'] = $page->getTarget();
        } else {
            $element = 'span';
        }
        
        return '<' . $element . $this->_htmlAttribs($attribs) . '>'
             . $this->view->escape($label)
             . '</' . $element . '>';
    }
}