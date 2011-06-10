alter table user_profile drop constraint user_profile_user_id_unique;

delete from sourcemap_schema_version where "key" = '13.profilefix';
