drop table user_token;
drop table user_role;
drop table role;
drop table "user";

delete from sourcemap_schema_version where "key" = '01.auth';
