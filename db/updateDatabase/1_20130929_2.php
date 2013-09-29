<?php
$g_majorDb = 1;
$g_minorDb = 20130929;
$g_buildDb = 2;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130929_2(Zend_Db_Adapter_Abstract $db)
{

    try {
        $query = sprintf("
insert into pharmacy_dictionary_type(id,ordernum,name) values (26,  26,  'Types of Community ADR intervention');
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (500, 26, 'Referred to prescriber / other HCWs/facility for ADR management');
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (501, 26, 'Patient counseled on how to manage ADR');
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (502, 26, 'Drug therapy initiated/ changed');
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    return true;
}