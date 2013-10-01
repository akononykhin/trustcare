<?php
$g_majorDb = 1;
$g_minorDb = 20131001;
$g_buildDb = 1;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20131001_1(Zend_Db_Adapter_Abstract $db)
{

    try {
        $query = sprintf("
CREATE TABLE report_community_services (
  `id` int NOT NULL,
  `generation_date` datetime NOT NULL,
  `period` int,
  `id_user` int default NULL,
  `id_pharmacy` int NOT NULL,
  `filename` varchar(255),
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
alter table report_community_services
    add constraint fk_report_community_services_1 foreign key (id_pharmacy)
        references pharmacy(id) on delete cascade;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
alter table report_community_services
    add constraint fk_report_community_services_2 foreign key (id_user)
        references user(id) on delete set NULL;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    try {
        $query = sprintf("
INSERT INTO db_sequence(name,value) VALUES ('report_community_services_id_seq', 1);
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    return true;
}