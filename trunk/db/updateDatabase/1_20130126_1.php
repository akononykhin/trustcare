<?php
$g_majorDb = 1;
$g_minorDb = 20130126;
$g_buildDb = 1;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130126_1(Zend_Db_Adapter_Abstract $db) {


    try {
        $query = sprintf("
CREATE TABLE nafdac (
  `id` int NOT NULL,
  `id_frm_care` int NOT NULL,
  `filename` varchar(255),
  `adr_description` text,
  `outcome_of_reaction_type` int,
  `outcome_of_reaction_desc` varchar(255) default NULL,
  `drug_brand_name` varchar(255) default NULL,
  `drug_generic_name` varchar(255) default NULL,
  `drug_batch_number` varchar(255) default NULL,
  `drug_nafdac_number` varchar(255) default NULL,
  `drug_expiry_name` varchar(255) default NULL,
  `drug_manufactor` varchar(255) default NULL,
  `drug_indication_for_use` varchar(255) default NULL,
  `drug_dosage` varchar(255) default NULL,
  `drug_route_of_administration` varchar(255) default NULL,
  `drug_date_started` varchar(255) default NULL,
  `drug_date_stopped` varchar(255) default NULL,
  `reporter_name` varchar(255) default NULL,
  `reporter_address` varchar(255) default NULL,
  `reporter_profession` varchar(255) default NULL,
  `reporter_contact` varchar(255) default NULL,
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
INSERT INTO db_sequence(name,value) VALUES ('nafdac_id_seq', 1);
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
CREATE TABLE nafdac_medicine (
  `id` int NOT NULL,
  `id_nafdac` int NOT NULL,
  `name` varchar(255) default NULL,
  `dosage` varchar(255) default NULL,
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
INSERT INTO db_sequence(name,value) VALUES ('nafdac_medicine_id_seq', 1);
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    return true;
}