<?php
$g_majorDb = 1;
$g_minorDb = 20130529;
$g_buildDb = 2;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130529_2(Zend_Db_Adapter_Abstract $db) {

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
delete from pharmacy_dictionary where id_pharmacy_dictionary_type=3;
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
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (%d, 3, 'Prescriber or other health worker contacted to clarify error');
        ", $id);
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $id = $pDb->nextSequenceId('pharmacy_dictionary_id_seq');
        $query = sprintf("
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (%d, 3, 'Refer patient to prescriber or other health worker to clarify error');
        ", $id);
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $id = $pDb->nextSequenceId('pharmacy_dictionary_id_seq');
        $query = sprintf("
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (%d, 3, 'Drug therapy initiated/changed');
        ", $id);
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $id = $pDb->nextSequenceId('pharmacy_dictionary_id_seq');
        $query = sprintf("
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (%d, 3, 'Did not dispense medication');
        ", $id);
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $id = $pDb->nextSequenceId('pharmacy_dictionary_id_seq');
        $query = sprintf("
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (%d, 22, 'Refer to adherence counselor for assessment and counseling');
        ", $id);
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $id = $pDb->nextSequenceId('pharmacy_dictionary_id_seq');
        $query = sprintf("
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (%d, 22, 'Patient counseling and education provided');
        ", $id);
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $id = $pDb->nextSequenceId('pharmacy_dictionary_id_seq');
        $query = sprintf("
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (%d, 22, 'Did not dispense medication');
        ", $id);
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    return true;
}