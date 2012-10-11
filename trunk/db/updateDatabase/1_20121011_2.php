<?php
$g_majorDb = 1;
$g_minorDb = 20121011;
$g_buildDb = 2;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20121011_2(Zend_Db_Adapter_Abstract $db) {

    try {
        $query = sprintf("
create index idx_frm_care_date_of_visit_month_index on frm_care(date_of_visit_month_index);
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
alter table frm_care add column `is_patient_younger_15` int default NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
alter table frm_care add column `is_patient_male` int default NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
create index idx_frm_community_date_of_visit_month_index on frm_community(date_of_visit_month_index);
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
alter table frm_community add column `is_patient_younger_15` int default NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
alter table frm_community add column `is_patient_male` int default NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    return true;
}