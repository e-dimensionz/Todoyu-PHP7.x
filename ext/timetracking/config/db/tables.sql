--
-- Table structure for table `ext_timetracking_track`
--

CREATE TABLE `ext_timetracking_track` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`date_create` int(10) unsigned NOT NULL default '0',
	`date_update` int(10) unsigned NOT NULL default '0',
	`id_person_create` int(10) unsigned NOT NULL default '0',
	`date_track` int(10) unsigned NOT NULL default '0',
	`id_task` int(10) unsigned NOT NULL default '0',
	`workload_tracked` int(10) unsigned NOT NULL default '0',
	`workload_chargeable` int(10) unsigned NOT NULL default '0',
	`comment` text NOT NULL,
	PRIMARY KEY  (`id`),
	 KEY `task` (`id_task`),
	 KEY `persondate` (`date_track`,`id_person_create`),
	 KEY `multi` (`id_person_create`,`date_track`,`id_task`,`date_create`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ext_timetracking_active`
--

CREATE TABLE `ext_timetracking_active` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`date_create` int(10) unsigned NOT NULL default '0',
	`date_update` int(10) unsigned NOT NULL default '0',
	`id_person_create` int(10) unsigned NOT NULL default '0',
	`id_task` int(10) unsigned NOT NULL default '0',
	PRIMARY KEY (`id`),
	 KEY `persondate` (`id_person_create`,`date_create`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Extend table `ext_project_taskpreset`
--

CREATE TABLE `ext_project_taskpreset` (
	`ext_timetracking_start_tracking` tinyint(1) unsigned NOT NULL default '0',
	`ext_timetracking_workload_done` int(10) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;