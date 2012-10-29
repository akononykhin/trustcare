<?php
$g_majorDb = 1;
$g_minorDb = 20121029;
$g_buildDb = 2;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20121029_2(Zend_Db_Adapter_Abstract $db) {

    try {
        $query = sprintf("
delete from db_sequence where name='frm_community_htc_result_id_seq';
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    return true;
}