<?php
$g_majorDb = 1;
$g_minorDb = 20130928;
$g_buildDb = 4;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130928_4(Zend_Db_Adapter_Abstract $db) {


    try {
        $query = sprintf("
delete from pharmacy_dictionary where id in (402);
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
update pharmacy_dictionary set name='STIs Screening & Counselling' where id=401;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    return true;
}