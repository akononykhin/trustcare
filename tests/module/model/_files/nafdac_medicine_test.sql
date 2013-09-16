insert into patient(id, identifier) values (1, 'name1');
insert into patient(id, identifier) values (2, 'name2');
insert into patient(id, identifier) values (3, 'name3');

insert into user(id, login) values (21, 'name1');

insert into pharmacy(id, name) values (31, 'Pharmacy1');


insert into nafdac(id, generation_date, id_user, id_patient, id_pharmacy) values (1, now(), 21, 1, 31);
insert into nafdac(id, generation_date, id_user, id_patient, id_pharmacy) values (2, now(), 21, 2, 31);
insert into nafdac(id, generation_date, id_user, id_patient, id_pharmacy) values (3, now(), 21, 3, 31);

