-- Channel Functionality
create table channel (
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

create table channel_featured (
	channel_id integer not null,
	index integer not null,
	supplychain_id integer not null,
	constraint channel_id_index_pkey PRIMARY KEY (channel_id, index),
	foreign key (channel_id) references "channel" (id) on delete cascade, 
	foreign key (supplychain_id) references "supplychain" (id) on delete cascade 
); 

insert into sourcemap_schema_version ("key", extra) values (
    '19.channels', 'Channels table and comments switch'
);