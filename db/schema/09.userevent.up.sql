create table user_profile (
    id serial,
    user_id integer not null references "user"(id),
    real_name varchar(64) null,
    description text, -- markdown
    url varchar(64),
    privacy_flags integer not null default 0,
    constraint user_profile_id_pkey primary key (id)
);

create table user_event (
    id serial,
    timestamp integer not null,
    event varchar(24),
    scope integer null,
    scope_id integer null,
    data text not null,
    constraint user_event_id_pkey primary key (id)
);

create table user_message (
    id serial,
    timestamp integer not null,
    flags integer not null default 0,
    from_user_id integer not null references "user"(id),
    to_user_id integer not null references "user"(id),
    subject varchar(256) not null default '',
    body text not null default '',
    constraint usermessage_id_pkey primary key (id)
);

create table user_favorite (
    id serial,
    timestamp integer not null,
    user_id integer not null references "user"(id),
    supplychain_id integer not null references supplychain(id),
    constraint user_message_id_pkey primary key (id)
);

create table supplychain_comment (
    id serial,
    flags integer not null default 0,
    timestamp integer not null,
    user_id integer not null references "user"(id),
    supplychain_id integer not null references "supplychain"(id),
    body text,
    constraint supplychain_comment_id_pkey primary key (id)
);

insert into sourcemap_schema_version ("key", extra) values (
    '09.userevent', 'User profiles, events, messages, and comments.'
);
