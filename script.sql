set search_path = "passwordmanagerdb";

drop schema passwordmanagerdb;
create schema passwordmanagerdb;

drop table authentication;
create table if not exists authentication
(
    user_id serial not null,
    username varchar(255),
    password varchar(255),
    firstname varchar(255),
    lastname varchar(255),
    email varchar(255),
    constraint authentication_pk
        primary key (user_id)
);

alter table authentication owner to etudiant;

drop table person;
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

drop table service_information;
drop table service;
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
            references passwordmanagerdb.service,
    username               varchar(255),
    password               text,
    user_id                integer,
    id_service_information serial  not null
        constraint service_information_pk
            primary key
);

drop table salt;
create table if not exists salt
(
    user_id integer not null
        constraint salt_pk
            primary key,
    salt    varchar(64)
);

alter table salt owner to etudiant;

alter table passwordmanagerdb.service_information owner to etudiant;

INSERT INTO authentication (user_id, username, password, firstname, lastname, email) VALUES (1, 'admin', '$2y$10$pkb2ag75IRayNlgvJQkoeuGeYuc9sSgOnASjbGuxFEtUr/MFLiFlG', 'admin', 'system', 'admin-system@hotmail.com');
INSERT INTO authentication (user_id, username, password, firstname, lastname, email) VALUES (2, 'bob', '$2y$10$A9fbOu7JoDUgHACVocChROnjHACP7nAi5BKESNsLn1LpsOrQ/wiTa', 'Jé', 'Bouy', 'jé-ou@gmail.com');
INSERT INTO authentication (user_id, username, password, firstname, lastname, email) VALUES (3, 'jé', '$2y$10$f4xxk/pK7FZMk2XmV6zOLe/lqfECqb8qJ.ejv/XDI.TbgQnkOxYze', 'Jérémie', 'Bou', 'jeremie-bouchard@hotmail.fr');
INSERT INTO authentication (user_id, username, password, firstname, lastname, email) VALUES (4, 'bri', '$2y$10$ErSWZfxsJct2damPB/d.y./01wZwD/JoYSqBD6mMByAe01lEYWxSa', 'Brigitte', 'Berger', 'bri@hotmail.com');

INSERT INTO service (id, name, img, url) VALUES (3, 'Instagram', '/assets/images/instagram_logo.png', 'https://www.instagram.com/');
INSERT INTO service (id, name, img, url) VALUES (1, 'Facebook', '/assets/images/facebook_logo.png', 'https://www.facebook.com/');
INSERT INTO service (id, name, img, url) VALUES (4, 'Omnivox', '/assets/images/omnivox_logo.png', 'https://cegepst.omnivox.ca/Login');
INSERT INTO service (id, name, img, url) VALUES (2, 'Netflix', '/assets/images/netflix_logo.png', 'https://www.netflix.com/');
