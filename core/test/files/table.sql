--
-- Dummy file for unit tests
--


--
-- Table structure for table `system_role`
--

CREATE TABLE `tablename` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`date_create` int(10) unsigned NOT NULL DEFAULT '0',
	`date_update` int(10) unsigned NOT NULL,
	`id_person_create` int(10) unsigned NOT NULL,
	`deleted` tinyint(1) NOT NULL DEFAULT '0',
	`title` varchar(64) NOT NULL,
	`is_active` tinyint(1) NOT NULL DEFAULT '0',
	`description` text NOT NULL,
	`price` decimal(5,2) NOT NULL DEFAULT 0.0,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `system_preference`
--

CREATE TABLE `system_preference` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`id_person` int(10) unsigned NOT NULL,
	`ext` smallint(5) unsigned NOT NULL,
	`area` smallint(5) unsigned NOT NULL,
	`preference` varchar(50) NOT NULL,
	`item` mediumint(8) unsigned NOT NULL DEFAULT '0',
	`value` text NOT NULL,
	PRIMARY KEY (`id`),
	KEY `fast` (`id_person`,`ext`,`preference`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

