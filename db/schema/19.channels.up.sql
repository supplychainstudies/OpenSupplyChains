-- Channel Functionality
create table channels (
    id serial,
    name varchar(128) default null,
	alias varchar(128) default null,
    user_id integer not null,
    constraint channels_id_pkey PRIMARY KEY (id),
    constraint channels_name_key unique (name),
	constraint channels_alias_key unique (alias),
    foreign key (user_id) references "user" (id) on delete cascade
);   

alter table supplychain add column enable_comments BOOLEAN not null default TRUE;

insert into sourcemap_schema_version ("key", extra) values (
    '19.channels', 'Channels table and comments switch'
);