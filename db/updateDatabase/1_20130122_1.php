<?php
$g_majorDb = 1;
$g_minorDb = 20130122;
$g_buildDb = 1;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130122_1(Zend_Db_Adapter_Abstract $db) {



    try {
        $query = sprintf("
alter table state change column `name` `name` varchar(255) NOT NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
alter table state add UNIQUE KEY `cons_state_name` (`name`)
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        if('42000' != $ex->getCode()) {
            return false;
        }
        return false;
    }

    try {
        $query = sprintf("
alter table lga change column `name` `name` varchar(255) NOT NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
alter table lga add UNIQUE KEY `cons_lga_name` (`name`)
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
alter table facility_type add UNIQUE KEY `cons_facility_type_name` (`name`)
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
alter table facility_level add UNIQUE KEY `cons_facility_level_name` (`name`)
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }
    return true;
}