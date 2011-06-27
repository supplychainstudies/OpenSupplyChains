create table supplychain_search (
    id serial,
    supplychain_id integer not null,
    user_id integer not null not null,
    body text,
    favorited integer not null default 0,
    comments integer not null default 0,
    created integer not null default 0,
    modified integer not null default 0,
    category integer,
    featured boolean not null default FALSE
);

insert into sourcemap_schema_version ("key", extra) values (
    '14.searchidx', 'Simple search index.'
);
