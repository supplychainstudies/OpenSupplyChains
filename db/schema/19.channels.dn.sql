drop table channels;

alter table "supplyhain" drop column enable_comments;

delete from sourcemap_schema_version where "key" = '19.channels', 'Channels table and comments switch';
