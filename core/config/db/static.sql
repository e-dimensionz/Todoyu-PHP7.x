-- ==================================================
-- Static Data Tables
-- ==================================================

--
-- Table structure for table `static_country`
--

CREATE TABLE `static_country` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`iso_alpha2` char(2) NOT NULL,
	`iso_alpha3` char(3) NOT NULL,
	`iso_num` int(11) unsigned NOT NULL DEFAULT '0',
	`iso_num_currency` char(3) NOT NULL,
	`phone` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `static_country_zone`
--

CREATE TABLE `static_country_zone` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`iso_alpha2_country` char(2) NOT NULL,
	`iso_alpha3_country` char(3) NOT NULL,
	`iso_num_country` int(11) unsigned NOT NULL DEFAULT '0',
	`code` varchar(15) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `static_territory`
--

CREATE TABLE `static_territory` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`iso_num` int(11) unsigned NOT NULL DEFAULT '0',
	`parent_iso_num` int(11) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table  `static_timezone`
--

CREATE TABLE `static_timezone` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`timezone` varchar(50) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `static_language`
--

CREATE TABLE `static_language` (
	`id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
	`iso_alpha2` char(2) DEFAULT '' NOT NULL,
	`iso_alpha3` char(3) DEFAULT '' NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `alpha2` (`iso_alpha2`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;