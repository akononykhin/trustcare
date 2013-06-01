<?php
$g_majorDb = 1;
$g_minorDb = 20121030;
$g_buildDb = 3;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20121030_3(Zend_Db_Adapter_Abstract $db) {


    try {
        $query = sprintf("
alter table frm_care add column `id_pharmacy` int default NULL;
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