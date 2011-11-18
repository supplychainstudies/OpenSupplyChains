-- Additionalsupplychain 
alter table "supplychain" drop column passcode;

delete from sourcemap_schema_version ("key", extra) values (
    '20.map_passcode', 'Added passcode information into supplychain'
);

