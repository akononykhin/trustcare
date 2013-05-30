<?php
$g_majorDb = 1;
$g_minorDb = 20130530;
$g_buildDb = 2;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130530_2(Zend_Db_Adapter_Abstract $db) {


    try {
        $query = sprintf("
update pharmacy_dictionary_type set name='Type of Medication Error Intervention Outcome' where id=4;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
update pharmacy_dictionary_type set ordernum=ordernum+1 where ordernum > 5;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }



    try {
        $query = sprintf("
insert into pharmacy_dictionary_type(id,ordernum,name) values (23,  6,  'Type of Adherence Intervention Outcome');
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    return true;
}