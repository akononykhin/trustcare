<?php
$g_majorDb = 1;
$g_minorDb = 20130128;
$g_buildDb = 5;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130128_5(Zend_Db_Adapter_Abstract $db) {


    try {
        $query = sprintf("
alter table nafdac_medicine add column `route` varchar(255) default NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    return true;
}