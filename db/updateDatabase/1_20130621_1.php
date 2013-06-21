<?php
$g_majorDb = 1;
$g_minorDb = 20130621;
$g_buildDb = 1;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130621_1(Zend_Db_Adapter_Abstract $db) {


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
alter table frm_care drop index `cons_frm_care_1`;
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
alter table frm_care add   UNIQUE KEY `cons_frm_care_1` (`id_pharmacy`, `id_patient`, `date_of_visit`);
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    

    return true;
}