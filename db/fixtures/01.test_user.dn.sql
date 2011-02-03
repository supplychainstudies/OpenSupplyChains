delete from "user" (email, username, password) values ('test@sourcemap.org', 'test', 'e8cf656fb7c9a32f35bc5966f1b505338bf1a5307eefd3b90c');

delete from "user" (email, username, password) values ('testtwo@sourcemap.org', 'testtwo', 'c49ecaafb14242bb4784e669a42a1c01363bc16935ebaac2b2');
delete from "user" (email, username, password) values ('testthree@sourcemap.org', 'testthree', '280456efb834d227ebb5bf0dd3c883884d61977cf06769008b');



delete from user_role (user_id, role_id) values (2, 1);
delete from user_role (user_id, role_id) values (3, 1);
delete from user_role (user_id, role_id) values (4, 1);