 -- user created column
alter table "user" add column created integer not null default extract(epoch from now());

-- usergroup flag column
alter table "usergroup" add column created integer not null default extract(epoch from now());
alter table "usergroup" add column flags integer not null default 0;


insert into sourcemap_schema_version ("key", extra) values (
    '04.user_created', 'User group alter tables.'
);
