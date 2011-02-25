CREATE TABLE iploc_block (
    st bigint,
    en bigint,
    city character varying(64),
    region character varying(64),
    country character varying(64),
    postal_code character varying(32),
    latitude double precision,
    longitude double precision
);

create index iploc_block_st_en_idx on iploc_block (st, en);

insert into sourcemap_schema_version ("key", extra) values (
    '05.iploc', 'IP location schema.'
);
