--
-- Table structure for table `ext_comment_comment`
--

CREATE TABLE `ext_comment_comment` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`date_create` int(10) unsigned NOT NULL DEFAULT '0',
	`date_update` int(10) unsigned NOT NULL DEFAULT '0',
	`id_person_create` int(10) unsigned NOT NULL DEFAULT '0',
	`id_person_update` int(10) unsigned NOT NULL DEFAULT '0',
	`deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`id_task` mediumint(9) unsigned NOT NULL DEFAULT '0',
	`comment` mediumtext NOT NULL,
	`is_public` tinyint(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `task` (`id_task`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------


--
-- Table structure for table `ext_comment_mm_comment_feedback`
--

CREATE TABLE `ext_comment_mm_comment_feedback` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`date_create` int(10) unsigned NOT NULL,
	`date_update` int(10) unsigned NOT NULL,
	`id_person_create` int(10) unsigned NOT NULL,
	`id_person_feedback` int(10) unsigned NOT NULL,
	`id_comment` int(10) unsigned NOT NULL,
	`is_seen` tinyint(1) unsigned NOT NULL,
	PRIMARY KEY (`id`),
	 KEY `comment` (`id_comment`),
	 KEY `personseen` (`id_person_feedback`,`is_seen`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------


--
-- Table structure for table `ext_comment_fallback`
--

CREATE TABLE IF NOT EXISTS `ext_comment_fallback` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`date_create` int(10) unsigned NOT NULL,
	`date_update` int(10) unsigned NOT NULL,
	`id_person_create` int(10) unsigned NOT NULL,
	`deleted` tinyint(1) unsigned NOT NULL,
	`title` varchar(255) NOT NULL DEFAULT '',
	`id_person_feedback` int(10) unsigned NOT NULL,
	`taskperson_feedback` varchar(10) NOT NULL,
	`id_role_feedback` int(10) unsigned NOT NULL,
	`id_person_email` int(10) unsigned NOT NULL,
	`taskperson_email` varchar(10) NOT NULL,
	`id_role_email` int(10) unsigned NOT NULL,
	`is_public` tinyint(1) unsigned NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------


--
-- Table structure for table `ext_comment_mm_comment_asset`
--

CREATE TABLE IF NOT EXISTS `ext_comment_mm_comment_asset` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`date_create` int(10) unsigned NOT NULL,
	`date_update` int(10) unsigned NOT NULL,
	`id_person_create` int(10) unsigned NOT NULL,
	`id_asset` int(10) unsigned NOT NULL,
	`id_comment` int(10) unsigned NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--  --  --  -- --------------------------------------------------------


--
-- Table structure for table `ext_project_project`
--

CREATE TABLE `ext_project_project` (
	`ext_comment_fallback` int(10) unsigned NOT NULL
);