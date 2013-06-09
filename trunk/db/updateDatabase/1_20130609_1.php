<?php
$g_majorDb = 1;
$g_minorDb = 20130609;
$g_buildDb = 1;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130609_1(Zend_Db_Adapter_Abstract $db) {


    try {
        $query = sprintf("
alter table pharmacy_dictionary add column is_active bool default 1;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    

    return true;
}