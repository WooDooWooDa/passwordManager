set search_path = "passwordmanagerdb";

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

drop table token;
create table if not exists token
(
    user_id integer,
    cookie_token varchar(64) not null,
    constraint token_pk
        primary key (cookie_token)
);

alter table token owner to etudiant;

create unique index if not exists token_user_id_uindex
    on token (user_id);

drop table service;
create table if not exists service
(
    id serial not null,
    name varchar(255),
    img varchar(255),
    constraint service_pk
        primary key (id)
);

alter table service owner to etudiant;

INSERT INTO authentication (user_id, username, password, firstname, lastname, email) VALUES (1, 'admin', '$2y$10$pkb2ag75IRayNlgvJQkoeuGeYuc9sSgOnASjbGuxFEtUr/MFLiFlG', 'admin', 'system', 'admin-system@hotmail.com');
INSERT INTO authentication (user_id, username, password, firstname, lastname, email) VALUES (2, 'bob', '$2y$10$A9fbOu7JoDUgHACVocChROnjHACP7nAi5BKESNsLn1LpsOrQ/wiTa', 'Jé', 'Bouy', 'jé-ou@gmail.com');
INSERT INTO authentication (user_id, username, password, firstname, lastname, email) VALUES (3, 'jé', '$2y$10$f4xxk/pK7FZMk2XmV6zOLe/lqfECqb8qJ.ejv/XDI.TbgQnkOxYze', 'Jérémie', 'Bou', 'jeremie-bouchard@hotmail.com');
INSERT INTO authentication (user_id, username, password, firstname, lastname, email) VALUES (4, 'bri', '$2y$10$ErSWZfxsJct2damPB/d.y./01wZwD/JoYSqBD6mMByAe01lEYWxSa', 'Brigitte', 'Berger', 'bri@hotmail.com');

INSERT INTO service (id, name, img) VALUES (1, 'Facebook', '/assets/images/facebook_logo.png');
INSERT INTO service (id, name, img) VALUES (2, 'Netflix', '/assets/images/netflix_logo.png');
INSERT INTO service (id, name, img) VALUES (3, 'Instagram', '/assets/images/instagram_logo.png');