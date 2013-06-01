<?php
$g_majorDb = 1;
$g_minorDb = 20130529;
$g_buildDb = 1;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130529_1(Zend_Db_Adapter_Abstract $db) {


    try {
        $query = sprintf("
update pharmacy_dictionary_type set name='Types of Medication Error Intervention provided' where id=3;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
update pharmacy_dictionary_type set ordernum=ordernum+1 where id > 3;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }



    try {
        $query = sprintf("
insert into pharmacy_dictionary_type(id,ordernum,name) values (22,  4,  'Types of Adherence Intervention provided');
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    return true;
}