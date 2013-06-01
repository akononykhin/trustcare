<?php
$g_majorDb = 1;
$g_minorDb = 20130128;
$g_buildDb = 1;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130128_1(Zend_Db_Adapter_Abstract $db) {


    try {
        $query = sprintf("
alter table nafdac add column `was_admitted` bool default false;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
alter table nafdac add column `was_hospitalization_prolonged` bool default false;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }



    try {
        $query = sprintf("
alter table nafdac add column `treatment_of_reaction` varchar(255) default NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    return true;
}