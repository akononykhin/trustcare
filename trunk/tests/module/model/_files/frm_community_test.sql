insert into patient(id, identifier) values (1, 'name1');
insert into patient(id, identifier) values (2, 'name2');


insert into pharmacy(id, name) values (1, 'Pharmacy1');
insert into pharmacy(id, name) values (2, 'Pharmacy2');


insert into user(id, login) values (1, 'name1');
insert into user(id, login) values (2, 'name2');


insert into nafdac(id, generation_date, date_of_visit, id_user, id_patient, id_pharmacy) values (1, now(), '2012-04-01', 1, 1, 1);
insert into nafdac(id, generation_date, date_of_visit, id_user, id_patient, id_pharmacy) values (2, now(), '2012-04-02', 1, 1, 1);
