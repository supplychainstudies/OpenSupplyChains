drop table channel; 

alter table supplyhain drop column enable_comments;

drop table channel_featured;

delete from sourcemap_schema_version where "key" = '19.channels', 'Channels table and comments switch';
