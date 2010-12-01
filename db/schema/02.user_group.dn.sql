drop table supplychain_rev;

alter table supplychain drop column other_perms;
alter table supplychain drop column usergroup_perms;
alter table supplychain drop column usergroup_id;
alter table supplychain drop column user_id;

drop table user_usergroup;
drop table usergroup;
