<?php
$g_majorDb = 1;
$g_minorDb = 20121031;
$g_buildDb = 1;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20121031_1(Zend_Db_Adapter_Abstract $db) {

    try {
        $query = sprintf("
alter table report_care add column `id_user` int default NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
alter table report_care
    add constraint fk_report_care_id_user foreign key (id_user)
        references user(id) on delete set NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
alter table report_community add column `id_user` int default NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
alter table report_community
    add constraint fk_report_community_id_user foreign key (id_user)
        references user(id) on delete set NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }
    return true;
}