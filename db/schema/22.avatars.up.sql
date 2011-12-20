-- Additional User Field 
alter table "user" add column avatar_url varchar(128) default null;

insert into sourcemap_schema_version ("key", extra) values (
    '22.avatars', 'Added additional user field for avatars'
);
