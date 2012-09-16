
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


/************* START Pharmacy Dictionaries **************************************************/


CREATE TABLE pharmacy_dictionary_type (
  `id` int NOT NULL,
  `ordernum` int NOT NLL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into pharmacy_dictionary_type(id,ordernum,name) values (1,  1,  'Types of Medication Error');
insert into pharmacy_dictionary_type(id,ordernum,name) values (2,  2,  'Types of Medication Adherence Related problems');
insert into pharmacy_dictionary_type(id,ordernum,name) values (3,  3,  'Types of Medication Error / Adherence intervention provided');
insert into pharmacy_dictionary_type(id,ordernum,name) values (4,  4,  'Type of Medication Error / Adherence intervention Outcome');
insert into pharmacy_dictionary_type(id,ordernum,name) values (5,  5,  'Types of ADR severity grade');
insert into pharmacy_dictionary_type(id,ordernum,name) values (6,  6,  'Types of ADR intervention');
insert into pharmacy_dictionary_type(id,ordernum,name) values (7,  7,  'GIT/Hepatic System Options');
insert into pharmacy_dictionary_type(id,ordernum,name) values (8,  8,  'Nervious System Options');
insert into pharmacy_dictionary_type(id,ordernum,name) values (9,  9,  'Cardiovascular System Options');
insert into pharmacy_dictionary_type(id,ordernum,name) values (10, 10, 'Skin and Appendages Options');
insert into pharmacy_dictionary_type(id,ordernum,name) values (11, 11, 'Metabolic/Endocrine System Options');
insert into pharmacy_dictionary_type(id,ordernum,name) values (12, 12, 'Musculoskeletal');
insert into pharmacy_dictionary_type(id,ordernum,name) values (13, 13, 'Systemic-General Options');
insert into pharmacy_dictionary_type(id,ordernum,name) values (14, 14, '\‘Referred in\’ List');
insert into pharmacy_dictionary_type(id,ordernum,name) values (15, 15, '\‘Referred out\’ List');
insert into pharmacy_dictionary_type(id,ordernum,name) values (16, 16, 'Type of HIV testing results');
insert into pharmacy_dictionary_type(id,ordernum,name) values (17, 17, 'Palliative Care Services');
insert into pharmacy_dictionary_type(id,ordernum,name) values (18, 18, 'Reproductive Health services');
insert into pharmacy_dictionary_type(id,ordernum,name) values (19, 19, 'STI Services');
insert into pharmacy_dictionary_type(id,ordernum,name) values (20, 20, 'Tuberculosis services');
insert into pharmacy_dictionary_type(id,ordernum,name) values (21, 21, 'OVC Care and Support services');

CREATE TABLE pharmacy_dictionary (
  `id` int NOT NULL,
  `id_pharmacy_dictionary_type` int NOT NULL,
  `name` text NOT NULL,
  UNIQUE KEY `cons_pharmacy_dictionary_name` (`name`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (1,  1, 'ART-ineligible client commencing ART');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (2,  1, 'Duration and/or frequency of medication inappropriate');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (3,  1, 'Incorrect dose (Low dose or high dose) prescribed');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (4,  1, 'Incorrect ARV drugs combinations/regiments prescribed');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (5,  1, 'No drug for the medical problem');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (6,  1, 'No valid indication for the drug');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (7,  1, 'Possible Drug-Drug interaction or contraindication present');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (8,  1, 'Prescription order with incomplete prescriber/client details including date');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (9,  2, 'Client’s Adherence Counseling not done or completed (new clients)');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (10, 2, 'Non-adherence to therapy identified (Refill Clients)');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (11, 3, 'Prescriber or other HCWs contacted to clarify error/provide drug information');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (12, 3, 'Drug therapy initiated/changed');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (13, 3, 'Did not dispense medication');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (14, 3, 'Patient Counseling and education provided');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (15, 4, 'Medication Error/Adherence issues addressed');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (16, 4, 'Medication Error/Adherence issues NOT addressed');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (17, 5, 'Mild');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (18, 5, 'Noderate');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (19, 5, 'Severe');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (20, 5, 'Life-threatening');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (21, 6, 'Referred to prescriber / other HCWs/facility for ADR management');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (22, 6, 'Patient hospitalized for ADR management');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (23, 6, 'Drug therapy initiated/changed');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (24, 6, 'Patient counseled on how to manage ADR');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (25, 7, 'Nausea/Vomiting');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (26, 7, 'Abdominal pain');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (27, 7, 'Diarrhoea');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (28, 7, 'Dyspepsia');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (29, 7, 'Jaundice');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (30, 8, 'Anorexia (lose of appetite)');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (31, 8, 'Depression');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (32, 8, 'Dizziness');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (33, 8, 'Dry mouth');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (34, 8, 'Headache');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (35, 8, 'Insomnia');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (36, 8, 'Nightmares');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (37, 8, 'Pain, tingling or numbness in hands or feet');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (38, 8, 'Visual disturbances (blured vision etc.)');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (33, 9, 'Chest pain / Chest discomfort');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (34, 9, 'Dyspnoea / Shortness of breath');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (35, 9, 'Oedema');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (36, 9, 'Palpitation');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (37, 10, 'Pluritus (Itching)');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (38, 10, 'Skin Rash');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (39, 10, 'Steven-Johnson Syndrome');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (40, 10, 'Hyperpigmentation');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (41, 11, 'Dysmenorrhea');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (42, 11, 'Excessive thirst (Polydipsia)');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (43, 11, 'Lipodystrophy');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (44, 11, 'Polyuria (Increased micturition)');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (45, 12, 'Arthralgia');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (46, 12, 'Myopathy');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (47, 12, 'Muscle Pain (Myalgia)');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (48, 13, 'Anaemia');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (49, 13, 'Fatigue/weakness');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (40, 13, 'Malaise');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (41, 14, 'Secondary/Tertiary institution');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (42, 14, 'PHC');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (43, 14, 'TBA');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (44, 14, 'PMV');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (45, 15, 'HCT');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (46, 15, 'ART');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (47, 15, 'PMTCT');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (48, 15, 'TB');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (49, 15, 'STI');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (50, 15, 'FP');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (51, 15, 'Support group');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (52, 15, 'OVC services');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (53, 16, 'Positive');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (54, 16, 'Negative');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (55, 16, 'Intermediate');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (56, 17, 'Adherence Counseling');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (57, 17, 'Ol management');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (58, 17, 'Psychosocial support');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (59, 17, 'Pain Management');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (60, 17, 'Nutritional Support and Counseling');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (61, 17, 'Malaria Prevention');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (62, 17, 'Malaria Treatment');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (63, 18, 'Condoms Provided');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (64, 18, 'Emergency Contraceptive Provided');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (65, 18, 'Injectable Contraceptive Provided');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (66, 18, 'Oral Contraceptives Provided');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (67, 18, 'RH/FP Counseling');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (68, 19, 'STI Screening');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (69, 19, 'STI Treatment');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (70, 19, 'STI Counseling');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (71, 20, 'TB Screening');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (72, 20, 'TB Adherence Support');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (73, 20, 'TB Drugs Refills');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (74, 20, 'DOTs/CTBC');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (75, 21, 'Enrollment');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (76, 21, 'Educational support');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (77, 21, 'Shelter');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (78, 21, 'Nutritional');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (79, 21, 'Legal support/Protection');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (80, 21, 'Health support');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (81, 21, 'Economic support (Skill acquisition)');


/************* END Pharmacy Dictionaries **************************************************/


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

alter table pharmacy_dictionary
    add constraint fk_pharmacy_dictionary_type_id_pharmacy_dictionary_type foreign key (id_pharmacy_dictionary_type)
        references pharmacy_dictionary_type(id) on delete cascade;





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
INSERT INTO db_sequence(name,value) VALUES ('pharmacy_dictionary_type_id_seq', 100);
INSERT INTO db_sequence(name,value) VALUES ('pharmacy_dictionary_id_seq', 1000);

INSERT INTO db_sequence(name,value) VALUES ('form_pharm_care_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('form_pharm_community_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('report_pharm_care_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('report_pharm_community_id_seq', 1);


insert into db_version values (1, 20120914, 1);
