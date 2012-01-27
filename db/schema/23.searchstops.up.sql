-- Additional User Field 
alter table "supplychain_search" add column stops int default null;

insert into sourcemap_schema_version ("key", extra) values (
    '23.searchstops', 'Add additional field to supplychain_search that stores the # of stops'
);
