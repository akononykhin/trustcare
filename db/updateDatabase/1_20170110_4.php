<?php
$g_majorDb = 1;
$g_minorDb = 20170110;
$g_buildDb = 4;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20170110_4(Zend_Db_Adapter_Abstract $db) {

    try {
        $query = sprintf("
insert into nafdac_drug (`id`,`id_nafdac`,`name`,`dosage`,`batch`,`started`,`stopped`,`reason`) select id,id,CONCAT_WS(' ', drug_brand_name, drug_generic_name, drug_nafdac_number),drug_dosage,concat_ws(' ', drug_manufactor, drug_batch_number),drug_date_started,drug_date_stopped,drug_indication_for_use from nafdac;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
update db_sequence set value=(select ifnull(max(id),0)+1 from nafdac_drug) where name='nafdac_drug_id_seq';
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    return true;
}