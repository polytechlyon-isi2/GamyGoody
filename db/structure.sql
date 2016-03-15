drop table if exists category;
drop table if exists game;
drop table if exists article;
drop table if exists user;
drop table if exists comment;

create table category (
    cat_id integer not null primary key auto_increment,
    cat_title varchar(100) not null
) engine=innodb character set utf8 collate utf8_unicode_ci;

create table game (
    game_id integer not null primary key auto_increment,
    game_title varchar(100) not null,
    game_logo_ex varchar(5) COLLATE utf8_unicode_ci NOT NULL,
    game_bg_ex varchar(5) COLLATE utf8_unicode_ci NOT NULL
) engine=innodb character set utf8 collate utf8_unicode_ci;

create table article (
    art_id integer not null primary key auto_increment,
    art_title varchar(100) not null,
    art_content varchar(2000) not null,
    cat_id integer not null,
    game_id integer not null,
    constraint fk_art_cat foreign key(cat_id) references category(cat_id),
    constraint fk_art_game foreign key(game_id) references game(game_id)
) engine=innodb character set utf8 collate utf8_unicode_ci;

create table user (
    user_id integer not null primary key auto_increment,
    user_name varchar(50) not null,
    user_password varchar(88) not null,
    user_salt varchar(23) not null,
    user_role varchar(50) not null 
) engine=innodb character set utf8 collate utf8_unicode_ci;

create table comment (
    com_id integer not null primary key auto_increment,
    com_content varchar(500) not null,
    art_id integer not null,
    user_id integer not null,
    constraint fk_com_art foreign key(art_id) references article(art_id),
    constraint fk_com_usr foreign key(user_id) references user(user_id)
) engine=innodb character set utf8 collate utf8_unicode_ci;