<?php
$g_majorDb = 1;
$g_minorDb = 20130601;
$g_buildDb = 1;

/**
 * Update database structure
 *
 * @param Zend_Db_Adapter_Abstract $db
 * @return boolean
 */
function update_to_1_20130601_1(Zend_Db_Adapter_Abstract $db) {


    try {
        $query = sprintf("
delete from frm_care;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }

    
    try {
        $query = sprintf("
                delete from frm_community;
                ");
        $db->query($query);
    
    }
    catch(Exception $ex) {
        return false;
    }
    
    try {
        $query = sprintf("
delete from pharmacy_dictionary_type;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    try {
        $query = sprintf("
delete from pharmacy_dictionary;
        ");
        $db->query($query);

    }
    catch(Exception $ex) {
        return false;
    }


    $queries = sprintf("
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
insert into pharmacy_dictionary_type(id,ordernum,name) values (16, 16, 'Referred in List');
insert into pharmacy_dictionary_type(id,ordernum,name) values (17, 17, 'Referred out List');
insert into pharmacy_dictionary_type(id,ordernum,name) values (18, 18, 'Type of HIV testing results');
insert into pharmacy_dictionary_type(id,ordernum,name) values (19, 19, 'Palliative Care Services');
insert into pharmacy_dictionary_type(id,ordernum,name) values (20, 20, 'Reproductive Health services');
insert into pharmacy_dictionary_type(id,ordernum,name) values (21, 21, 'STI Services');
insert into pharmacy_dictionary_type(id,ordernum,name) values (22, 22, 'Tuberculosis services');
insert into pharmacy_dictionary_type(id,ordernum,name) values (23, 23, 'OVC Care and Support services');

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
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (323, 17, 'TB');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (324, 17, 'STI');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (325, 17, 'FP');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (326, 17, 'Support group');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (327, 17, 'OVC services');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (340, 18, 'Positive');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (341, 18, 'Negative');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (342, 18, 'Intermediate');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (360, 19, 'Adherence Counseling');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (361, 19, 'Ol management');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (362, 19, 'Psychosocial support');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (363, 19, 'Pain Management');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (364, 19, 'Nutritional Support and Counseling');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (365, 19, 'Malaria Prevention');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (366, 19, 'Malaria Treatment');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (380, 20, 'Condoms Provided');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (381, 20, 'Emergency Contraceptive Provided');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (382, 20, 'Injectable Contraceptive Provided');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (383, 20, 'Oral Contraceptives Provided');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (384, 20, 'RH/FP Counseling');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (400, 21, 'STI Screening');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (401, 21, 'STI Treatment');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (402, 21, 'STI Counseling');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (420, 22, 'TB Screening');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (421, 22, 'TB Adherence Support');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (422, 22, 'TB Drugs Refills');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (423, 22, 'DOTs/CTBC');

insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (440, 23, 'Enrollment');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (441, 23, 'Educational support');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (442, 23, 'Shelter');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (443, 23, 'Nutritional');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (444, 23, 'Legal support/Protection');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (445, 23, 'Health support');
insert into pharmacy_dictionary(id,id_pharmacy_dictionary_type,name) values (446, 23, 'Economic support (Skill acquisition)');
    ");

    $queriesArr = preg_split('/[\n\r]/', $queries, -1, PREG_SPLIT_NO_EMPTY);
    foreach($queriesArr as $query) {
        $query = trim($query);
        if(empty($query)) {
            continue;
        }
        try {
            $db->query($query);

        }
        catch(Exception $ex) {
            return false;
        }
    }

    return true;
}