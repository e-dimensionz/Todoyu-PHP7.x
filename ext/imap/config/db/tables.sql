--
-- Table structure for table `ext_imap_account`
--

CREATE TABLE `ext_imap_account` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`date_create` int(10) unsigned NOT NULL DEFAULT '0',
	`date_update` int(10) unsigned NOT NULL DEFAULT '0',
	`id_person_create` int(10) unsigned NOT NULL DEFAULT '0',
	`deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`is_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`host` varchar(100) NOT NULL,
	`username` varchar(100) NOT NULL,
	`password` varchar(100) NOT NULL,
	`port` int(10) unsigned NOT NULL DEFAULT '0',
	`folder` varchar(100) NOT NULL DEFAULT '',
	`delimiter` char(1) NOT NULL DEFAULT '',
	`use_starttls` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`use_ssl` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`cert_novalidate` tinyint(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ext_imap_address`
--

CREATE TABLE `ext_imap_address` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`date_create` int(10) unsigned NOT NULL DEFAULT '0',
	`date_update` int(10) unsigned NOT NULL DEFAULT '0',
	`id_person_create` int(10) unsigned NOT NULL DEFAULT '0',
	`deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`address` varchar(100) NOT NULL DEFAULT '',
	`name` varchar(100) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ext_imap_message` - imported emails
--

CREATE TABLE `ext_imap_message` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`date_create` int(10) unsigned NOT NULL DEFAULT '0',
	`date_update` int(10) unsigned NOT NULL DEFAULT '0',
	`id_person_create` int(10) unsigned NOT NULL DEFAULT '0',
	`deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`id_account` int(10) unsigned NOT NULL DEFAULT '0',
	`message_id` varchar(255) NOT NULL DEFAULT '',
	`date_sent` int(10) unsigned NOT NULL DEFAULT '0',
	`subject` varchar(255) NOT NULL DEFAULT '',
	`id_address_from` int(10) unsigned NOT NULL DEFAULT '0',
	`size` int(10) unsigned NOT NULL DEFAULT '0',
	`amount_attachments` int(10) unsigned NOT NULL DEFAULT '0',
	`message_plain` text NOT NULL,
	`message_html` mediumtext NOT NULL,
	`raw_message_key` varchar(48) NOT NULL DEFAULT '',
	KEY `messageid` (`id_account`,`message_id`),
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ext_imap_mm_message_address`
--

CREATE TABLE `ext_imap_mm_message_address` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`type` tinyint(3) unsigned NOT NULL DEFAULT '0',
	`id_message` int(10) unsigned NOT NULL,
	`id_address` int(10) unsigned NOT NULL,
	PRIMARY KEY (`id`),
	KEY `message` (`id_message`),
	KEY `address` (`id_address`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ext_imap_attachment`
--

CREATE TABLE `ext_imap_attachment` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`date_create` int(10) unsigned NOT NULL DEFAULT '0',
	`date_update` int(10) unsigned NOT NULL DEFAULT '0',
	`id_person_create` int(10) unsigned NOT NULL DEFAULT '0',
	`deleted` tinyint(1) NOT NULL DEFAULT '0',
	`id_message` int(10) unsigned NOT NULL DEFAULT '0',
	`file_ext` varchar(10) NOT NULL,
	`file_storage` varchar(255) NOT NULL,
	`file_name` varchar(255) NOT NULL,
	`file_size` int(10) unsigned NOT NULL DEFAULT '0',
	`file_mime` varchar(20) NOT NULL,
	`file_mime_sub` varchar(50) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `message` (`id_message`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;