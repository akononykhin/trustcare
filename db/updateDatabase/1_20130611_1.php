<?php
$g_majorDb = 1;
$g_minorDb = 20130611;
$g_buildDb = 1;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130611_1(Zend_Db_Adapter_Abstract $db) {


    try {
        $query = sprintf("
alter table frm_care add   UNIQUE KEY `cons_frm_care_1` (`id_patient`, `date_of_visit`);
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    

    return true;
}