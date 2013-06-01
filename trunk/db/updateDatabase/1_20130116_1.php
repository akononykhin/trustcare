<?php
$g_majorDb = 1;
$g_minorDb = 20130116;
$g_buildDb = 1;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130116_1(Zend_Db_Adapter_Abstract $db) {


    try {
        $query = sprintf("
CREATE TABLE facility_type (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
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
INSERT INTO db_sequence(name,value) VALUES ('facility_type_id_seq', 1);
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
CREATE TABLE facility_level (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
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
INSERT INTO db_sequence(name,value) VALUES ('facility_level_id_seq', 1);
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
alter table lga add column `id_state` int default NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
alter table lga
    add constraint fk_lga_id_state foreign key (id_state)
        references state(id) on delete set NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
alter table facility add column `id_lga` int default NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
alter table facility
    add constraint fk_facility_id_lga foreign key (id_lga)
        references lga(id) on delete set NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }



    try {
        $query = sprintf("
alter table facility add column `id_facility_type` int default NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
alter table facility
    add constraint fk_facility_id_facility_type foreign key (id_facility_type)
        references facility_type(id) on delete set NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
alter table facility add column `id_facility_level` int default NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
alter table facility
    add constraint fk_facility_id_facility_level foreign key (id_facility_level)
        references facility_level(id) on delete set NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
alter table facility add column `sn` varchar(128) default NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    return true;
}