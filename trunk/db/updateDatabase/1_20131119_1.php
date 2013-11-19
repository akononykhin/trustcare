<?php
$g_majorDb = 1;
$g_minorDb = 20131119;
$g_buildDb = 1;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20131119_1(Zend_Db_Adapter_Abstract $db) {

    try {
        $query = sprintf("
update pharmacy_dictionary set name='Indeterminate' where id=342;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
update pharmacy_dictionary set name='Dots' where id=423;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    return true;
}