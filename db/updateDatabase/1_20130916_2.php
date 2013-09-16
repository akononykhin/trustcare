<?php
$g_majorDb = 1;
$g_minorDb = 20130916;
$g_buildDb = 2;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130916_2(Zend_Db_Adapter_Abstract $db) {


    try {
        $query = sprintf("
alter table nafdac drop foreign key fk_nafdac_id_frm_care;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        if('HY000' != $ex->getCode()) {
            return false;
        }
    }

    try {
        $query = sprintf("
alter table nafdac drop column `id_frm_care`;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    return true;
}