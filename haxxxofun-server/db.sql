create database `haxxxofun-tasks`
    collate utf8mb4_general_ci;

use `haxxxofun-tasks`;

create table users
(
    username varchar(30) null,
    password varchar(30) null
);

insert into users
values ('admin', '1181271721626231');

create database `haxxxofun`
    collate utf8mb4_general_ci;

use `haxxxofun`;

create table `results`
(
    id         int auto_increment primary key,
    task       tinyint                            not null,
    user       varchar(255)                       not null,
    epoch_time int                                not null,
    added      datetime default CURRENT_TIMESTAMP not null
);