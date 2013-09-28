<?php
$g_majorDb = 1;
$g_minorDb = 20130928;
$g_buildDb = 3;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130928_3(Zend_Db_Adapter_Abstract $db) {


    try {
        $query = sprintf("
delete from frm_community;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
delete from pharmacy_dictionary where id in (360,362,364,365,366);
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (360, 19, 'Opportunistic Infections Screening & Management');
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    return true;
}