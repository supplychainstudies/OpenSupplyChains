-- Additional User Field 
alter table "user" drop column avatar_url; 

delete from sourcemap_schema_version ("key", extra) values (
    '22.avatars', 'Added additional user field for avatars'
);
