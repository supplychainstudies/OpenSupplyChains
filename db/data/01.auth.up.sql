-- Kohana auth module default roles
INSERT INTO role (name, description) VALUES ('login', 'Login privileges, granted after account confirmation');
INSERT INTO role (name, description) VALUES ('admin', 'Administrative user, has access to everything.');

-- Default administrator user.
insert into "user" (email, username, password) values ('admin@sourcemap.org', 'administrator', '937f5184946df987ddfb7131afc2beddc2c9bfc4a04eabbfe5');

-- Add admin role to administrator user.
insert into user_role (user_id, role_id) values (1, 2);
insert into user_role (user_id, role_id) values (1, 1);

