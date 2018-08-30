--
-- Table structure for table `ext_contact_person`
--

CREATE TABLE `ext_contact_person` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`date_create` int(10) unsigned NOT NULL DEFAULT '0',
	`date_update` int(10) unsigned NOT NULL DEFAULT '0',
	`id_person_create` smallint(5) unsigned NOT NULL DEFAULT '0',
	`deleted` tinyint(2) NOT NULL DEFAULT '0',
	`username` varchar(100) NOT NULL,
	`password` varchar(40) NOT NULL,
	`email` varchar(100) NOT NULL,
	`id_smtpaccount` smallint(5) unsigned NOT NULL DEFAULT '0',
	`is_admin` tinyint(1) NOT NULL DEFAULT '0',
	`is_dummy` tinyint(1) NOT NULL DEFAULT '0',
	`is_active` tinyint(1) NOT NULL DEFAULT '0',
	`firstname` varchar(64) NOT NULL,
	`lastname` varchar(64) NOT NULL,
	`shortname` varchar(11) NOT NULL,
	`salutation` varchar(1) NOT NULL,
	`title` varchar(64) NOT NULL,
	`birthday` date NOT NULL,
	`comment` text NOT NULL,
	`mail_signature` text NOT NULL,
	`locale_correspondence` varchar(5) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ext_contact_company`
--

CREATE TABLE `ext_contact_company` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`date_create` int(10) unsigned NOT NULL DEFAULT '0',
	`date_update` int(10) unsigned NOT NULL,
	`id_person_create` int(10) unsigned NOT NULL,
	`deleted` tinyint(2) NOT NULL DEFAULT '0',
	`title` tinytext NOT NULL,
	`shortname` tinytext NOT NULL,
	`date_enter` int(10) unsigned NOT NULL DEFAULT '0',
	`is_internal` tinyint(1) NOT NULL DEFAULT '0',
	`comment` text NOT NULL,
	`locale_correspondence` varchar(5) NOT NULL DEFAULT '',
	`is_notactive` tinyint(2) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ext_contact_address`
--

CREATE TABLE `ext_contact_address` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`date_create` int(10) unsigned NOT NULL DEFAULT '0',
	`date_update` int(10) unsigned NOT NULL,
	`id_person_create` int(10) unsigned NOT NULL,
	`deleted` tinyint(2) NOT NULL,
	`id_addresstype` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`id_country` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`id_holidayset` int(11) NOT NULL DEFAULT '0',
	`id_timezone` smallint(3) NOT NULL DEFAULT '0',
	`street` varchar(255) NOT NULL,
	`postbox` varchar(32) NOT NULL,
	`city` varchar(64) NOT NULL,
	`region` varchar(64) NOT NULL,
	`zip` varchar(10) NOT NULL,
	`comment` varchar(255) NOT NULL,
	`is_preferred` tinyint(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ext_contact_contactinfo`
--

CREATE TABLE `ext_contact_contactinfo` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`date_create` int(10) unsigned NOT NULL DEFAULT '0',
	`date_update` int(10) unsigned NOT NULL,
	`id_person_create` int(10) unsigned NOT NULL,
	`deleted` tinyint(1) NOT NULL DEFAULT '0',
	`id_contactinfotype` smallint(5) unsigned NOT NULL DEFAULT '0',
	`info` tinytext NOT NULL,
	`is_preferred` tinyint(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ext_contact_contactinfotype`
--

CREATE TABLE `ext_contact_contactinfotype` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`date_create` int(11) NOT NULL,
	`date_update` int(11) NOT NULL,
	`id_person_create` int(10) unsigned NOT NULL,
	`deleted` tinyint(1) NOT NULL DEFAULT '0',
	`category` smallint(5) unsigned NOT NULL,
	`key` varchar(32) NOT NULL,
	`title` varchar(64) NOT NULL,
	`is_public` tinyint(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ext_contact_jobtype`
--

CREATE TABLE `ext_contact_jobtype` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`date_create` int(11) NOT NULL,
	`date_update` int(11) NOT NULL,
	`id_person_create` int(10) unsigned NOT NULL,
	`deleted` tinyint(1) NOT NULL DEFAULT '0',
	`title` varchar(64) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ext_contact_mm_company_address`
--

CREATE TABLE `ext_contact_mm_company_address` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`id_company` int(10) unsigned NOT NULL DEFAULT '0',
	`id_address` int(10) unsigned NOT NULL,
	PRIMARY KEY (`id`),
	KEY `ref` (`id_company`,`id_address`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ext_contact_mm_company_contactinfo`
--

CREATE TABLE `ext_contact_mm_company_contactinfo` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`id_company` int(10) unsigned NOT NULL DEFAULT '0',
	`id_contactinfo` int(10) unsigned NOT NULL,
	PRIMARY KEY (`id`),
	KEY `ref` (`id_company`,`id_contactinfo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ext_contact_mm_company_person`
--

CREATE TABLE `ext_contact_mm_company_person` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`id_company` int(10) unsigned NOT NULL DEFAULT '0',
	`id_person` int(10) unsigned NOT NULL,
	`id_workaddress` int(10) unsigned NOT NULL,
	`id_jobtype` int(10) unsigned NOT NULL,
	PRIMARY KEY (`id`),
	KEY `ref` (`id_company`,`id_person`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ext_contact_mm_person_address`
--

CREATE TABLE `ext_contact_mm_person_address` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`id_person` smallint(5) unsigned NOT NULL DEFAULT '0',
	`id_address` int(10) unsigned NOT NULL,
	PRIMARY KEY (`id`),
	KEY `ref` (`id_person`,`id_address`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ext_contact_mm_person_contactinfo`
--

CREATE TABLE `ext_contact_mm_person_contactinfo` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`id_person` int(10) unsigned NOT NULL DEFAULT '0',
	`id_contactinfo` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `ref` (`id_person`,`id_contactinfo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ext_contact_mm_person_role`
--

CREATE TABLE `ext_contact_mm_person_role` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`id_person` int(10) unsigned NOT NULL DEFAULT '0',
	`id_role` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `ref` (`id_person`,`id_role`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;