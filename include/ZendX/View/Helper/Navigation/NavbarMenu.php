<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */

require_once 'Zend/View/Helper/Navigation/Menu.php';

class ZendX_View_Helper_Navigation_NavbarMenu
    extends Zend_View_Helper_Navigation_Menu
{
    private $_subIndicatorClass = "";
    private $_menuItemClass = "";
    
    /**
     * View helper entry point:
     * Retrieves helper and optionally sets container to operate on
     *
     * @param  Zend_Navigation_Container $container  [optional] container to
     *                                               operate on
     * @return Zend_View_Helper_Navigation_Menu      fluent interface,
     *                                               returns self
     */
    public function navbarMenu(Zend_Navigation_Container $container = null)
    {
        if (null !== $container) {
            $this->setContainer($container);
        }

        return $this;
    }
    

    /**
     * Determines whether a page should be accepted by checking it's privilege when iterating
     *
     * Rules:
     *  - Page is accepted if it has no privilege
     *  - Page is accepted if current user has specified privilege for specified privilege_scope
     *
     * @param  Zend_Navigation_Page $page  page to check
     * @return bool                        whether page is accepted by ACL
     */
    protected function _checkPrivilege(Zend_Navigation_Page $page)
    {
        $privilege = $page->getPrivilege();
        $context = $page->get('context');
        $orPrivileges = $page->get('or_privileges');
        
        if(is_null($privilege) && is_null($orPrivileges)) {
            return true;
        }
        if(!is_null($privilege) && App_Service_AccessControl::getInstance()->isGranted($privilege, $context)) {
            return true;
        }
        if(!is_null($orPrivileges) && is_array($orPrivileges)) {
            /* Any from array */
            foreach($orPrivileges as $info) {
                $privKey = array_key_exists('privilege', $info) ? $info['privilege'] : '';
                $privContext = array_key_exists('context', $info) ? $info['context'] : null;
                
                if(!empty($privKey) && App_Service_AccessControl::getInstance()->isGranted($privKey, $privContext)) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Determines whether a page should be accepted when iterating
     *
     * Rules:
     * - If a page is not visible it is not accepted, unless RenderInvisible has
     *   been set to true.
     *  - Page is accepted if checking privilege allows it
     * - If page is accepted by the rules above and $recursive is true, the page
     *   will not be accepted if it is the descendant of a non-accepted page.
     *
     * @param  Zend_Navigation_Page $page      page to check
     * @param  bool                $recursive  [optional] if true, page will not
     *                                         be accepted if it is the
     *                                         descendant of a page that is not
     *                                         accepted. Default is true.
     * @return bool                            whether page should be accepted
     */
    public function accept(Zend_Navigation_Page $page, $recursive = true)
    {
        // accept by default
        $accept = true;
    
        if (!$page->isVisible(false) && !$this->getRenderInvisible()) {
            // don't accept invisible pages
            $accept = false;
        } elseif (!$this->_checkPrivilege($page)) {
            // acl is not amused
            $accept = false;
        }
    
        if ($accept && $recursive) {
            $parent = $page->getParent();
            if ($parent instanceof Zend_Navigation_Page) {
                $accept = $this->accept($parent, true);
            }
        }
    
        return $accept;
    }
    
    
    /**
     * @param Zend_Navigation_Page $page
     */
    public function htmlify(Zend_Navigation_Page $page, $depth=0)
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

        $label = $this->view->escape($label);
        // get attribs for element
        $attribs = array(
            'id'     => $page->getId(),
            'title'  => $title,
        );

        // does page have a href?
        $elClasses = array();
        if ($href = $page->getHref()) {
            $element = 'a';
            $attribs['href'] = $href;
            $attribs['target'] = $page->getTarget();
        } else {
            $element = 'a';
            $attribs['href'] = "#";
            $attribs['target'] = $page->getTarget();
        }
        $hasVisible = !is_null($page->findOneBy('visible', true)) ? true : false;
        if($hasVisible) {
            $elClasses[] = "dropdown-toggle";
            $attribs['data-toggle'] = "dropdown";
            $attribs['data-target'] = "#";
            if(!$depth) {
                $label .= " <span class='caret'></span>";
            }
        }
        $attribs['class'] = join($elClasses, " ");
        
        
        return '<' . $element . $this->_htmlAttribs($attribs) . '>'
             . $label
             . '</' . $element . '>';
    }
    
    
    protected function _renderMenu(Zend_Navigation_Container $container,
                                   $ulClass,
                                   $indent,
                                   $minDepth,
                                   $maxDepth,
                                   $onlyActive)
    {
        $html = '';

        // find deepest active
        if ($found = $this->findActive($container, $minDepth, $maxDepth)) {
            $foundPage = $found['page'];
            $foundDepth = $found['depth'];
        } else {
            $foundPage = null;
        }

        // create iterator
        $iterator = new RecursiveIteratorIterator($container,
                            RecursiveIteratorIterator::SELF_FIRST);
        if (is_int($maxDepth)) {
            $iterator->setMaxDepth($maxDepth);
        }

        // iterate container
        $prevDepth = -1;
        foreach ($iterator as $page) {
            $depth = $iterator->getDepth();
            $isActive = $page->isActive(true);
            if ($depth < $minDepth || !$this->accept($page)) {
                // page is below minDepth or not accepted by acl/visibilty
                continue;
            } else if ($onlyActive && !$isActive) {
                // page is not active itself, but might be in the active branch
                $accept = false;
                if ($foundPage) {
                    if ($foundPage->hasPage($page)) {
                        // accept if page is a direct child of the active page
                        $accept = true;
                    } else if ($foundPage->getParent()->hasPage($page)) {
                        // page is a sibling of the active page...
                        if (!$foundPage->hasPages() ||
                            is_int($maxDepth) && $foundDepth + 1 > $maxDepth) {
                            // accept if active page has no children, or the
                            // children are too deep to be rendered
                            $accept = true;
                        }
                    }
                }

                if (!$accept) {
                    continue;
                }
            }

            // make sure indentation is correct
            $depth -= $minDepth;
            $myIndent = $indent . str_repeat('        ', $depth);

            if ($depth > $prevDepth) {
                // start new ul tag
                if ($ulClass && $depth ==  0) {
                    $ulClass = ' class="' . $ulClass . '"';
                } else {
                    //$ulClass = $page->hasPages() ? ' class="dropdown-menu"' : '';
                    $ulClass = ' role="menu" class="dropdown-menu"';
                }
                $html .= $myIndent . '<ul' . $ulClass . '>' . self::EOL;
            } else if ($prevDepth > $depth) {
                // close li/ul tags until we're at current depth
                for ($i = $prevDepth; $i > $depth; $i--) {
                    $ind = $indent . str_repeat('        ', $i);
                    $html .= $ind . '    </li>' . self::EOL;
                    $html .= $ind . '</ul>' . self::EOL;
                }
                // close previous li tag
                $html .= $myIndent . '    </li>' . self::EOL;
            } else {
                // close previous li tag
                $html .= $myIndent . '    </li>' . self::EOL;
            }

            // render li tag and page
            $liClasses = array();
            if($isActive) {
                $liClasses[] = 'active';
            }
            $hasVisible = !is_null($page->findOneBy('visible', true)) ? true : false;
            if($hasVisible) {
                if(!$depth) {
                    $liClasses[] = 'dropdown';
                }
                else {
                    $liClasses[] = 'dropdown-submenu';
                }
            }
            $liClass = count($liClasses) ? sprintf(" class='%s'", join($liClasses, ' ')) : '';
            $html .= $myIndent . '    <li' . $liClass . '>' . self::EOL
                   . $myIndent . '        ' . $this->htmlify($page, $depth) . self::EOL;

            // store as previous depth for next iteration
            $prevDepth = $depth;
        }

        if ($html) {
            // done iterating container; close open ul/li tags
            for ($i = $prevDepth+1; $i > 0; $i--) {
                $myIndent = $indent . str_repeat('        ', $i-1);
                $html .= $myIndent . '    </li>' . self::EOL
                       . $myIndent . '</ul>' . self::EOL;
            }
            $html = rtrim($html, self::EOL);
        }

        return $html;
    }
}