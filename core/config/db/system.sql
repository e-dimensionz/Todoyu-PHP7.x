-- ==================================================
-- System Tables
-- ==================================================

--
-- Table structure for table `system_role`
--

CREATE TABLE `system_role` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`date_create` int(10) unsigned NOT NULL DEFAULT '0',
	`date_update` int(10) unsigned NOT NULL,
	`id_person_create` int(10) unsigned NOT NULL,
	`deleted` tinyint(1) NOT NULL DEFAULT '0',
	`title` varchar(64) NOT NULL,
	`is_active` tinyint(1) NOT NULL DEFAULT '0',
	`description` text NOT NULL,
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

-- --------------------------------------------------------

--
-- Table structure for table `system_right`
--

CREATE TABLE `system_right` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`ext` smallint(5) unsigned NOT NULL DEFAULT '0',
	`right` tinytext NOT NULL,
	`id_role` tinyint(3) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `ext` (`ext`,`id_role`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `system_freeze`
--

CREATE TABLE IF NOT EXISTS `system_freeze` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`date_create` int(11) NOT NULL,
	`date_update` int(11) NOT NULL,
	`id_person_create` int(11) NOT NULL,
	`element_type` varchar(255) NOT NULL,
	`element_id` int(11) NOT NULL,
	`data` text NOT NULL,
	`hash` varchar(32) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `original` (`element_type`,`element_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `system_lock`
--

CREATE TABLE `system_lock` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`ext` smallint(6) NOT NULL,
	`table` varchar(60) NOT NULL,
	`id_record` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `tablerecord` (`table`,`id_record`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `system_scheduler`
--

CREATE TABLE IF NOT EXISTS `system_scheduler` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`date_execute` int(11) NOT NULL,
	`class` varchar(100) NOT NULL,
	`is_success` tinyint(1) NOT NULL,
	`message` varchar(255) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `latest` (`class`,`date_execute`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `system_token`
--

CREATE TABLE IF NOT EXISTS `system_token` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`date_create` int(11) NOT NULL,
	`date_update` int(11) NOT NULL,
	`id_person_create` int(11) NOT NULL,
	`id_person_owner` int(11) NOT NULL,
	`deleted` tinyint(1) NOT NULL DEFAULT '0',
	`ext` smallint(6) NOT NULL,
	`token_type` int(11) NOT NULL,
	`hash` varchar(32) NOT NULL,
	`callback_params` text NOT NULL,
  PRIMARY KEY (`id`),
) ENGINE=MyISAM DEFAULT CHARSET=utf8;