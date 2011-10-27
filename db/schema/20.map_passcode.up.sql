-- Additionalsupplychain 
alter table supplychain add column passcode varchar(32) default null;

insert into sourcemap_schema_version ("key", extra) values (
    '20.map_passcode', 'Added passcode information into supplychain'
);
