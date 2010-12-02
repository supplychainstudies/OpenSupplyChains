-- Kohana auth module default roles
INSERT INTO role (name, description) VALUES ('login', 'Login privileges, granted after account confirmation');
INSERT INTO role (name, description) VALUES ('admin', 'Administrative user, has access to everything.');

-- Default administrator user.
insert into "user" (email, username, password) values ('admin@sourcemap.org', 'administrator', '9168e13964c7f552ac278fc00df8d80aeee04beedf591cccf8');

-- Add admin role to administrator user.
insert into user_role (user_id, role_id) values (1, 2);

