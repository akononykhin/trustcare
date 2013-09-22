<?php
$g_majorDb = 1;
$g_minorDb = 20130922;
$g_buildDb = 1;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130922_1(Zend_Db_Adapter_Abstract $db) {


    try {
        $query = sprintf("
alter table frm_care add column `is_commited` bool default false;
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
alter table frm_care add column `id_nafdac` int default NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
alter table frm_care
    add constraint fk_frm_care_id_nafdac foreign key (id_nafdac)
        references nafdac(id) on delete set NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }



    return true;
}