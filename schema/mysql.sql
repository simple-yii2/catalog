create table if not exists `CatalogCategory`
(
	`id` int(10) not null auto_increment,
	`lft` int(10) not null,
	`rgt` int(10) not null,
	`depth` int(10) not null,
	`active` tinyint(1) default 1,
	`alias` varchar(100) default null,
	`title` varchar(100),
	`path` text,
	primary key (`id`),
	key `alias` (`alias`)
) engine InnoDB;
create table if not exists `CatalogCategoryProperty`
(
	`id` int(10) not null auto_increment,
	`category_id` int(10) not null,
	`alias` varchar(50) default null,
	`title` varchar(50) default null,
	`type` int(10) not null,
	`values` text,
	primary key (`id`),
	foreign key (`category_id`) references `CatalogCategory` (`id`) on delete cascade on update cascade,
	key `alias` (`alias`)
) engine InnoDB;
create table if not exists `CatalogGoods`
(
	`id` int(10) not null auto_increment,
	`category_id` int(10) not null,
	`category_lft` int(10) not null,
	`category_rgt` int(10) not null,
	`active` tinyint(1) default 1,
	`alias` varchar(100) default null,
	`title` varchar(100) default null,
	`description` text,
	primary key (`id`),
	foreign key (`category_id`) references `CatalogCategory` (`id`) on delete cascade on update cascade,
	key `alias` (`alias`)
)
