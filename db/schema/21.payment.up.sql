-- Additional User Fields
alter table "user" add column customer_id varchar(128) default null;

insert into sourcemap_schema_version ("key", extra) values (
    '21.payment', 'Added additional user field for customer ID'
);

