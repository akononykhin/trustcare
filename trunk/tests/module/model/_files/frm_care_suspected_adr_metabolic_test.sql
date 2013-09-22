insert into patient(id, identifier) values (1, 'name1');
insert into patient(id, identifier) values (2, 'name2');


insert into frm_care(id, id_patient, date_of_visit, generation_date) values (1, 1, now(), now());
insert into frm_care(id, id_patient, date_of_visit, generation_date) values (2, 2, now(), now());
