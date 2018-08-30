--
-- Table structure for table `ext_sysmanager_extension`
--

CREATE TABLE `ext_sysmanager_extension` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`date_create` int(10) unsigned NOT NULL default '0',
	`date_update` int(10) unsigned NOT NULL,
	`ext` int(10) unsigned NOT NULL,
	`version` varchar(16) NOT NULL,
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ext_sysmanager_smtpaccount`
--

CREATE TABLE `ext_sysmanager_smtpaccount` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`date_create` int(10) unsigned NOT NULL DEFAULT '0',
	`date_update` int(10) unsigned NOT NULL DEFAULT '0',
	`id_person_create` int(10) unsigned NOT NULL DEFAULT '0',
	`deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`host` varchar(100) NOT NULL DEFAULT '',
	`port` int(10) unsigned NOT NULL DEFAULT '0',
	`authentication` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`username` varchar(100) NOT NULL DEFAULT '',
	`password` varchar(100) NOT NULL DEFAULT '',
	`forcename` varchar(100) NOT NULL DEFAULT '',
	`comment` text NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;