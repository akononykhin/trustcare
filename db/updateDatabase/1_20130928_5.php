<?php
$g_majorDb = 1;
$g_minorDb = 20130928;
$g_buildDb = 5;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130928_5(Zend_Db_Adapter_Abstract $db) {


    try {
        $query = sprintf("
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (385, 20, 'Counseling on safe sex practices');
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (386, 20, 'Health education & promotion');
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (424, 22, 'INH Preventive Therapy (IPT)');
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    return true;
}