<?php
$g_majorDb = 1;
$g_minorDb = 20131008;
$g_buildDb = 1;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20131008_1(Zend_Db_Adapter_Abstract $db) {

    try {
        $query = sprintf("
update pharmacy_dictionary set name='STIs Screening & Counselling' where id=400;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
update pharmacy_dictionary set name='STIs Treatment' where id=401;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    return true;
}