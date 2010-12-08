drop table stop_attribute;
drop table hop_attribute;

select DropGeometryColumn('', 'hop', 'geometry');

drop table hop;

select DropGeometryColumn('', 'stop', 'geometry');

drop table stop;

drop table supplychain_attribute;

drop table supplychain;

drop table sourcemap_schema_version;
