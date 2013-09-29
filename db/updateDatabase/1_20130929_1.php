<?php
$g_majorDb = 1;
$g_minorDb = 20130929;
$g_buildDb = 1;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130929_1(Zend_Db_Adapter_Abstract $db)
{

    try {
        $query = sprintf("
alter table frm_community add column `is_adr_screened` int;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        if('42S21' != $ex->getCode()) {
            return false;
        }
    }

    try {
        $query = sprintf("
alter table frm_community add column `is_adr_symptoms` int;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        if('42S21' != $ex->getCode()) {
            return false;
        }
    }

    try {
        $query = sprintf("
alter table frm_community add column `adr_start_date` datetime default NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        if('42S21' != $ex->getCode()) {
            return false;
        }
    }

    try {
        $query = sprintf("
alter table frm_community add column `adr_stop_date` datetime default NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        if('42S21' != $ex->getCode()) {
            return false;
        }
    }

    try {
        $query = sprintf("
alter table frm_community add column `is_adr_intervention_provided` int;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        if('42S21' != $ex->getCode()) {
            return false;
        }
    }

    try {
        $query = sprintf("
CREATE TABLE frm_community_adr_intervention (
  `id` int NOT NULL,
  `id_frm_community` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_community_adr_intervention_1` (`id_frm_community`, `id_pharmacy_dictionary`),
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
alter table frm_community_adr_intervention
    add constraint fk_frm_community_adr_intervention_id_frm_community foreign key (id_frm_community)
        references frm_community(id) on delete cascade;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
alter table frm_community_adr_intervention
    add constraint fk_frm_community_adr_intervention_id_pharmacy_dictionary foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
INSERT INTO db_sequence(name,value) VALUES ('frm_community_adr_intervention_id_seq', 1);
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    return true;
}