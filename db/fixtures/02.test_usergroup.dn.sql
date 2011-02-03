delete from usergroup (owner_id, name) values (1, 'Testgroup');
delete from usergroup (owner_id, name) values (2, 'Testgroup1');
delete from usergroup (owner_id, name) values (3, 'Testgroup2');

delete from user_usergroup (usergroup_id, user_id) values (1, 2);
delete from user_usergroup (usergroup_id, user_id) values (1, 3);
delete from user_usergroup (usergroup_id, user_id) values (1, 4);
delete from user_usergroup (usergroup_id, user_id) values (2, 2);
delete from user_usergroup (usergroup_id, user_id) values (2, 3);
delete from user_usergroup (usergroup_id, user_id) values (3, 2);
delete from user_usergroup (usergroup_id, user_id) values (3, 4);
