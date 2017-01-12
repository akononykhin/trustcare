<?php
$g_majorDb = 1;
$g_minorDb = 20170110;
$g_buildDb = 3;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20170110_3(Zend_Db_Adapter_Abstract $db) {

    try {
        $query = sprintf("
CREATE TABLE nafdac_drug (
  `id` int NOT NULL,
  `id_nafdac` int NOT NULL,
  `name` varchar(255) default NULL,
  `dosage` varchar(255) default NULL,
  `batch` varchar(255) default NULL,
  `started` varchar(255) default NULL,
  `stopped` varchar(255) default NULL,
  `reason` varchar(255) default NULL,
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
alter table nafdac_drug
    add constraint fk_nafdac_drug_1 foreign key (id_nafdac)
        references nafdac(id) on delete cascade;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
INSERT INTO db_sequence(name,value) VALUES ('nafdac_drug_id_seq', 1);
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    return true;
}