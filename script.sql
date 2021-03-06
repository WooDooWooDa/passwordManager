set search_path = "passwordmanagerdb";

drop table if exists authentication;
create table if not exists authentication
(
    user_id serial not null,
    username varchar(255),
    password varchar(255),
    firstname varchar(255),
    lastname varchar(255),
    email varchar(255),
    phone varchar(20),
    authType int,
    constraint authentication_pk
        primary key (user_id)
);

alter table authentication owner to etudiant;

drop table if exists person;
create table if not exists person
(
    id serial not null,
    firstname varchar(255),
    lastname varchar(255),
    constraint person_pk
        primary key (id)
);

alter table person owner to etudiant;

drop table if exists token;
create table if not exists token
(
    user_id      integer,
    cookie_token varchar(64) not null
        constraint token_pk
            primary key,
    date         varchar(255),
    ip           varchar(50),
    user_agent   varchar(255)
);

alter table token
    owner to etudiant;

drop table if exists service_information;
drop table if exists service;
create table if not exists service
(
    id serial not null,
    name varchar(255),
    img varchar(255),
    url varchar(255),
    constraint service_pk
        primary key (id)
);

alter table service owner to etudiant;

create table if not exists service_information
(
    id_service             integer not null
        constraint service_information_service_id_fk
            references service,
    username               varchar(255),
    password               text,
    user_id                integer,
    id_service_information serial  not null
        constraint service_information_pk
            primary key
);

drop table if exists salt;
create table if not exists salt
(
    user_id integer not null
        constraint salt_pk
            primary key,
    salt    varchar(64)
);

alter table salt owner to etudiant;

alter table service_information owner to etudiant;

create table if not exists google_auth_secret
(
    user_id integer,
    secret varchar(50) not null,
    constraint google_auth_secret_pk
        primary key (secret)
);

alter table google_auth_secret owner to etudiant;

INSERT INTO authentication (user_id, username, password, firstname, lastname, email) VALUES (default, 'admin', '$2y$10$pkb2ag75IRayNlgvJQkoeuGeYuc9sSgOnASjbGuxFEtUr/MFLiFlG', 'admin', 'system', 'admin-system@hotmail.com');
INSERT INTO authentication (user_id, username, password, firstname, lastname, email) VALUES (default, 'bob', '$2y$10$A9fbOu7JoDUgHACVocChROnjHACP7nAi5BKESNsLn1LpsOrQ/wiTa', 'J??', 'Bouy', 'j??-ou@gmail.com');
INSERT INTO authentication (user_id, username, password, firstname, lastname, email, phone, authType) VALUES (default, 'j??', '$2y$10$f4xxk/pK7FZMk2XmV6zOLe/lqfECqb8qJ.ejv/XDI.TbgQnkOxYze', 'J??r??mie', 'Bou', 'woodoowooda@gmail.com', '4508801456', 1);
INSERT INTO authentication (user_id, username, password, firstname, lastname, email, phone, authType) VALUES (default, 'bri', '$2y$10$ErSWZfxsJct2damPB/d.y./01wZwD/JoYSqBD6mMByAe01lEYWxSa', 'Brigitte', 'Berger', 'bri@hotmail.com', '4508801456', 1);

INSERT INTO salt(user_id, salt) VALUES (3, 'sanvuifbsvksowufb3rbfb3976fbbi23');

INSERT INTO service (id, name, img, url) VALUES (default, 'Instagram', '/assets/images/instagram_logo.png', 'https://www.instagram.com/');
INSERT INTO service (id, name, img, url) VALUES (default, 'Facebook', '/assets/images/facebook_logo.png', 'https://www.facebook.com/');
INSERT INTO service (id, name, img, url) VALUES (default, 'Omnivox', '/assets/images/omnivox_logo.png', 'https://cegepst.omnivox.ca/Login');
INSERT INTO service (id, name, img, url) VALUES (default, 'Netflix', '/assets/images/netflix_logo.png', 'https://www.netflix.com/');
