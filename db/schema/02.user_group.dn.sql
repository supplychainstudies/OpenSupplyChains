drop table supplychain_rev;

alter table supplychain drop column other_perms;
alter table supplychain drop column usergroup_perms;
alter table supplychain drop column usergroup_id;
alter table supplychain drop column user_id;

alter table "user" drop column flags;

drop table user_usergroup;
drop table usergroup;
