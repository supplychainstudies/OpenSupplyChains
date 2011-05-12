alter table supplychain add column category bigint;

create table category (
    id serial,
    name varchar(16) not null unique,
    title varchar(32) not null unique,
    description text,
    "left" bigint not null,
    "right" bigint not null
);

insert into sourcemap_schema_version ("key", extra) values (
    '11.sccategories', 'Supplychain categories.'
);
