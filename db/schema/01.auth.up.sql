CREATE TABLE "user"
(
    id serial,
    email varchar(318) NOT NULL,
    username varchar(32) NOT NULL,
    "password" varchar(50) NOT NULL,
    logins integer NOT NULL DEFAULT 0,
    last_login integer,
    CONSTRAINT user_id_pkey PRIMARY KEY (id),
    CONSTRAINT user_username_key UNIQUE (username),
    CONSTRAINT user_email_key UNIQUE (email),
    CONSTRAINT user_logins_check CHECK (logins >= 0)
);

CREATE TABLE role
(
    id serial,
    "name" varchar(32) NOT NULL,
    description text NOT NULL,
    CONSTRAINT role_id_pkey PRIMARY KEY (id),
    CONSTRAINT role_name_key UNIQUE (name)
);

CREATE TABLE user_role
(
    id serial,
    user_id integer,
    role_id integer,
    constraint role_user_id_pkey primary key (id),
    foreign key (user_id) references "user" (id) on delete cascade,
    foreign key (role_id) references role (id) on delete cascade
);

CREATE TABLE user_token
(
    id serial,
    user_id integer NOT NULL,
    user_agent varchar(40) NOT NULL,
    token character varying(32) NOT NULL,
    created integer NOT NULL,
    expires integer NOT NULL,
    CONSTRAINT user_token_id_pkey PRIMARY KEY (id),
    CONSTRAINT user_token_token_key UNIQUE (token)
);

CREATE INDEX user_id_idx ON user_role (user_id);
CREATE INDEX role_id_idx ON user_role (role_id);

ALTER TABLE user_token
  ADD CONSTRAINT user_id_fkey FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE;
