alter table supplychain drop column category;

drop table category;

drop from table sourcemap_schema_version where "key" = '11.sccategories';
