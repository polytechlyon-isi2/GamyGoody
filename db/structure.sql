drop table if exists comment;
drop table if exists article_image;
drop table if exists article;
drop table if exists category;
drop table if exists game;
drop table if exists user;
drop table if exists image;

create table image (
    img_id integer not null primary key auto_increment,
    img_url varchar(500) not null,
    img_alt varchar(500) not null
) ;

create table game (
    game_id integer not null primary key auto_increment,
    game_title varchar(100) not null,
    game_logo_id integer,
    game_bg_id integer,
    constraint fk_game_logo foreign key(game_logo_id) references image(img_id),
    constraint fk_game_bg foreign key(game_bg_id) references image(img_id)
) engine=innodb character set utf8 collate utf8_unicode_ci;

create table article (
    art_id integer not null primary key auto_increment,
    art_title varchar(100) not null,
    art_content varchar(2000) not null,
    art_price decimal(9,2) not null, 
    game_id integer not null,
    constraint fk_art_game foreign key(game_id) references game(game_id)
) engine=innodb character set utf8 collate utf8_unicode_ci;


create table user (
  user_id integer not null primary key auto_increment,
  user_name varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  user_surname varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  user_firstname varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  user_mail varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  user_address varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  user_city varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  user_password varchar(88) COLLATE utf8_unicode_ci NOT NULL,
  user_salt varchar(23) COLLATE utf8_unicode_ci NOT NULL,
  user_role varchar(50) COLLATE utf8_unicode_ci NOT NULL
) engine=innodb character set utf8 collate utf8_unicode_ci;

create table comment (
    com_id integer not null primary key auto_increment,
    com_content varchar(500) not null,
    art_id integer not null,
    user_id integer not null,
    constraint fk_com_art foreign key(art_id) references article(art_id),
    constraint fk_com_usr foreign key(user_id) references user(user_id)
) engine=innodb character set utf8 collate utf8_unicode_ci;

CREATE TABLE article_image (
  id integer not null primary key auto_increment,
  article_id integer NOT NULL,
  image_id integer NOT NULL,
  level integer NOT NULL,
  constraint fk_arti_art foreign key(article_id) references article(art_id),
  constraint fk_arti_img foreign key(image_id) references image(img_id)
) engine=innodb character set utf8 collate utf8_unicode_ci;