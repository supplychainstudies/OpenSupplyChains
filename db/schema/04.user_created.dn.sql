alter table user drop column created;
alter table usergroup drop column created;
alter table usergroup drop column flags;

delete from sourcemap_schema_version where "key" = '04.user_created';
