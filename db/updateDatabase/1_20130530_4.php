<?php
$g_majorDb = 1;
$g_minorDb = 20130530;
$g_buildDb = 4;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130530_4(Zend_Db_Adapter_Abstract $db) {


    try {
        $query = sprintf("
CREATE TABLE frm_care_med_error_intervention_outcome (
  `id` int NOT NULL,
  `id_frm_care` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_care_med_error_intervention_outcome_1` (`id_frm_care`, `id_pharmacy_dictionary`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        if('42S01' != $ex->getCode()) {
            return false;
        }
    }

    try {
        $query = sprintf("
alter table frm_care_med_error_intervention_outcome
    add constraint fk_frm_care_med_error_intervention_outcome_id_frm_care foreign key (id_frm_care)
        references frm_care(id) on delete cascade;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
alter table frm_care_med_error_intervention_outcome
    add constraint fk_frm_care_med_error_intervention_outcome_2 foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
INSERT INTO db_sequence(name,value) VALUES ('frm_care_med_error_intervention_outcome_id_seq', 1);
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    return true;
}