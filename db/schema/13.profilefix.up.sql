alter table user_profile add constraint user_profile_user_id_unique unique (user_id);

insert into sourcemap_schema_version ("key", extra) values (
    '13.profilefix', 'Added unique constraint to user id column in user_profile.'
);
