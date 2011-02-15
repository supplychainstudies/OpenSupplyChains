create table supplychain_alias (
    id serial,
    supplychain_id integer not null,
    site varchar(32) not null default 'default',
    alias varchar(32) not null,
    constraint supplychain_alias_id_pkey primary key (id),
    constraint supplychain_alias_key unique (site, alias)
);

insert into sourcemap_schema_version ("key", extra) values (
    '03.supplychain_alias', 'Supplychain aliases.'
);
