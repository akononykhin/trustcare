<?php
$g_majorDb = 1;
$g_minorDb = 20130916;
$g_buildDb = 3;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130916_3(Zend_Db_Adapter_Abstract $db) {

    try {
        $query = sprintf("
alter table nafdac add column `date_of_visit` datetime NOT NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    return true;
}