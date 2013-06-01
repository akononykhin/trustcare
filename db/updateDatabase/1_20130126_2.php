<?php
$g_majorDb = 1;
$g_minorDb = 20130126;
$g_buildDb = 2;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130126_2(Zend_Db_Adapter_Abstract $db) {


    try {
        $query = sprintf("
alter table nafdac
    add constraint fk_nafdac_id_frm_care foreign key (id_frm_care)
        references frm_care(id) on delete cascade;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
alter table nafdac_medicine
    add constraint fk_nafdac_medicine_id_nafdac foreign key (id_nafdac)
        references nafdac(id) on delete cascade;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    return true;
}