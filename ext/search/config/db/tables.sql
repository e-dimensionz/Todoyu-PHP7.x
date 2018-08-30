--
-- Table structure for table `ext_search_filtercondition`
--

CREATE TABLE `ext_search_filtercondition` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`date_create` int(10) unsigned NOT NULL DEFAULT '0',
	`date_update` int(10) unsigned NOT NULL,
	`id_person_create` int(10) unsigned NOT NULL DEFAULT '0',
	`deleted` tinyint(2) NOT NULL DEFAULT '0',
	`id_set` int(10) unsigned NOT NULL,
	`filter` varchar(64) NOT NULL,
	`value` varchar(100) NOT NULL,
	`is_negated` tinyint(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `id_set` (`id_set`,`deleted`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `ext_search_filterset`
-- current = currently active widgets per search tab + person
--

CREATE TABLE `ext_search_filterset` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`date_create` int(10) unsigned NOT NULL DEFAULT '0',
	`date_update` int(10) unsigned NOT NULL,
	`id_person_create` int(10) unsigned NOT NULL DEFAULT '0',
	`deleted` tinyint(2) NOT NULL DEFAULT '0',
	`sorting` int(10) unsigned NOT NULL,
	`is_hidden` tinyint(2) NOT NULL DEFAULT '0',
	`is_separator` tinyint(2) NOT NULL DEFAULT '0',
	`current` tinyint(2) NOT NULL DEFAULT '0',
	`roles` varchar(16) NOT NULL,
	`type` varchar(16) NOT NULL,
	`title` varchar(64) NOT NULL,
	`conjunction` varchar(3) NOT NULL,
	`resultsorting` varchar(255) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `createdelete` (`id_person_create`,`deleted`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;