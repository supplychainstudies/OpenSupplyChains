create table user_apikey (
    id serial,
    user_id integer not null,
    apikey varchar(32) not null unique,
    apisecret varchar(32) not null unique,
    created integer not null,
    requests integer not null default 0
);

insert into sourcemap_schema_version ("key", extra) values (
    '12.apikey', 'API keys.'
);
