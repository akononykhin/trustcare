<?php
$g_majorDb = 1;
$g_minorDb = 20130926;
$g_buildDb = 2;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130926_2(Zend_Db_Adapter_Abstract $db) {


    try {
        $query = sprintf("
update pharmacy_dictionary_type set name = 'Referred Source List' where id=16;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
insert into pharmacy_dictionary_type(id,ordernum,name) values (24, 24, 'Referred in List');
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (460, 24, 'OVC identification and referral to CBO for enrollment');
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (461, 24, 'Adherence counseling');
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (462, 24, 'Psychosocial support');
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (463, 24, 'Nutritional support & counseling');
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (464, 24, 'Distribution of SBC materials');
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }




    try {
        $query = sprintf("
update pharmacy_dictionary set name='Tuberculosis services' where id=323;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }
    try {
        $query = sprintf("
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (328, 17, 'Post Exposure Prophylaxis (PEP)');
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    return true;
}