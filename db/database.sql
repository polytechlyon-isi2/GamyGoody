create database if not exists gamygoody character set utf8 collate utf8_unicode_ci;
use gamygoody;

grant all privileges on gamygoody.* to 'gamygoody_user'@'localhost' identified by 'admin';