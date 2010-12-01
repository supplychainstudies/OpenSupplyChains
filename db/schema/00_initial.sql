-- createdb -T postgistemplate -O smap smap "Sourcemap Redux"
-- \i /usr/share/postgres/<v.m>/contrib/{postgis.sql,postgis_comments.sql,spatial_ref_...sql

create table "supplychain" (
    id serial,
    created integer not null,
    modified integer not null,
    constraint supplychain_id_pkey primary key (id)
);

create table "supplychain_attribute" (
    id serial,
    supplychain_id integer not null,
    "key" varchar(32) not null,
    "value" text,
    constraint supplychain_attribute_id_pkey primary key (id),
    constraint supplychain_attribute_key_key unique (supplychain_id, "key"),
    foreign key (supplychain_id) references supplychain (id) on delete cascade
);

create table "stop" (
    id serial,
    supplychain_id integer not null,
    constraint stop_id_pkey primary key (id),
    foreign key (supplychain_id) references supplychain (id) on delete cascade
);
select AddGeometryColumn('', 'stop', 'geometry', 3785, 'POINT', 2);

create table "stop_attribute" (
    id serial,
    stop_id integer not null,
    "key" varchar(32) not null,
    "value" text,
    constraint stop_attribute_id_pkey primary key (id),
    constraint stop_attribute_key_key unique (stop_id, "key"),
    foreign key (stop_id) references stop (id) on delete cascade
);

create table "hop" (
    id serial,
    from_stop_id integer not null,
    to_stop_id integer not null,
    constraint hop_id_pkey primary key (id),
    foreign key (from_stop_id) references stop (id) on delete cascade,
    foreign key (to_stop_id) references stop (id) on delete cascade
);
select AddGeometryColumn('', 'hop', 'geometry', 3785, 'MULTILINESTRING', 2);
