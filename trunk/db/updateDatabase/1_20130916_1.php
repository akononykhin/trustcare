<?php
$g_majorDb = 1;
$g_minorDb = 20130916;
$g_buildDb = 1;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130916_1(Zend_Db_Adapter_Abstract $db) {


    try {
        $query = sprintf("
delete from nafdac;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
alter table nafdac add column `id_user` int NOT NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
alter table nafdac
    add constraint fk_nafdac_id_user foreign key (id_user)
        references user(id);
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
alter table nafdac add column `id_patient` int NOT NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
alter table nafdac
    add constraint fk_nafdac_id_patient foreign key (id_patient)
        references patient(id);
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
alter table nafdac add column `id_pharmacy` int NOT NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
alter table nafdac
    add constraint fk_nafdac_id_pharmacy foreign key (id_pharmacy)
        references pharmacy(id);
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }
    

    try {
        $query = sprintf("
alter table nafdac add column `adr_start_date` datetime default NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
alter table nafdac add column `adr_stop_date` datetime default NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    return true;
}