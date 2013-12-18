<?php
$g_majorDb = 1;
$g_minorDb = 20131218;
$g_buildDb = 1;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20131218_1(Zend_Db_Adapter_Abstract $db) {

    try {
        $query = sprintf("
update pharmacy_dictionary set name='Moderate' where id=121;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    return true;
}