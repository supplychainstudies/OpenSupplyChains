-- Additional User Fields (to be accessed by Channel Users)
alter table "user" drop column display_name; 
alter table "user" drop column description;
alter table "user" drop column url;
alter table "user" drop column banner_url; 

-- Additional supplychain fields (to be accessed by Channel Users)
alter table supplychain add column enable_comments BOOLEAN not null default TRUE;
alter table supplychain drop column user_featured;
alter table supplychain_search drop column user_featured;


delete from sourcemap_schema_version where "key" = '19.userfields', 'Added additional user fields, additional properties for supplychains';
