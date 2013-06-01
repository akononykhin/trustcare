<?php
$g_majorDb = 1;
$g_minorDb = 20130128;
$g_buildDb = 3;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130128_3(Zend_Db_Adapter_Abstract $db) {


    try {
        $query = sprintf("
alter table nafdac add column generation_date datetime NOT NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    return true;
}