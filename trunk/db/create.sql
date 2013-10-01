
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
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE state (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `id_country` int default NULL,
  UNIQUE KEY `cons_state_name` (`name`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE lga (
  `id` int NOT NULL,
  `id_state` int default NULL,
  `name` varchar(255) NOT NULL,
  UNIQUE KEY `cons_lga_name` (`name`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE facility_type (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  UNIQUE KEY `cons_facility_type_name` (`name`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE facility_level (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  UNIQUE KEY `cons_facility_level_name` (`name`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE facility (
  `id` int NOT NULL,
  `id_lga` int default NULL,
  `name` text NOT NULL,
  `id_facility_type` int default NULL,
  `id_facility_level` int default NULL,
  `sn` varchar(128) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/************* END System Dictionaries **************************************************/


/************* START Pharmacy Dictionaries **************************************************/


CREATE TABLE pharmacy_dictionary_type (
  `id` int NOT NULL,
  `ordernum` int NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into pharmacy_dictionary_type(id,ordernum,name) values (1,  1,  'Types of Medication Error');
insert into pharmacy_dictionary_type(id,ordernum,name) values (2,  2,  'Types of Medication Adherence Related problems');
insert into pharmacy_dictionary_type(id,ordernum,name) values (3,  3,  'Types of Medication Error Intervention provided');
insert into pharmacy_dictionary_type(id,ordernum,name) values (4,  4, 'Types of Adherence Intervention provided');
insert into pharmacy_dictionary_type(id,ordernum,name) values (5,  5,  'Type of Medication Error Intervention Outcome');
insert into pharmacy_dictionary_type(id,ordernum,name) values (6,  6,  'Type of Adherence Intervention Outcome');
insert into pharmacy_dictionary_type(id,ordernum,name) values (7,  7,  'Types of ADR severity grade');
insert into pharmacy_dictionary_type(id,ordernum,name) values (8,  8,  'Types of ADR intervention');
insert into pharmacy_dictionary_type(id,ordernum,name) values (9,  9,  'GIT/Hepatic System Options');
insert into pharmacy_dictionary_type(id,ordernum,name) values (10, 10,  'Nervious System Options');
insert into pharmacy_dictionary_type(id,ordernum,name) values (11, 11,  'Cardiovascular System Options');
insert into pharmacy_dictionary_type(id,ordernum,name) values (12, 12, 'Skin and Appendages Options');
insert into pharmacy_dictionary_type(id,ordernum,name) values (13, 13, 'Metabolic/Endocrine System Options');
insert into pharmacy_dictionary_type(id,ordernum,name) values (14, 14, 'Musculoskeletal');
insert into pharmacy_dictionary_type(id,ordernum,name) values (15, 15, 'Systemic-General Options');
insert into pharmacy_dictionary_type(id,ordernum,name) values (16, 16, 'Referred Source List');
insert into pharmacy_dictionary_type(id,ordernum,name) values (17, 17, 'Referred out List');
insert into pharmacy_dictionary_type(id,ordernum,name) values (18, 18, 'Type of HIV testing results');
insert into pharmacy_dictionary_type(id,ordernum,name) values (19, 19, 'Palliative Care Services');
insert into pharmacy_dictionary_type(id,ordernum,name) values (20, 20, 'Reproductive Health services');
insert into pharmacy_dictionary_type(id,ordernum,name) values (21, 21, 'STI Services');
insert into pharmacy_dictionary_type(id,ordernum,name) values (22, 22, 'Tuberculosis services');
insert into pharmacy_dictionary_type(id,ordernum,name) values (23, 23, 'OVC Care and Support services');
insert into pharmacy_dictionary_type(id,ordernum,name) values (24, 24, 'Referred in List');
insert into pharmacy_dictionary_type(id,ordernum,name) values (25, 25, 'Malaria Services');
insert into pharmacy_dictionary_type(id,ordernum,name) values (26, 26, 'Types of Community ADR intervention');

CREATE TABLE pharmacy_dictionary (
  `id` int NOT NULL,
  `id_pharmacy_dictionary_type` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_active` bool default 1,
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

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (20,  2, 'Client\'s Adherence Counseling not done or completed (new clients)');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (21, 2, 'Non-adherence to therapy identified (Refill Clients)');


insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (41, 3, 'Prescriber or other health worker contacted to clarify error');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (42, 3, 'Refer patient to prescriber or other health worker to clarify error');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (43, 3, 'Drug therapy initiated/changed');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (44, 3, 'Did not dispense medication');


insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (60, 4, 'Refer to adherence counselor for assessment and counseling');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (61, 4, 'Patient counseling and education provided');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (62, 4, 'Did not dispense medication');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (81, 5, 'Medication error(s) corrected or addressed');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (82, 5, 'Medication error(s) NOT corrected or addressed');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (101, 6, 'Adherence issue(s) resolved');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (102, 6, 'Adherence issue(s) NOT resolved');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (120, 7, 'Mild');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (121, 7, 'Noderate');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (122, 7, 'Severe');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (123, 7, 'Life-threatening');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (141, 8, 'Referred to prescriber / other HCWs/facility for ADR management');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (142, 8, 'Patient hospitalized for ADR management');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (143, 8, 'Drug therapy initiated/changed');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (144, 8, 'Patient counseled on how to manage ADR');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (160, 9, 'Nausea/Vomiting');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (161, 9, 'Abdominal pain');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (162, 9, 'Diarrhoea');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (163, 9, 'Dyspepsia');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (164, 9, 'Jaundice');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (180, 10, 'Anorexia (lose of appetite)');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (181, 10, 'Depression');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (182, 10, 'Dizziness');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (183, 10, 'Dry mouth');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (184, 10, 'Headache');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (185, 10, 'Insomnia');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (186, 10, 'Nightmares');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (187, 10, 'Pain, tingling or numbness in hands or feet');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (188, 10, 'Visual disturbances (blured vision etc.)');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (200, 11, 'Chest pain / Chest discomfort');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (201, 11, 'Dyspnoea / Shortness of breath');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (202, 11, 'Oedema');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (203, 11, 'Palpitation');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (220, 12, 'Pluritus (Itching)');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (221, 12, 'Skin Rash');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (222, 12, 'Steven-Johnson Syndrome');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (223, 12, 'Hyperpigmentation');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (240, 13, 'Dysmenorrhea');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (241, 13, 'Excessive thirst (Polydipsia)');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (242, 13, 'Lipodystrophy');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (243, 13, 'Polyuria (Increased micturition)');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (260, 14, 'Arthralgia');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (261, 14, 'Myopathy');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (262, 14, 'Muscle Pain (Myalgia)');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (280, 15, 'Anaemia');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (281, 15, 'Fatigue/weakness');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (282, 15, 'Malaise');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (300, 16, 'Secondary/Tertiary institution');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (301, 16, 'PHC');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (302, 16, 'TBA');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (303, 16, 'PMV');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (320, 17, 'HCT');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (321, 17, 'ART');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (322, 17, 'PMTCT');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (323, 17, 'Tuberculosis services');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (324, 17, 'STI');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (325, 17, 'FP');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (326, 17, 'Support group');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (327, 17, 'OVC services');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (328, 17, 'Post Exposure Prophylaxis (PEP)');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (340, 18, 'Positive');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (341, 18, 'Negative');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (342, 18, 'Intermediate');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (360, 19, 'Opportunistic Infections Screening & Management');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (361, 19, 'Ol management');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (363, 19, 'Pain Management');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (380, 20, 'Condoms Provided');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (381, 20, 'Emergency Contraceptive Provided');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (382, 20, 'Injectable Contraceptive Provided');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (383, 20, 'Oral Contraceptives Provided');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (384, 20, 'RH/FP Counseling');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (385, 20, 'Counseling on safe sex practices');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (386, 20, 'Health education & promotion');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (400, 21, 'STI Screening');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (401, 21, 'STIs Screening & Counselling');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (420, 22, 'TB Screening');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (421, 22, 'TB Adherence Support');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (422, 22, 'TB Drugs Refills');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (423, 22, 'DOTs/CTBC');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (424, 22, 'INH Preventive Therapy (IPT)');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (440, 23, 'Enrollment');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (441, 23, 'Educational support');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (442, 23, 'Shelter');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (443, 23, 'Nutritional');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (444, 23, 'Legal support/Protection');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (445, 23, 'Health support');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (446, 23, 'Economic support (Skill acquisition)');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (460, 24, 'OVC identification and referral to CBO for enrollment');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (461, 24, 'Adherence counseling');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (462, 24, 'Psychosocial support');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (463, 24, 'Nutritional support & counseling');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (464, 24, 'Distribution of SBC materials');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (480, 25, 'Malaria prevention (LLITN)');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (481, 25, 'Malaria prevention (IPT)');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (482, 25, 'Malaria Treatment');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (500, 26, 'Referred to prescriber / other HCWs/facility for ADR management');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (501, 26, 'Patient counseled on how to manage ADR');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (502, 26, 'Drug therapy initiated/ changed');

/************* END Pharmacy Dictionaries **************************************************/


CREATE TABLE pharmacy (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text default NULL,
  `id_lga` int default NULL,
  `id_country` int default NULL,
  `id_state` int default NULL,
  `id_facility` int default NULL,
  `is_active` int default 1,
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
  `id_pharmacy` int default NULL,
  `city` varchar(256) default NULL,
  `address` text default NULL,
  `zip` varchar(256) default NULL,
  `phone` varchar(256) default NULL,
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
  `is_active` int default 1,
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


/****************** START FRM_CARE tables ************************************************/
CREATE TABLE frm_care (
  `id` int NOT NULL,
  `generation_date` datetime default NULL,
  `id_user` int default NULL
  `date_of_visit` datetime NOT NULL,
  `date_of_visit_month_index` int default NULL,
  `is_commited` bool default false,
  `id_pharmacy` int default NULL,
  `id_patient` int NOT NULL,
  `is_pregnant` int default 0,
  `is_receive_prescription` int,
  `is_med_error_screened` int,
  `is_med_error_identified` int,
  `is_med_adh_problem_screened` int,
  `is_med_adh_problem_identified` int,
  `is_med_error_intervention_provided` int,
  `is_adh_intervention_provided` int,
  `is_adr_screened` int,
  `is_adr_symptoms` int,
  `adr_severity_id` int default NULL,
  `adr_start_date` datetime default NULL,
  `adr_stop_date` datetime default NULL,
  `is_adr_intervention_provided` int,
  `is_nafdac_adr_filled` int,
  `is_patient_younger_15` int default NULL,
  `is_patient_male` int default NULL,
  `id_nafdac` int default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create index idx_frm_care_date_of_visit_month_index on frm_care(date_of_visit_month_index);


CREATE TABLE frm_care_med_error_type (
  `id` int NOT NULL,
  `id_frm_care` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_care_med_error_type_1` (`id_frm_care`, `id_pharmacy_dictionary`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE frm_care_med_adh_problem (
  `id` int NOT NULL,
  `id_frm_care` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_care_med_adh_problem_1` (`id_frm_care`, `id_pharmacy_dictionary`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE frm_care_med_error_intervention (
  `id` int NOT NULL,
  `id_frm_care` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_care_med_error_intervention_1` (`id_frm_care`, `id_pharmacy_dictionary`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE frm_care_adh_intervention (
  `id` int NOT NULL,
  `id_frm_care` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_care_adh_intervention_1` (`id_frm_care`, `id_pharmacy_dictionary`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE frm_care_med_error_intervention_outcome (
  `id` int NOT NULL,
  `id_frm_care` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_care_med_error_intervention_outcome_1` (`id_frm_care`, `id_pharmacy_dictionary`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE frm_care_adh_intervention_outcome (
  `id` int NOT NULL,
  `id_frm_care` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_care_adh_intervention_outcome_1` (`id_frm_care`, `id_pharmacy_dictionary`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE frm_care_suspected_adr_hepatic (
  `id` int NOT NULL,
  `id_frm_care` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_care_suspected_adr_hepatic_1` (`id_frm_care`, `id_pharmacy_dictionary`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE frm_care_suspected_adr_nervous (
  `id` int NOT NULL,
  `id_frm_care` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_care_suspected_adr_nervous_1` (`id_frm_care`, `id_pharmacy_dictionary`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE frm_care_suspected_adr_cardiovascular (
  `id` int NOT NULL,
  `id_frm_care` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_care_suspected_adr_cardiovascular_1` (`id_frm_care`, `id_pharmacy_dictionary`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE frm_care_suspected_adr_skin (
  `id` int NOT NULL,
  `id_frm_care` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_care_suspected_adr_skin_1` (`id_frm_care`, `id_pharmacy_dictionary`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE frm_care_suspected_adr_metabolic (
  `id` int NOT NULL,
  `id_frm_care` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_care_suspected_adr_metabolic_1` (`id_frm_care`, `id_pharmacy_dictionary`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE frm_care_suspected_adr_musculoskeletal (
  `id` int NOT NULL,
  `id_frm_care` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_care_suspected_adr_musculoskeletal_1` (`id_frm_care`, `id_pharmacy_dictionary`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE frm_care_suspected_adr_general (
  `id` int NOT NULL,
  `id_frm_care` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_care_suspected_adr_general_1` (`id_frm_care`, `id_pharmacy_dictionary`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE frm_care_adr_intervention (
  `id` int NOT NULL,
  `id_frm_care` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_care_adr_intervention_1` (`id_frm_care`, `id_pharmacy_dictionary`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/****************** END FRM_CARE tables ************************************************/



/****************** START FRM_COMMUNITY tables ************************************************/

CREATE TABLE frm_community (
  `id` int NOT NULL,
  `generation_date` datetime default NULL,
  `id_user` int default NULL,
  `date_of_visit` datetime NOT NULL,
  `date_of_visit_month_index` int default NULL,
  `is_commited` bool default false,
  `id_pharmacy` int default NULL,
  `id_patient` int NOT NULL,
  `is_first_visit_to_pharmacy` int default NULL,
  `is_referred_from` int,
  `is_referred_in` int,
  `is_referred_out` int,
  `is_referral_completed` int,
  `is_hiv_risk_assesment_done` int,
  `is_htc_done` int,
  `htc_result_id` int default NULL,
  `is_client_received_htc` int,
  `is_htc_done_in_current_pharmacy` int,
  `is_palliative_services_to_plwha` int,
  `is_sti_services` int,
  `is_reproductive_health_services` int,
  `is_tuberculosis_services` int,
  `is_malaria_services` int,
  `is_ovc_services` int,
  `is_patient_younger_15` int default NULL,
  `is_patient_male` int default NULL,
  `hiv_status` varchar(8) default NULL,
  `is_adr_screened` int,
  `is_adr_symptoms` int,
  `adr_start_date` datetime default NULL,
  `adr_stop_date` datetime default NULL,
  `is_adr_intervention_provided` int,
  `id_nafdac` int default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create index idx_frm_community_date_of_visit_month_index on frm_community(date_of_visit_month_index);

CREATE TABLE frm_community_referred_from (
  `id` int NOT NULL,
  `id_frm_community` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_community_referred_from_1` (`id_frm_community`, `id_pharmacy_dictionary`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE frm_community_referred_in (
  `id` int NOT NULL,
  `id_frm_community` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_community_referred_in_1` (`id_frm_community`, `id_pharmacy_dictionary`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE frm_community_referred_out (
  `id` int NOT NULL,
  `id_frm_community` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_community_referred_out_1` (`id_frm_community`, `id_pharmacy_dictionary`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE frm_community_palliative_care_type (
  `id` int NOT NULL,
  `id_frm_community` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_community_palliative_care_type_1` (`id_frm_community`, `id_pharmacy_dictionary`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE frm_community_sti_type (
  `id` int NOT NULL,
  `id_frm_community` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_community_sti_type_1` (`id_frm_community`, `id_pharmacy_dictionary`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE frm_community_reproductive_health_type (
  `id` int NOT NULL,
  `id_frm_community` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_community_reproductive_health_type_1` (`id_frm_community`, `id_pharmacy_dictionary`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE frm_community_tuberculosis_type (
  `id` int NOT NULL,
  `id_frm_community` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_community_tuberculosis_type_1` (`id_frm_community`, `id_pharmacy_dictionary`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE frm_community_malaria_type (
  `id` int NOT NULL,
  `id_frm_community` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_community_malaria_type_1` (`id_frm_community`, `id_pharmacy_dictionary`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE frm_community_ovc_type (
  `id` int NOT NULL,
  `id_frm_community` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_community_ovc_type_1` (`id_frm_community`, `id_pharmacy_dictionary`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE frm_community_adr_intervention (
  `id` int NOT NULL,
  `id_frm_community` int NOT NULL,
  `id_pharmacy_dictionary` int NOT NULL,
  UNIQUE KEY `cons_frm_community_adr_intervention_1` (`id_frm_community`, `id_pharmacy_dictionary`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/****************** END FRM_COMMUNITY tables ************************************************/

CREATE TABLE report_care (
  `id` int NOT NULL,
  `generation_date` datetime NOT NULL,
  `period` int,
  `id_user` int default NULL,
  `id_pharmacy` int NOT NULL,
  `number_of_clients_with_prescription_male_younger_15` int default 0,
  `number_of_clients_with_prescription_female_younger_15` int default 0,
  `number_of_clients_with_prescription_male_from_15` int default 0,
  `number_of_clients_with_prescription_female_from_15` int default 0,
  `number_of_dispensed_drugs` int,
  `filename` varchar(255),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE report_community (
  `id` int NOT NULL,
  `generation_date` datetime NOT NULL,
  `period` int,
  `id_user` int default NULL,
  `id_pharmacy` int NOT NULL,
  `filename` varchar(255),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE report_community_services (
  `id` int NOT NULL,
  `generation_date` datetime NOT NULL,
  `period` int,
  `id_user` int default NULL,
  `id_pharmacy` int NOT NULL,
  `filename` varchar(255),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE nafdac (
  `id` int NOT NULL,
  `generation_date` datetime default NULL,
  `date_of_visit` datetime NOT NULL,
  `id_user` int NOT NULL,
  `id_patient` int NOT NULL,
  `id_pharmacy` int NOT NULL,
  `filename` varchar(255),
  `adr_start_date` datetime default NULL,
  `adr_stop_date` datetime default NULL,
  `adr_description` text,
  `was_admitted` bool default false,
  `was_hospitalization_prolonged` bool default false,
  `duration_of_admission` varchar(8) default NULL,
  `treatment_of_reaction` varchar(255) default NULL,
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

CREATE TABLE nafdac_medicine (
  `id` int NOT NULL,
  `id_nafdac` int NOT NULL,
  `name` varchar(255) default NULL,
  `dosage` varchar(255) default NULL,
  `route` varchar(255) default NULL,
  `started` varchar(255) default NULL,
  `stopped` varchar(255) default NULL,
  `reason` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE tmp_community_report_register (
  `report_uid`              varchar(32) NOT NULL,
  `client_name`             varchar(255),
  `is_plhiv_paba`           bool,
  `fe_provided_preventive`  bool,
  `fe_provided_clinical`    bool,
  `fe_provided_supportive`  bool,
  `plwha_young_male`        bool,
  `plwha_young_female`      bool,
  `plwha_adult_male`        bool,
  `plwha_adult_female`      bool,
  `paba_young_male`         bool,
  `paba_young_female`       bool,
  `paba_adult_male`         bool,
  `paba_adult_female`       bool,
  `other_young_male`        bool,
  `other_young_female`      bool,
  `other_adult_male`        bool,
  `other_adult_female`      bool,
  `se_provided_preventive`  bool,
  `se_provided_clinical`    bool,
  `se_provided_supportive`  bool,
  `preventive_1`            bool,
  `preventive_2`            bool,
  `preventive_3`            bool,
  `preventive_4`            bool,
  `preventive_5`            bool,
  `preventive_6`            bool,
  `supportive_out_1`        bool,
  `supportive_out_2`        bool,
  `supportive_out_3`        bool,
  `supportive_out_4`        bool,
  `supportive_out_5`        bool,
  `supportive_out_6`        bool,
  `supportive_in_1`         bool,
  `supportive_in_2`         bool,
  `supportive_in_3`         bool,
  `supportive_in_4`         bool,
  `supportive_in_5`         bool,
  `clinical_sti_1`          bool,
  `clinical_sti_2`          bool,
  `clinical_malaria_1`      bool,
  `clinical_malaria_2`      bool,
  `clinical_malaria_3`      bool,
  `clinical_reproductive_1` bool,
  `clinical_reproductive_2` bool,
  `clinical_reproductive_3` bool,
  `clinical_reproductive_4` bool,
  `clinical_tb_1`           bool,
  `clinical_tb_2`           bool,
  `clinical_tb_3`           bool,
  `clinical_tb_4`           bool,
  `clinical_tb_5`           bool,
  `clinical_palliative_1`   bool,
  `clinical_palliative_2`   bool,
  `adr_screened`            bool,
  `adr_not_detected`        bool,
  `adr_detected`            bool,
  `nafdac_filled`           bool,
  `adr_intervention_1`      bool,
  `adr_intervention_2`      bool,
  `adr_intervention_3`      bool
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


alter table state
    add constraint fk_state_id_country foreign key (id_country)
        references country(id) on delete cascade;

alter table lga
    add constraint fk_lga_id_state foreign key (id_state)
        references state(id) on delete set NULL;

alter table facility
    add constraint fk_facility_id_lga foreign key (id_lga)
        references lga(id) on delete set NULL;

alter table facility
    add constraint fk_facility_id_facility_type foreign key (id_facility_type)
        references facility_type(id) on delete set NULL;

alter table facility
    add constraint fk_facility_id_facility_level foreign key (id_facility_level)
        references facility_level(id) on delete set NULL;


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



alter table frm_care
    add constraint fk_frm_care_id_patient foreign key (id_patient)
        references patient(id);

alter table frm_care
    add constraint fk_frm_care_id_pharmacy foreign key (id_pharmacy)
        references pharmacy(id) on delete cascade;


alter table frm_care
    add constraint fk_frm_care_adr_severity_id foreign key (adr_severity_id)
        references pharmacy_dictionary(id) on delete set NULL;

alter table frm_care
    add constraint fk_frm_care_id_nafdac foreign key (id_nafdac)
        references nafdac(id) on delete set NULL;




alter table frm_community
    add constraint fk_frm_community_id_patient foreign key (id_patient)
        references patient(id);

alter table frm_community
    add constraint fk_frm_community_id_pharmacy foreign key (id_pharmacy)
        references pharmacy(id) on delete cascade;

alter table pharmacy_dictionary
    add constraint fk_pharmacy_dictionary_type_id_pharmacy_dictionary_type foreign key (id_pharmacy_dictionary_type)
        references pharmacy_dictionary_type(id) on delete cascade;



alter table frm_care_med_error_type
    add constraint fk_frm_care_med_error_type_id_frm_care foreign key (id_frm_care)
        references frm_care(id) on delete cascade;

alter table frm_care_med_error_type
    add constraint fk_frm_care_med_error_type_id_pharmacy_dictionary foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);

alter table frm_care_med_adh_problem
    add constraint fk_frm_care_med_adh_problem_id_frm_care foreign key (id_frm_care)
        references frm_care(id) on delete cascade;

alter table frm_care_med_adh_problem
    add constraint fk_frm_care_med_adh_problem_id_pharmacy_dictionary foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);

alter table frm_care_med_error_intervention
    add constraint fk_frm_care_med_error_intervention_id_frm_care foreign key (id_frm_care)
        references frm_care(id) on delete cascade;

alter table frm_care_med_error_intervention
    add constraint fk_frm_care_med_error_intervention_id_pharmacy_dictionary foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);

alter table frm_care_adh_intervention
    add constraint fk_frm_care_adh_intervention_id_frm_care foreign key (id_frm_care)
        references frm_care(id) on delete cascade;

alter table frm_care_adh_intervention
    add constraint fk_frm_care_adh_intervention_id_pharmacy_dictionary foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);

alter table frm_care_med_error_intervention_outcome
    add constraint fk_frm_care_med_error_intervention_outcome_id_frm_care foreign key (id_frm_care)
        references frm_care(id) on delete cascade;

alter table frm_care_med_error_intervention_outcome
    add constraint fk_frm_care_med_error_intervention_outcome_2 foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);

alter table frm_care_adh_intervention_outcome
    add constraint fk_frm_care_adh_intervention_outcome_id_frm_care foreign key (id_frm_care)
        references frm_care(id) on delete cascade;

alter table frm_care_adh_intervention_outcome
    add constraint fk_frm_care_adh_intervention_outcome_id_pharmacy_dictionary foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);

alter table frm_care_suspected_adr_hepatic
    add constraint fk_frm_care_suspected_adr_hepatic_id_frm_care foreign key (id_frm_care)
        references frm_care(id) on delete cascade;

alter table frm_care_suspected_adr_hepatic
    add constraint fk_frm_care_suspected_adr_hepatic_id_pharmacy_dictionary foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);

alter table frm_care_suspected_adr_nervous
    add constraint fk_frm_care_suspected_adr_nervous_id_frm_care foreign key (id_frm_care)
        references frm_care(id) on delete cascade;

alter table frm_care_suspected_adr_nervous
    add constraint fk_frm_care_suspected_adr_nervous_id_pharmacy_dictionary foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);

alter table frm_care_suspected_adr_cardiovascular
    add constraint fk_frm_care_suspected_adr_cardiovascular_id_frm_care foreign key (id_frm_care)
        references frm_care(id) on delete cascade;

alter table frm_care_suspected_adr_cardiovascular
    add constraint fk_frm_care_suspected_adr_cardiovascular_id_pharmacy_dictionary foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);

alter table frm_care_suspected_adr_skin
    add constraint fk_frm_care_suspected_adr_skin_id_frm_care foreign key (id_frm_care)
        references frm_care(id) on delete cascade;

alter table frm_care_suspected_adr_skin
    add constraint fk_frm_care_suspected_adr_skin_id_pharmacy_dictionary foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);

alter table frm_care_suspected_adr_metabolic
    add constraint fk_frm_care_suspected_adr_metabolic_id_frm_care foreign key (id_frm_care)
        references frm_care(id) on delete cascade;

alter table frm_care_suspected_adr_metabolic
    add constraint fk_frm_care_suspected_adr_metabolic_id_pharmacy_dictionary foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);

alter table frm_care_suspected_adr_musculoskeletal
    add constraint fk_frm_care_suspected_adr_musculoskeletal_id_frm_care foreign key (id_frm_care)
        references frm_care(id) on delete cascade;

alter table frm_care_suspected_adr_musculoskeletal
    add constraint fk_frm_care_suspected_adr_musculoskeletal_id_pharmacy_dictionary foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);

alter table frm_care_suspected_adr_general
    add constraint fk_frm_care_suspected_adr_general_id_frm_care foreign key (id_frm_care)
        references frm_care(id) on delete cascade;

alter table frm_care_suspected_adr_general
    add constraint fk_frm_care_suspected_adr_general_id_pharmacy_dictionary foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);

alter table frm_care_adr_intervention
    add constraint fk_frm_care_adr_intervention_id_frm_care foreign key (id_frm_care)
        references frm_care(id) on delete cascade;

alter table frm_care_adr_intervention
    add constraint fk_frm_care_adr_intervention_id_pharmacy_dictionary foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);



alter table frm_community_referred_from
    add constraint fk_frm_community_referred_from_id_frm_community foreign key (id_frm_community)
        references frm_community(id) on delete cascade;

alter table frm_community_referred_from
    add constraint fk_frm_community_referred_from_id_pharmacy_dictionary foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);


alter table frm_community_referred_in
    add constraint fk_frm_community_referred_in_id_frm_community foreign key (id_frm_community)
        references frm_community(id) on delete cascade;

alter table frm_community_referred_in
    add constraint fk_frm_community_referred_in_id_pharmacy_dictionary foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);

alter table frm_community_referred_out
    add constraint fk_frm_community_referred_out_id_frm_community foreign key (id_frm_community)
        references frm_community(id) on delete cascade;

alter table frm_community_referred_out
    add constraint fk_frm_community_referred_out_id_pharmacy_dictionary foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);

alter table frm_community_palliative_care_type
    add constraint fk_frm_community_palliative_care_type_id_frm_community foreign key (id_frm_community)
        references frm_community(id) on delete cascade;

alter table frm_community_palliative_care_type
    add constraint fk_frm_community_palliative_care_type_id_pharmacy_dictionary foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);

alter table frm_community_sti_type
    add constraint fk_frm_community_sti_type_id_frm_community foreign key (id_frm_community)
        references frm_community(id) on delete cascade;

alter table frm_community_sti_type
    add constraint fk_frm_community_sti_type_id_pharmacy_dictionary foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);

alter table frm_community_reproductive_health_type
    add constraint fk_frm_community_reproductive_health_type_id_frm_community foreign key (id_frm_community)
        references frm_community(id) on delete cascade;

alter table frm_community_reproductive_health_type
    add constraint fk_frm_community_reproductive_health_type_id_pharmacy_dictionary foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);

alter table frm_community_tuberculosis_type
    add constraint fk_frm_community_tuberculosis_type_id_frm_community foreign key (id_frm_community)
        references frm_community(id) on delete cascade;

alter table frm_community_tuberculosis_type
    add constraint fk_frm_community_tuberculosis_type_id_pharmacy_dictionary foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);

alter table frm_community_malaria_type
    add constraint fk_frm_community_malaria_type_id_frm_community foreign key (id_frm_community)
        references frm_community(id) on delete cascade;

alter table frm_community_malaria_type
    add constraint fk_frm_community_malaria_type_id_pharmacy_dictionary foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);


alter table frm_community_ovc_type
    add constraint fk_frm_community_ovc_type_id_frm_community foreign key (id_frm_community)
        references frm_community(id) on delete cascade;

alter table frm_community_ovc_type
    add constraint fk_frm_community_ovc_type_id_pharmacy_dictionary foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);

alter table frm_community_adr_intervention
    add constraint fk_frm_community_adr_intervention_id_frm_community foreign key (id_frm_community)
        references frm_community(id) on delete cascade;

alter table frm_community_adr_intervention
    add constraint fk_frm_community_adr_intervention_id_pharmacy_dictionary foreign key (id_pharmacy_dictionary)
        references pharmacy_dictionary(id);



alter table report_care
    add constraint fk_report_care_id_pharmacy foreign key (id_pharmacy)
        references pharmacy(id);

alter table report_care
    add constraint fk_report_care_id_user foreign key (id_user)
        references user(id) on delete set NULL;

alter table report_community
    add constraint fk_report_community_id_pharmacy foreign key (id_pharmacy)
        references pharmacy(id);

alter table report_community
    add constraint fk_report_community_id_user foreign key (id_user)
        references user(id) on delete set NULL;

alter table report_community_services
    add constraint fk_report_community_services_1 foreign key (id_pharmacy)
        references pharmacy(id) on delete cascade;

alter table report_community_services
    add constraint fk_report_community_services_2 foreign key (id_user)
        references user(id) on delete set NULL;

alter table nafdac
    add constraint fk_nafdac_id_user foreign key (id_user)
        references user(id);

alter table nafdac
    add constraint fk_nafdac_id_patient foreign key (id_patient)
        references patient(id);

alter table nafdac
    add constraint fk_nafdac_id_pharmacy foreign key (id_pharmacy)
        references pharmacy(id);

alter table nafdac_medicine
    add constraint fk_nafdac_medicine_id_nafdac foreign key (id_nafdac)
        references nafdac(id) on delete cascade;



insert into user(id, login, password, is_active,role) values (1, 'admin', MD5('admin'), 1, 'pharmacy_manager');

INSERT INTO db_sequence(name,value) VALUES ('log4php_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('pharmacy_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('user_id_seq', 10);
INSERT INTO db_sequence(name,value) VALUES ('physician_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('patient_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('country_id_seq', 300);
INSERT INTO db_sequence(name,value) VALUES ('state_id_seq', 300);
INSERT INTO db_sequence(name,value) VALUES ('facility_type_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('facility_level_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('facility_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('lga_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('pharmacy_dictionary_type_id_seq', 100);
INSERT INTO db_sequence(name,value) VALUES ('pharmacy_dictionary_id_seq', 1000);

INSERT INTO db_sequence(name,value) VALUES ('frm_care_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('frm_care_med_error_type_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('frm_care_med_adh_problem_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('frm_care_med_error_intervention_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('frm_care_adh_intervention_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('frm_care_med_error_intervention_outcome_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('frm_care_adh_intervention_outcome_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('frm_care_suspected_adr_hepatic_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('frm_care_suspected_adr_nervous_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('frm_care_suspected_adr_cardiovascular_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('frm_care_suspected_adr_skin_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('frm_care_suspected_adr_metabolic_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('frm_care_suspected_adr_musculoskeletal_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('frm_care_suspected_adr_general_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('frm_care_adr_intervention_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('frm_community_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('frm_community_referred_from_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('frm_community_referred_in_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('frm_community_referred_out_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('frm_community_palliative_care_type_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('frm_community_sti_type_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('frm_community_reproductive_health_type_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('frm_community_tuberculosis_type_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('frm_community_malaria_type_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('frm_community_ovc_type_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('frm_community_adr_intervention_id_seq', 1);

INSERT INTO db_sequence(name,value) VALUES ('report_care_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('report_community_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('report_community_services_id_seq', 1);


INSERT INTO db_sequence(name,value) VALUES ('nafdac_id_seq', 1);
INSERT INTO db_sequence(name,value) VALUES ('nafdac_medicine_id_seq', 1);

insert into db_version values (1, 20131001, 1);
