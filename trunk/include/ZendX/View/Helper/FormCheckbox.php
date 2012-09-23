<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 * Checkbox may have special 'label' attributes and label_comment attribute to be added after checkbox control
 */

require_once 'Zend/View/Helper/FormCheckbox.php';


class ZendX_View_Helper_FormCheckbox extends Zend_View_Helper_FormCheckbox
{
    public function formCheckbox($name, $value = null, $attribs = null, array $checkedOptions = null)
    {
        $label_attribs = array();

        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, id, value, attribs, options, listsep, disable

        // retrieve attributes for labels (prefixed with 'label_' or 'label')
        foreach ($attribs as $key => $val) {
            $tmp    = false;
            $keyLen = strlen($key);
            if ((6 < $keyLen) && (substr($key, 0, 6) == 'label_')) {
                $tmp = substr($key, 6);
            } elseif ((5 < $keyLen) && (substr($key, 0, 5) == 'label')) {
                $tmp = substr($key, 5);
            }

            if ($tmp) {
                // make sure first char is lowercase
                $tmp[0] = strtolower($tmp[0]);
                $label_attribs[$tmp] = $val;
                unset($attribs[$key]);
            }
        }
        
        $xhtml = parent::formCheckbox($name, $value, $attribs, $checkedOptions);
        
        if(empty($xhtml)) {
            return $xhtml;
        }

        $labelPlacement = 'append';
        $labelComment = '';
        foreach ($label_attribs as $key => $val) {
            switch (strtolower($key)) {
                case 'placement':
                    unset($label_attribs[$key]);
                    $val = strtolower($val);
                    if (in_array($val, array('prepend', 'append'))) {
                        $labelPlacement = $val;
                    }
                    break;
                case 'comment':
                    unset($label_attribs[$key]);
                    $labelComment = $val;
                    break;
            }
        }

        $content .= '<label'
                 . $this->_htmlAttribs($label_attribs)
                 . ' for="' . $this->view->escape($id) . '"'
                 . '>'
                 . (('prepend' == $labelPlacement) ? $labelComment : '')
                 . $xhtml
                 . (('append' == $labelPlacement) ? $labelComment : '')
                 . '</label>';

        return $content;
    }

    /**
     * Determine checkbox information
     * 
     * @param  string $value 
     * @param  bool $checked 
     * @param  array|null $checkedOptions 
     * @return array
     */
    public static function determineCheckboxInfo($value, $checked, array $checkedOptions = null)
    {
        // Checked/unchecked values
        $checkedValue   = null;
        $unCheckedValue = null;
        if (is_array($checkedOptions)) {
            if (array_key_exists('checked', $checkedOptions)) {
                $checkedValue = (string) $checkedOptions['checked'];
                unset($checkedOptions['checked']);
            }
            if (array_key_exists('unChecked', $checkedOptions)) {
                $unCheckedValue = (string) $checkedOptions['unChecked'];
                unset($checkedOptions['unChecked']);
            }
            if (null === $checkedValue) {
                $checkedValue = array_shift($checkedOptions);
            }
            if (null === $unCheckedValue) {
                $unCheckedValue = array_shift($checkedOptions);
            }
        } elseif ($value !== null) {
            $unCheckedValue = self::$_defaultCheckedOptions['unChecked'];
        } else {
            $checkedValue   = self::$_defaultCheckedOptions['checked'];
            $unCheckedValue = self::$_defaultCheckedOptions['unChecked'];
        }

        // is the element checked?
        $checkedString = '';
        if ($checked || ($value === $checkedValue)) {
            $checkedString = ' checked="checked"';
            $checked = true;
        } else {
            $checked = false;
        }

        // Checked value should be value if no checked options provided
        if ($checkedValue == null) {
            $checkedValue = $value;
        }

        return array(
            'checked'        => $checked,
            'checkedString'  => $checkedString,
            'checkedValue'   => $checkedValue,
            'unCheckedValue' => $unCheckedValue,
        );
    }
}
