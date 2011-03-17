alter table supplychain_alias 
    drop constraint supplychain_alias_supplychain_id_fk;

delete from sourcemap_schema_version where "key" = '07.alias_cascade';
