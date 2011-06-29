INSERT INTO role (name, description) VALUES ('api', 'API access');
INSERT INTO role (name, description) VALUES ('import', 'Access to import tools');

insert into sourcemap_schema_version ("key", extra) values (
    '16.addlroles', 'Additional essential user roles'
);
