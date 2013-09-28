<?php
$g_majorDb = 1;
$g_minorDb = 20130928;
$g_buildDb = 2;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130928_2(Zend_Db_Adapter_Abstract $db) {


    try {
        $query = sprintf("
CREATE TABLE frm_community_referred_from (
  `id` int NOT NULL,
  `id_frm_community` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_community_referred_from_1` (`id_frm_community`, `id_pharmacy_dictionary`),
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
alter table frm_community_referred_from
    add constraint fk_frm_community_referred_from_id_frm_community foreign key (id_frm_community)
        references frm_community(id) on delete cascade;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
alter table frm_community_referred_from
    add constraint fk_frm_community_referred_from_id_pharmacy_dictionary foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
INSERT INTO db_sequence(name,value) VALUES ('frm_community_referred_from_id_seq', 1);
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    return true;
}