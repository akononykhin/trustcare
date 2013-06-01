<?php
$g_majorDb = 1;
$g_minorDb = 20130530;
$g_buildDb = 3;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130530_3(Zend_Db_Adapter_Abstract $db) {

    try {
        $query = sprintf("
delete from frm_care;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
delete from pharmacy_dictionary where id_pharmacy_dictionary_type=4;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    $pDb = Zend_Registry::get("Storage")->getPersistantDb(); 
    try {
        $id = $pDb->nextSequenceId('pharmacy_dictionary_id_seq');
        $query = sprintf("
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (%d, 4, 'Medication error(s) corrected or addressed');
        ", $id);
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $id = $pDb->nextSequenceId('pharmacy_dictionary_id_seq');
        $query = sprintf("
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (%d, 4, 'Medication error(s) NOT corrected or addressed');
        ", $id);
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $id = $pDb->nextSequenceId('pharmacy_dictionary_id_seq');
        $query = sprintf("
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (%d, 23, 'Adherence issue(s) resolved');
        ", $id);
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $id = $pDb->nextSequenceId('pharmacy_dictionary_id_seq');
        $query = sprintf("
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (%d, 23, 'Adherence issue(s) NOT resolved');
        ", $id);
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    return true;
}