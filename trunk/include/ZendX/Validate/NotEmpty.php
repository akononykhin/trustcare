<?php
/**
 * 
 * Alexey Kononykhin
 * akononyhin@list.ru
 * 
 */
class ZendX_Validate_NotEmpty extends Zend_Validate_NotEmpty
{
    public function __construct() {
        $this->setMessage(_("Necessary to enter value"), self::IS_EMPTY);
    }
}


