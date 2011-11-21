-- Additional User Fields
alter table "user" drop column customer_id;

delete from sourcemap_schema_version ("key", extra) values (
    '21.payment', 'Added additional user field for customer ID'
);

