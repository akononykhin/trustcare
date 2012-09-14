
CREATE TABLE `db_version` (
  `major` int NOT NULL,
  `minor` int NOT NULL,
  `build` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `db_sequence` (
  `name` varchar(255) NOT NULL default '',
  `value` int NOT NULL default '0',
  PRIMARY KEY  (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE log_access (
  `id` bigint(20) NOT NULL auto_increment,
  `author` varchar(32) NOT NULL,
  `time` datetime NOT NULL,
  `ip` varchar(64) default NULL,
  `action` text default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE log_objects (
    `id` bigint(20) NOT NULL auto_increment,
    `timestamp` datetime NOT NULL,
    `author` varchar(128),
    `from_ip` varchar(128),
    `stack` text,
    `action` text,
    `object_name` varchar(128),
    `key_info` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE log4php (
    `id` bigint(20) NOT NULL,
    `timestamp` datetime NOT NULL,
    `logger` varchar(128),
    `level` varchar(32),
    `message` text,
    `thread` varchar(32),
    `file` varchar(255),
    `line` varchar(4),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



/************* START System Dictionaries **************************************************/

CREATE TABLE country (
  `id` int NOT NULL,
  `iso_3166` varchar(2) default NULL,
  `name` text NOT NULL,
  UNIQUE KEY `cons_country_iso_3166` (`iso_3166`),
  UNIQUE KEY `cons_country_name` (`name`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE state (
  `id` int NOT NULL,
  `name` text NOT NULL,
  `id_country` int default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE facility (
  `id` int NOT NULL,
  `name` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE lga (
  `id` int NOT NULL,
  `name` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/************* END System Dictionaries **************************************************/



CREATE TABLE pharmacy (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text default NULL,
  `id_lga` int default NULL,
  `id_country` int default NULL,
  `id_state` int default NULL,
  `id_facility` int default NULL,
  UNIQUE KEY `cons_pharmacy_name` (`name`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE user (
  `id` int NOT NULL,
  `login` varchar(32) NOT NULL,
  `password` varchar(64) default NULL,
  `first_name` varchar(256) default NULL,
  `last_name` varchar(256) default NULL,
  `role` varchar(16),
  `city` varchar(256) default NULL,
  `address` text default NULL,
  `zip` varchar(256) default NULL,
  `phone` varchar(256) default NULL,
  `id_pharmacy` int default NULL,
  `id_country` int default NULL,
  `id_state` int default NULL,
  `is_active` bool default 1,
  UNIQUE KEY `cons_user_login` (`login`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE physician (
  `id` int NOT NULL,
  `identifier` varchar(255) NOT NULL,
  `first_name` varchar(256) default NULL,
  `last_name` varchar(256) default NULL,
  `address` text default NULL,
  `id_country` int default NULL,
  `id_state` int default NULL,
  `id_lga` int default NULL,
  `id_facility` int default NULL,
  UNIQUE KEY `cons_physician_identifier` (`identifier`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE patient (
  `id` int NOT NULL,
  `identifier` varchar(255) NOT NULL,
  `first_name` varchar(256) default NULL,
  `last_name` varchar(256) default NULL,
  `id_country` int default NULL,
  `id_state` int default NULL,
  `city` varchar(256) default NULL,
  `address` text default NULL,
  `zip` varchar(256) default NULL,
  `phone` varchar(256) default NULL,
  `birthdate` datetime default NULL,
  `is_male` tinyint,
  `id_physician` int default NULL,
  UNIQUE KEY `cons_patient_identifier` (`identifier`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE form_pharm_care (
  `id` int NOT NULL,
  `id_patient` int NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE form_pharm_community (
  `id` int NOT NULL,
  `id_patient` int NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE report_pharm_care (
  `id` int NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE report_pharm_community (
  `id` int NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




alter table state
    add constraint fk_state_id_country foreign key (id_country)
        references country(id) on delete cascade;


alter table pharmacy
    add constraint fk_pharmacy_id_lga foreign key (id_lga)
        references lga(id) on delete set NULL;

alter table pharmacy
    add constraint fk_pharmacy_id_country foreign key (id_country)
        references country(id) on delete set NULL;

alter table pharmacy
    add constraint fk_pharmacy_id_state foreign key (id_state)
        references state(id) on delete set NULL;


alter table pharmacy
    add constraint fk_pharmacy_id_facility foreign key (id_facility)
        references facility(id) on delete set NULL;


alter table user
    add constraint fk_user_id_country foreign key (id_country)
        references country(id) on delete set NULL;


alter table user
    add constraint fk_user_id_state foreign key (id_state)
        references state(id) on delete set NULL;

alter table user
    add constraint fk_user_id_pharmacy foreign key (id_pharmacy)
        references pharmacy(id) on delete cascade;

alter table physician
    add constraint fk_physician_id_country foreign key (id_country)
        references country(id) on delete set NULL;

alter table physician
    add constraint fk_physician_id_state foreign key (id_state)
        references state(id) on delete set NULL;

alter table physician
    add constraint fk_physician_id_lga foreign key (id_lga)
        references lga(id) on delete set NULL;

alter table physician
    add constraint fk_physician_id_facility foreign key (id_facility)
        references facility(id) on delete set NULL;

alter table patient
    add constraint fk_patient_id_country foreign key (id_country)
        references country(id) on delete set NULL;

alter table patient
    add constraint fk_patient_id_state foreign key (id_state)
        references state(id) on delete set NULL;

alter table patient
    add constraint fk_patient_id_physician foreign key (id_physician)
        references physician(id) on delete set NULL;



alter table form_pharm_care
    add constraint fk_form_pharm_care_id_patient foreign key (id_patient)
        references patient(id) on delete set NULL;


alter table form_pharm_community
    add constraint fk_form_pharm_community_id_patient foreign key (id_patient)
        references patient(id) on delete set NULL;






insert into admin(id, login, password, is_active,role) values (1, 'admin', MD5('admin'), 1, 'pharmacy_manager');

INSERT INTO db_sequence(name,value) VALUES ('log4php_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('pharmacy_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('user_id_seq', 10);
INSERT INTO db_sequence(name,value) VALUES ('physician_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('patient_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('country_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('state_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('facility_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('lga_id_seq', 1);

INSERT INTO db_sequence(name,value) VALUES ('form_pharm_care_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('form_pharm_community_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('report_pharm_care_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('report_pharm_community_id_seq', 1);


insert into db_version values (1, 20120914, 1);
