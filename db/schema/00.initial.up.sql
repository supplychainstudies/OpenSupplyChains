-- createdb -T postgistemplate -O smap smap "Sourcemap Redux"
-- \i /usr/share/postgres/<v.m>/contrib/{postgis.sql,postgis_comments.sql,spatial_ref_...sql

create table sourcemap_schema_version (
    "when" timestamp not null default now(),
    "key" varchar(256) not null,
    extra varchar(256),
    constraint sourcemap_schema_version_key_pkey primary key ("key")
);

create table "supplychain" (
    id serial,
    created integer not null,
    modified integer not null,
    constraint supplychain_id_pkey primary key (id)
);

create table "supplychain_attribute" (
    id serial,
    supplychain_id integer not null,
    "key" varchar(128) not null,
    "value" text,
    constraint supplychain_attribute_id_pkey primary key (id),
    constraint supplychain_attribute_key_key unique (supplychain_id, "key"),
    foreign key (supplychain_id) references supplychain (id) on delete cascade
);

create table "stop" (
    id serial,
    supplychain_id integer not null,
    local_stop_id integer not null, -- unique to parent supplychain
    constraint stop_id_pkey primary key (id),
    constraint stop_supplychain_id_local_stop_id_key unique (supplychain_id, local_stop_id),
    foreign key (supplychain_id) references supplychain (id) on delete cascade
);
select AddGeometryColumn('', 'stop', 'geometry', 3857, 'POINT', 2);

create table "stop_attribute" (
    id serial,
    supplychain_id integer not null,
    local_stop_id integer not null,
    "key" varchar(128) not null,
    "value" text,
    constraint stop_attribute_id_pkey primary key (id),
    constraint stop_attribute_key_key unique (supplychain_id, local_stop_id, "key"),
    foreign key (supplychain_id, local_stop_id) references stop (supplychain_id, local_stop_id) on delete cascade
);

create table "hop" (
    id serial,
    supplychain_id integer not null,
    from_stop_id integer not null,
    to_stop_id integer not null,
    constraint hop_id_pkey primary key (id),
    constraint hop_supplychain_id_from_stop_id_to_stop_id_key unique (supplychain_id, from_stop_id, to_stop_id),
    foreign key (supplychain_id, from_stop_id) references stop (supplychain_id, local_stop_id) on delete cascade,
    foreign key (supplychain_id, to_stop_id) references stop (supplychain_id, local_stop_id) on delete cascade
);

select AddGeometryColumn('', 'hop', 'geometry', 3857, 'MULTILINESTRING', 2);

create table "hop_attribute" (
    id serial,
    supplychain_id integer not null,
    from_stop_id integer not null,
    to_stop_id integer not null,
    "key" varchar(128) not null,
    "value" text,
    constraint hop_attribute_id_pkey primary key (id),
    constraint hop_attribute_key_key unique (supplychain_id, from_stop_id, to_stop_id, "key"),
    foreign key (supplychain_id, from_stop_id, to_stop_id) references hop (supplychain_id, from_stop_id, to_stop_id) on delete cascade
);

insert into sourcemap_schema_version ("key", extra) values (
    '00.initial', 'Initial schema.'
);
