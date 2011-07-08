begin;

alter table user_profile
    drop constraint "user_profile_user_id_fkey";
    
alter table user_profile
    add constraint "user_profile_user_id_fkey"
        foreign key (user_id) references "user"(id)
            on delete cascade;

alter table user_message 
    drop constraint "user_message_from_user_id_fkey";

alter table user_message 
    drop constraint "user_message_to_user_id_fkey";

alter table user_message 
    add constraint "user_message_from_user_id_fkey" 
        foreign key (from_user_id) references "user"(id)
        on delete cascade;

alter table user_message 
    add constraint "user_message_to_user_id_fkey" 
        foreign key (to_user_id) references "user"(id)
        on delete cascade;

alter table user_favorite 
    drop constraint "user_favorite_supplychain_id_fkey";

alter table user_favorite 
    drop constraint "user_favorite_user_id_fkey";

alter table user_favorite
    add constraint "user_favorite_supplychain_id_fkey"
        foreign key (supplychain_id) references supplychain(id)
        on delete cascade;

alter table user_favorite
    add constraint "user_favorite_user_id_fkey"
        foreign key (user_id) references "user"(id)
        on delete cascade;

alter table supplychain_comment
    drop constraint "supplychain_comment_supplychain_id_fkey";

alter table supplychain_comment
    drop constraint "supplychain_comment_user_id_fkey";

alter table supplychain_comment
    add constraint "supplychain_comment_supplychain_id_fkey"
        foreign key (supplychain_id) references supplychain(id)
            on delete cascade;

alter table supplychain_comment
    add constraint "supplychain_comment_user_id_fkey"
        foreign key (user_id) references "user"(id)
            on delete cascade;

alter table supplychain_search
    add constraint "supplychain_search_user_id_fkey"
        foreign key (user_id) references "user"(id)
            on delete cascade;

alter table supplychain_search
    add constraint "supplychain_search_supplychain_id_fkey"
        foreign key (supplychain_id) references "supplychain"(id)
            on delete cascade;

commit;
