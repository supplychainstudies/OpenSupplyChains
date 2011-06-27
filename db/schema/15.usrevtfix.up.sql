alter table user_event alter column "event" type integer using event::text::numeric::integer;

insert into sourcemap_schema_version ("key", extra) values (
    '15.usrevtfix', 'Fix column type in user_event table.'
);
