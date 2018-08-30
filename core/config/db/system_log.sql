-- ==================================================
-- Log Tables
-- ==================================================

--
-- Table structure for table `system_log_error`
--

CREATE TABLE `system_log_error` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`date_create` int(10) unsigned NOT NULL,
	`requestkey` varchar(8) NOT NULL,
	`id_person` int(5) unsigned NOT NULL,
	`level` tinyint(1) unsigned NOT NULL,
	`file` varchar(100) NOT NULL,
	`line` smallint(5) unsigned NOT NULL,
	`message` varchar(255) NOT NULL,
	`data` text NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `system_log_mail`
--

CREATE TABLE `system_log_email` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`date_create` int(10) unsigned NOT NULL,
	`date_update` int(10) unsigned NOT NULL,
	`id_person_create` int(10) unsigned NOT NULL,
	`ext` smallint(5) unsigned NOT NULL,
	`record_type` smallint(5) unsigned NOT NULL,
	`id_record` int(10) unsigned NOT NULL,
	`id_receiver` int(10) unsigned NOT NULL,
	`receiver_type` varchar(32) NOT NULL DEFAULT 'contactperson',
	PRIMARY KEY (`id`),
	KEY `record` (`id_record`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
