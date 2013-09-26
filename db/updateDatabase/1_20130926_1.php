<?php
$g_majorDb = 1;
$g_minorDb = 20130926;
$g_buildDb = 1;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130926_1(Zend_Db_Adapter_Abstract $db) {


    try {
        $query = sprintf("
alter table frm_community add column `hiv_status` varchar(8) default NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    return true;
}