insert into patient(id, identifier) values (1, 'name1');
insert into patient(id, identifier) values (2, 'name2');


insert into frm_community(id, id_patient, date_of_visit) values (1, 1, now());
insert into frm_community(id, id_patient, date_of_visit) values (2, 2, now());
