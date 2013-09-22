<?php
$g_majorDb = 1;
$g_minorDb = 20130922;
$g_buildDb = 4;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130922_4(Zend_Db_Adapter_Abstract $db) {


    try {
        $query = sprintf("
alter table frm_care change column `generation_date` `generation_date` datetime default NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
alter table nafdac change column `generation_date` `generation_date` datetime default NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
update frm_care set generation_date=NULL where generation_date='0000-00-00 00:00:00';
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    return true;
}