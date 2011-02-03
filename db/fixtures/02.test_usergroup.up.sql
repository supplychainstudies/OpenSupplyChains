insert into usergroup (owner_id, name) values (1, 'Testgroup');
insert into usergroup (owner_id, name) values (2, 'Testgroup1');
insert into usergroup (owner_id, name) values (3, 'Testgroup2');

insert into user_usergroup (usergroup_id, user_id) values (1, 2);
insert into user_usergroup (usergroup_id, user_id) values (1, 3);
insert into user_usergroup (usergroup_id, user_id) values (1, 4);
insert into user_usergroup (usergroup_id, user_id) values (2, 2);
insert into user_usergroup (usergroup_id, user_id) values (2, 3);
insert into user_usergroup (usergroup_id, user_id) values (3, 2);
insert into user_usergroup (usergroup_id, user_id) values (3, 4);
