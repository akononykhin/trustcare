<?php
$g_majorDb = 1;
$g_minorDb = 20121016;
$g_buildDb = 2;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20121016_2(Zend_Db_Adapter_Abstract $db) {


    try {
        $query = sprintf("
alter table frm_community add column `is_first_visit_to_pharmacy` int default NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    return true;
}