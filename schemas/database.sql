
--
-- Database database
--

drop database if exists `database`;

create database database default charset=utf8 collate=utf8_unicode_ci;

use database;



--
-- Table structure for table `users`
--

drop table if exists `users`;
create table `users` (
	`id` int unsigned not null auto_increment,
	`client_id` int unsigned default null,
	`created_at` datetime not null,
	`updated_at` datetime default null,
	`deleted_at` datetime default null,
	`name` varchar(255) not null,
	`phone` varchar(30) not null,
	`email` varchar(255) default null,
	`gender` varchar(1) default null,
	`facebook` varchar(255) default null,
	`password` varchar(255) not null,
	`role` varchar(20) not null,
	`status` varchar(1) not null default 'V',
	`comments` text default null,
	primary key (`id`),
	unique key (`email`)
) engine=innodb default charset=utf8 collate=utf8_unicode_ci;

-- roles: admin, user

-- password: admin
insert into users (created_at, name, email, phone, password, role) values (now(), 'name', 'email', 'phone', '$2a$08$0jnTKtBdbvM2Wq4jPKlf5efAkhLU5KWcZpW8ebLXKgDdBJDWq2c.S', 'admin');
