create table if not exists `CatalogCategory`
(
	`id` int(10) not null auto_increment,
	`lft` int(10) not null,
	`rgt` int(10) not null,
	`depth` int(10) not null,
	`active` tinyint(1) default 1,
	`alias` varchar(100) default null,
	`title` varchar(100) default null,
	primary key (`id`),
	key `alias` (`alias`)
) engine InnoDB;
