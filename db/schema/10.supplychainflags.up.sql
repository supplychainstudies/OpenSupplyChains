alter table supplychain add column flags integer not null default 0;

insert into sourcemap_schema_version ("key", extra) values (
    '10.supplychainflags', 'Flags field for supplychain objects.'
);
