<?php
$g_majorDb = 1;
$g_minorDb = 20121029;
$g_buildDb = 1;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20121029_1(Zend_Db_Adapter_Abstract $db) {

    try {
        $query = sprintf("
alter table frm_community add column `htc_result_id` int default NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
drop table frm_community_htc_result;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    return true;
}