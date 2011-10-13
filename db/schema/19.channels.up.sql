-- Additional User Fields 
alter table "user" add column display_name varchar(128) default null;
alter table "user" add column description text default null;
alter table "user" add column url text default null;
alter table "user" add column banner_url text default null;

-- Additional supplychain fields
alter table supplychain add column enable_comments BOOLEAN not null default TRUE;
alter table supplychain add column user_featured integer not null;

insert into sourcemap_schema_version ("key", extra) values (
    '19.userfields', 'Added additional user fields, additional properties for supplychains'
);
