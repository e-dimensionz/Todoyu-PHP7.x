--
-- Table structure for table `ext_bookmark_bookmark`
--

CREATE TABLE `ext_bookmark_bookmark` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`date_create` int(10) unsigned NOT NULL DEFAULT '0',
	`date_update` int(10) unsigned NOT NULL DEFAULT '0',
	`id_person_create` int(10) unsigned NOT NULL,
	`deleted` tinyint(1) NOT NULL,
	`type` tinyint(1) NOT NULL,
	`id_item` int(10) unsigned NOT NULL,
	`sorting` int(10) unsigned NOT NULL,
	`title` varchar(255) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `multi` (`id_person_create`,`id_item`,`type`,`deleted`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;