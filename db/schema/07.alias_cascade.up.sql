
-- add cascade on delete for supply chain aliases
alter table supplychain_alias add 
    constraint supplychain_alias_supplychain_id_fk 
        foreign key (supplychain_id) references supplychain (id)
        on delete cascade;

-- update schema version
insert into sourcemap_schema_version ("key", extra) values (
    '07.alias_cascade', 'Cascade on delete for supply chain aliases.'
);
