<?php
$g_majorDb = 1;
$g_minorDb = 20121011;
$g_buildDb = 1;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20121011_1(Zend_Db_Adapter_Abstract $db) {

    try {
        $query = sprintf("
alter table frm_care add column `date_of_visit_month_index` int default NULL;
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
alter table frm_community add column `date_of_visit_month_index` int default NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    return true;
}