<?php
$g_majorDb = 1;
$g_minorDb = 20130922;
$g_buildDb = 2;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130922_2(Zend_Db_Adapter_Abstract $db) {


    try {
        $query = sprintf("
alter table frm_care drop foreign key fk_frm_care_id_patient;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
alter table frm_care drop foreign key fk_frm_care_id_pharmacy;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
alter table frm_care drop key cons_frm_care_1;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
alter table frm_care
    add constraint fk_frm_care_id_patient foreign key (id_patient)
        references patient(id);
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
alter table frm_care
    add constraint fk_frm_care_id_pharmacy foreign key (id_pharmacy)
        references pharmacy(id) on delete cascade;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    return true;
}