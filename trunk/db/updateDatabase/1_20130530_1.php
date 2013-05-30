<?php
$g_majorDb = 1;
$g_minorDb = 20130530;
$g_buildDb = 1;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130530_1(Zend_Db_Adapter_Abstract $db) {


    try {
        $query = sprintf("
alter table frm_care add column `is_med_error_intervention_provided` int;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
CREATE TABLE frm_care_med_error_intervention (
  `id` int NOT NULL,
  `id_frm_care` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_care_med_error_intervention_1` (`id_frm_care`, `id_pharmacy_dictionary`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
alter table frm_care_med_error_intervention
    add constraint fk_frm_care_med_error_intervention_id_frm_care foreign key (id_frm_care)
        references frm_care(id) on delete cascade;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
alter table frm_care_med_error_intervention
    add constraint fk_frm_care_med_error_intervention_id_pharmacy_dictionary foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
INSERT INTO db_sequence(name,value) VALUES ('frm_care_med_error_intervention_id_seq', 1);
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    return true;
}