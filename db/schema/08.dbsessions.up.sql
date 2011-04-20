create table  sessions ( -- used by kohana's session_database class. ugh.
    session_id varchar(24) primary key,
    last_active integer not null,
    contents text not null
);

insert into sourcemap_schema_version ("key", extra) values (
    '08.dbsessions', 'Database session storage.'
);
