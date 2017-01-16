<?php
$g_majorDb = 1;
$g_minorDb = 20170110;
$g_buildDb = 2;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20170110_2(Zend_Db_Adapter_Abstract $db) {

    try {
        $query = sprintf("
alter table nafdac add column `reporter_email` varchar(128) default NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    return true;
}