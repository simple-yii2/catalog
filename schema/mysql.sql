create table if not exists `CatalogSettings`
(
	`id` int(10) not null auto_increment,
	`defaultCurrency_id` int(10) default null,
	primary key (`id`)
) engine InnoDB;

create table if not exists `CatalogCurrency`
(
	`id` int(10) not null auto_increment,
	`name` varchar(100) not null,
	`code` varchar(10) not null,
	`rate` decimal(10,2) not null,
	`precision` integer default 2,
	`prefix` varchar(10) not null,
	`suffix` varchar(10) not null,
	primary key (`id`)
) engine InnoDB;

create table if not exists `CatalogVendor`
(
	`id` int(10) not null auto_increment,
	`alias` varchar(100) not null,
	`name` varchar(100) not null,
	`description` text,
	`url` varchar(200) default null,
	`file` varchar(200) default null,
	`thumb` varchar(200) default null,
	primary key (`id`),
	key `alias` (`alias`),
	key `name` (`name`)
) engine InnoDB;

create table if not exists `CatalogStore`
(
	`id` int(10) not null auto_increment,
	`type` int(10) not null,
	`name` varchar(100) not null,
	primary key (`id`)
) engine InnoDB;

create table if not exists `CatalogDelivery`
(
	`id` int(10) not null auto_increment,
	`name` varchar(100) not null,
	`cost` decimal(10,2) not null,
	`days` int(10) not null,
	primary key (`id`)
) engine InnoDB;

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
	`offerCount` int(10) not null,
	primary key (`id`),
	key `alias` (`alias`)
) engine InnoDB;

create table if not exists `CatalogCategoryProperty`
(
	`id` int(10) not null auto_increment,
	`category_id` int(10) not null,
	`alias` varchar(50) default null,
	`name` varchar(50) default null,
	`type` int(10) not null,
	`svalues` text,
	`unit` varchar(10) default null,
	primary key (`id`),
	foreign key (`category_id`) references `CatalogCategory` (`id`) on delete cascade on update cascade,
	key `alias` (`alias`)
) engine InnoDB;

create table if not exists `CatalogOffer`
(
	`id` int(10) not null auto_increment,
	`category_id` int(10) not null,
	`category_lft` int(10) not null,
	`category_rgt` int(10) not null,
	`vendor_id` int(10) default null,
	`currency_id` int(10) default null,
	`active` tinyint(1) default 1,
	`alias` varchar(100) default null,
	`name` varchar(100) default null,
	`model` varchar(100) default null,
	`description` text,
	`vendor` varchar(100) default null,
	`price` decimal(10,2) default null,
	`oldPrice` decimal(10,2) default null,
	`storeAvailable` tinyint(1) default 0,
	`pickupAvailable` tinyint(1) default 0,
	`deliveryAvailable` tinyint(1) default 0,
	`defaultDelivery` tinyint(1) default 1,
	`quantity` int(10) default 0,
	`countryOfOrigin` varchar(100) default null,
	`length` int(10) default null,
	`width` int(10) default null,
	`height` int(10) default null,
	`weight` decimal(10,3) default null,
	`modifyDate` datetime,
	`thumb` varchar(200) default null,
	`imageCount` int(10) not null,
	`priceValue` decimal(12, 4) default null,
	primary key (`id`),
	foreign key (`category_id`) references `CatalogCategory` (`id`) on delete cascade on update cascade,
	key `alias` (`alias`),
	key `filter` (`category_lft`, `category_rgt`, `vendor_id`, `price`)
) engine InnoDB;

create table if not exists `CatalogOfferBarcode`
(
	`id` int(10) not null auto_increment,
	`offer_id` int(10) not null,
	`barcode` varchar(50) not null,
	primary key (`id`),
	foreign key (`offer_id`) references `CatalogOffer` (`id`) on delete cascade on update cascade,
	key `barcode` (`barcode`)
) engine InnoDB;

create table if not exists `CatalogOfferDelivery`
(
	`id` int(10) not null auto_increment,
	`offer_id` int(10) not null,
	`delivery_id` int(10) not null,
	`cost` decimal(10,2) default null,
	`days` int(10) default null,
	primary key (`id`),
	foreign key (`offer_id`) references `CatalogOffer` (`id`) on delete cascade on update cascade,
	foreign key (`delivery_id`) references `CatalogDelivery` (`id`) on delete cascade on update cascade
) engine InnoDB;

create table if not exists `CatalogOfferProperty`
(
	`id` int(10) not null auto_increment,
	`offer_id` int(10) not null,
	`property_id` int(10) not null,
	`value` varchar(30) not null,
	primary key (`id`),
	foreign key (`offer_id`) references `CatalogOffer` (`id`) on delete cascade on update cascade,
	foreign key (`property_id`) references `CatalogCategoryProperty` (`id`) on delete cascade on update cascade,
	key `filter` (`offer_id`, `property_id`, `value`)
) engine InnoDB;

create table if not exists `CatalogOfferImage`
(
	`id` int(10) not null auto_increment,
	`offer_id` int(10) not null,
	`file` varchar(200) default null,
	`thumb` varchar(200) default null,
	primary key (`id`),
	foreign key (`offer_id`) references `CatalogOffer` (`id`) on delete cascade on update cascade
) engine InnoDB;

create table if not exists `CatalogOfferRecommended`
(
	`id` int(10) not null auto_increment,
	`offer_id` int(10) not null,
	`recommended_id` int(10) not null,
	primary key (`id`),
	foreign key (`offer_id`) references `CatalogOffer` (`id`) on delete cascade on update cascade,
	foreign key (`recommended_id`) references `CatalogOffer` (`id`) on delete cascade on update cascade
) engine InnoDB;

create table if not exists `CatalogStoreOffer`
(
	`id` int(10) not null auto_increment,
	`store_id` int(10) not null,
	`offer_id` int(10) not null,
	`quantity` int(10) not null,
	primary key (`id`),
	foreign key (`store_id`) references `CatalogStore` (`id`) on delete cascade on update cascade,
	foreign key (`offer_id`) references `CatalogOffer` (`id`) on delete cascade on update cascade
) engine InnoDB;
