<?php
$g_majorDb = 1;
$g_minorDb = 20121016;
$g_buildDb = 1;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20121016_1(Zend_Db_Adapter_Abstract $db) {

    try {
        $query = sprintf("
alter table frm_care add column `adr_severity_id` int default NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        if('42S21' != $ex->getCode()) {
            return false;
        }
    }

    try {
        $query = sprintf("
alter table frm_care
    add constraint fk_frm_care_adr_severity_id foreign key (adr_severity_id)
        references pharmacy_dictionary(id) on delete set NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
drop table frm_care_adr_severity;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
delete from db_sequence where name='frm_care_adr_severity_id_seq';
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    return true;
}