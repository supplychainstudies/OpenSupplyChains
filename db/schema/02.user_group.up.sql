-- user groups
create table "usergroup" (
    id serial,
    owner_id integer not null,
    name varchar(128) not null,
    constraint usergroup_id_pkey PRIMARY KEY (id),
    constraint usergroup_owner_name_key unique (owner_id, name),
    foreign key (owner_id) references "user" (id) on delete cascade
);

-- group membership relation
create table "user_usergroup" (
    id serial,
    usergroup_id integer not null,
    user_id integer not null,
    constraint user_usergroup_id_pkey PRIMARY KEY (id),
    constraint usergroup_id_user_id_key unique (usergroup_id, user_id),
    foreign key (usergroup_id) references usergroup (id) on delete cascade,
    foreign key (user_id) references "user" (id) on delete cascade
);

alter table supplychain add column user_id integer null;
alter table supplychain add column usergroup_id integer null;
alter table supplychain add column usergroup_perms integer not null default 0;
alter table supplychain add column other_perms integer not null default 0;

-- user flag column
alter table "user" add column flags integer not null default 0;

--supplychain revision tracking
create table supplychain_rev (
    id serial,
    supplychain_id integer not null,
    user_id integer not null default 0,
    rev_hash varchar(64) not null,
    data text,
    created integer not null,
    constraint supplychain_rev_id_pkey primary key (id),
    constraint supplychain_rev_rev_hash_key unique (rev_hash),
    foreign key (supplychain_id) references supplychain (id) on delete cascade--,
    --foreign key (user_id) references "user" (id) on delete cascade
);

insert into sourcemap_schema_version ("key", extra) values (
    '02.user_group', 'User group tables.'
);
