alter table user_event alter column "event" type varchar(64);


delete from sourcemap_schema_version where "key" = '15.usrevtfix';
