<?php
$g_majorDb = 1;
$g_minorDb = 20170202;
$g_buildDb = 1;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20170202_1(Zend_Db_Adapter_Abstract $db) {

    try {
        $query = sprintf("
alter table nafdac_drug add column `generic_name` varchar(255) DEFAULT NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
alter table nafdac_drug add column `nafdac_number` varchar(255) DEFAULT NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
alter table nafdac_drug add column `expiry_date` varchar(32) DEFAULT NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
alter table nafdac_drug add column `manufactor` varchar(255) DEFAULT NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
alter table nafdac_drug add column `route_of_administration` varchar(255) DEFAULT NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    return true;
}