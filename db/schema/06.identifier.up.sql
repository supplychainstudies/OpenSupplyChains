-- Users with open id tracking
create table "openidusers" (
    id serial,
    identifier varchar(128) default null,
    user_id integer not null,
    constraint openidusers_id_pkey PRIMARY KEY (id),
    constraint openidusers_identifier_key unique (identifier),
    foreign key (user_id) references "user" (id) on delete cascade
);

insert into sourcemap_schema_version ("key", extra) values (
    '06.identifier', 'User identifier table.'
);