alter table supplychain drop column flags;

delete from sourcemap_schema_version where "key" = '10.supplychainflags';
