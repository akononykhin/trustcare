<?php
$g_majorDb = 1;
$g_minorDb = 20121030;
$g_buildDb = 2;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20121030_2(Zend_Db_Adapter_Abstract $db) {

    try {
        $query = sprintf("
alter table frm_community
    add constraint fk_frm_community_id_pharmacy foreign key (id_pharmacy)
        references pharmacy(id) on delete cascade;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    return true;
}