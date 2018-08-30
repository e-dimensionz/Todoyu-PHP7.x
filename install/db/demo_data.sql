-- phpMyAdmin SQL Dump
-- version 2.11.8.1deb5+lenny3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Creation time: January 29. 2010 18:38
-- Server Version: 5.0.51
-- PHP-Version: 5.2.6-1+lenny4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

--
-- Data for Table `ext_bookmark_bookmark`
--

INSERT INTO `ext_bookmark_bookmark` (`id`, `date_create`, `date_update`, `id_person_create`, `deleted`, `type`, `id_item`) VALUES
(1, 1254233035, 0, 1, 0, 1, 2),
(2, 1264768629, 0, 1, 0, 1, 115),
(3, 1264779035, 0, 1, 0, 1, 60),
(4, 1264779042, 0, 1, 0, 1, 59),
(5, 1264779432, 0, 12, 0, 1, 145),
(6, 1264779489, 0, 18, 0, 1, 133),
(11, 1264779710, 0, 18, 0, 1, 60),
(12, 1264779712, 0, 18, 0, 1, 135);

-- --------------------------------------------------------

--
-- Data for Table `ext_calendar_event`
--

INSERT INTO `ext_calendar_event` (`id`, `date_create`, `date_update`, `id_person_create`, `deleted`, `id_project`, `id_task`, `eventtype`, `title`, `description`, `place`, `date_start`, `date_end`, `is_private`, `is_dayevent`) VALUES
(1, 1264697556, 1264697592, 1, 0, 0, 0, 1, 'My Reminder', '', '', 1264753800, 1264757400, 0, 0),
(2, 1264700473, 1264700482, 1, 0, 0, 0, 6, 'Meet and Greet', '', 'Long silver beach', 1266390000, 1266393600, 1, 1),
(3, 1264700497, 1264700497, 1, 0, 0, 0, 1, 'Another one', '', '', 1266390000, 1266393600, 0, 1),
(4, 1264778135, 1264778147, 17, 0, 0, 0, 4, 'Vacation', '02/08/10 - 02/12/10', '', 1265616000, 1265994000, 0, 0),
(5, 1264778302, 1264778302, 17, 0, 0, 0, 5, 'Office Cert', 'Certification Day', '', 1266658200, 1266661800, 0, 1),
(6, 1264778364, 1264778364, 17, 0, 0, 0, 6, 'Project Meeting', '', '', 1266418800, 1266433200, 0, 0);

-- --------------------------------------------------------

--
-- Data for Table `ext_calendar_holiday`
--

INSERT INTO `ext_calendar_holiday` (`id`, `date_create`, `date_update`, `id_person_create`, `deleted`, `date`, `title`, `description`, `workingtime`) VALUES
(39, 0, 0, 0, 0, 1230764400, 'Neujahr', '', 0),
(40, 0, 0, 0, 0, 1230850800, 'Berchtoldstag', '', 0),
(41, 0, 0, 0, 0, 1239314400, 'Karfreitag', '', 0),
(42, 0, 0, 0, 0, 1239573600, 'Ostermontag', '', 0),
(43, 0, 0, 0, 0, 1240178400, 'Sechseläuten', 'In Zürich Mittag frei', 240),
(44, 0, 0, 0, 0, 1241128800, 'Tag der Arbeit', '', 0),
(45, 0, 0, 0, 0, 1243807200, 'Pfingstmontag', '', 0),
(46, 0, 0, 0, 0, 1249077600, 'Nationalfeiertag', '', 0),
(47, 0, 0, 0, 0, 1252879200, 'Knabenschiessen', 'In Zürich am Mittag frei', 240),
(48, 0, 0, 0, 0, 1261609200, 'Heiligabend', 'ab Mittag frei', 240),
(49, 0, 0, 0, 0, 1261695600, 'Weihnachten', '', 0),
(51, 0, 0, 0, 0, 1261782000, 'Stephanstag', '', 0),
(52, 0, 0, 0, 0, 1262214000, 'Silvester', 'Ab Mittag frei', 240),
(54, 0, 0, 0, 0, 1258930800, 'Zibelemärit ', 'Mittag frei - nur Bern', 240),
(62, 0, 0, 0, 0, 1242856800, 'Auffahrt', '', 0),
(63, 0, 0, 0, 0, 1262300400, 'Neujahrstag', '', 0),
(64, 0, 0, 0, 0, 1270159200, 'Karfreitag', '', 0),
(65, 0, 0, 0, 0, 1270418400, 'Ostermontag', '', 0),
(66, 0, 0, 0, 0, 1272664800, 'Tag der Arbeit', '', 0),
(67, 0, 0, 0, 0, 1273701600, 'Auffahrt', '', 0),
(68, 0, 0, 0, 0, 1274652000, 'Pfingstmontag', '', 0),
(69, 0, 0, 0, 0, 1280613600, 'Nationalfeiertag Schweiz', '', 0),
(70, 0, 0, 0, 0, 1293231600, 'Weihnachten', '', 0),
(71, 0, 0, 0, 0, 1293318000, 'Stephanstag', '', 0),
(72, 0, 0, 0, 0, 1266102000, 'Valentinstag', '', 8),
(73, 0, 0, 0, 0, 1271628000, 'Sechseläuten', '', 0),
(74, 0, 0, 0, 0, 1273356000, 'Muttertag', '', 28800),
(75, 0, 0, 0, 0, 1293750000, 'Silvester', '', 4);

-- --------------------------------------------------------

--
-- Data for Table `ext_calendar_holidayset`
--

INSERT INTO `ext_calendar_holidayset` (`id`, `date_create`, `date_update`, `id_person_create`, `deleted`, `title`, `description`) VALUES
(1, 0, 1244290547, 0, 0, 'Zürich', 'Switzerland'),
(2, 0, 1244290548, 0, 0, 'Bern', 'Switzerland'),
(3, 0, 0, 0, 0, 'USA', 'United States'),
(4, 0, 0, 0, 0, 'Japan', 'Japan');

-- --------------------------------------------------------

--
-- Data for Table `ext_calendar_mm_event_person`
--

INSERT INTO `ext_calendar_mm_event_person` (`id`, `id_event`, `id_person`, `is_acknowledged`) VALUES
(8, 1, 17, 0),
(7, 1, 1, 1),
(12, 2, 6, 0),
(11, 2, 1, 1),
(13, 3, 1, 1),
(15, 4, 17, 1),
(16, 5, 17, 1),
(17, 6, 17, 1);

-- --------------------------------------------------------

--
-- Data for Table `ext_calendar_mm_holiday_holidayset`
--

INSERT INTO `ext_calendar_mm_holiday_holidayset` (`id`, `id_holiday`, `id_holidayset`) VALUES
(432, 70, 1),
(431, 72, 1),
(430, 66, 1),
(429, 66, 1),
(428, 71, 1),
(427, 75, 1),
(404, 52, 2),
(426, 73, 1),
(403, 51, 2),
(425, 68, 1),
(402, 49, 2),
(401, 48, 2),
(400, 54, 2),
(424, 65, 1),
(399, 46, 2),
(398, 45, 2),
(397, 62, 2),
(423, 63, 1),
(396, 44, 2),
(422, 69, 1),
(395, 42, 2),
(421, 74, 1),
(394, 41, 2),
(393, 40, 2),
(420, 64, 1),
(392, 39, 2),
(419, 67, 1),
(378, 48, 3),
(379, 42, 3),
(380, 40, 3),
(381, 41, 3),
(382, 52, 3),
(383, 49, 3),
(384, 42, 3),
(385, 40, 4),
(386, 48, 4),
(387, 45, 4),
(388, 52, 4),
(389, 52, 4),
(390, 42, 4),
(391, 49, 4);

-- --------------------------------------------------------

--
-- Data for Table `ext_comment_comment`
--

INSERT INTO `ext_comment_comment` (`id`, `date_update`, `date_create`, `deleted`, `id_person_create`, `id_task`, `comment`, `is_public`) VALUES
(1, 1254233694, 1254233694, 0, 1, 7, '<p>Hallo, schau dir das bitte mal an, wenn moeglich noch Heute</p>', 0),
(2, 1264692282, 1264692282, 0, 1, 61, '<p>hahah</p>', 0),
(3, 1264699613, 1264699613, 0, 1, 35, '<p>Hi Bob</p><p>can you start with this task a bit earlier?</p>', 0),
(4, 1264699947, 1264699947, 0, 17, 60, '<p>Any questions concerning this one?</p>', 0),
(5, 1264700306, 1264700028, 0, 17, 106, '<p>There seems to be a problem. Can you check the attachment to this task? And please send me a short feedback.</p>', 0),
(6, 1264700390, 1264700385, 0, 17, 95, '<p>thanks for the good job. seems to be a good decision.</p>', 0),
(7, 1264777276, 1264777276, 0, 18, 60, '<p>hey friedrich, please inform me when the task whereabouts have changed.</p>', 1),
(8, 1264778321, 1264778217, 0, 18, 134, '<p>please see the attached image, does this meet the specs?</p>', 0),
(9, 1264778497, 1264778497, 0, 18, 134, '<p>readme please.</p>', 0),
(10, 1264778585, 1264778585, 0, 12, 134, '<p>Thanks for the image. It''s ok so</p><p>Are you ready for the next step</p>', 0),
(11, 1264780345, 1264780345, 0, 1, 60, '<p>When can we start?</p>', 0),
(12, 1264780389, 1264780389, 0, 12, 116, '<p>There may come up a problem with this task. We should have a meeting before you start it. Please organize a meeting with all involved persons</p>', 0),
(13, 1264784917, 1264784917, 0, 1, 149, '<p>sssafd</p>', 0);

-- --------------------------------------------------------

--
-- Data for Table `ext_comment_mm_comment_feedback`
--

INSERT INTO `ext_comment_mm_comment_feedback` (`id`, `date_create`, `date_update`, `id_person_create`, `id_person_feedback`, `id_comment`, `is_seen`) VALUES
(1, 1254233694, 0, 1, 2, 1, 0),
(2, 1264699613, 0, 1, 1, 3, 0),
(3, 1264699947, 0, 17, 14, 4, 0),
(4, 1264699947, 1264777276, 17, 18, 4, 1),
(5, 1264699947, 0, 17, 17, 4, 0),
(6, 1264700306, 0, 17, 1, 5, 0),
(7, 1264700390, 0, 17, 1, 6, 0),
(8, 1264777276, 0, 18, 14, 7, 0),
(9, 1264778217, 0, 18, 14, 8, 0),
(10, 1264778217, 0, 18, 17, 8, 0),
(11, 1264778313, 1264778497, 18, 18, 8, 1),
(12, 1264778321, 0, 18, 14, 8, 0),
(13, 1264778321, 0, 18, 17, 8, 0),
(14, 1264778497, 1264778585, 18, 12, 9, 1),
(15, 1264778585, 0, 12, 18, 10, 0);

-- --------------------------------------------------------

--
-- Data for Table `ext_project_mm_project_person`
--

INSERT INTO `ext_project_mm_project_person` (`id`, `id_project`, `id_person`, `id_role`, `comment`) VALUES
(5, 2, 2, 2, ''),
(4, 2, 3, 1, ''),
(6, 2, 1, 1, ''),
(48, 4, 14, 1, ''),
(52, 5, 5, 4, ''),
(32, 6, 14, 1, ''),
(29, 7, 4, 4, ''),
(28, 7, 14, 1, ''),
(47, 4, 13, 4, ''),
(54, 8, 6, 4, ''),
(53, 8, 14, 1, ''),
(20, 9, 14, 1, ''),
(21, 9, 10, 4, ''),
(22, 10, 14, 1, ''),
(23, 10, 8, 4, ''),
(24, 11, 14, 1, ''),
(25, 11, 12, 4, ''),
(26, 12, 14, 1, ''),
(27, 12, 11, 4, ''),
(51, 5, 14, 1, ''),
(33, 6, 9, 4, ''),
(35, 13, 14, 1, ''),
(36, 13, 7, 4, ''),
(50, 5, 1, 2, 'Bob is a busy man...'),
(44, 14, 12, 1, ''),
(45, 14, 18, 3, ''),
(46, 14, 15, 2, ''),
(49, 4, 1, 1, '');

-- --------------------------------------------------------

--
-- Data for Table `ext_project_project`
--

INSERT INTO `ext_project_project` (`id`, `date_create`, `date_update`, `id_person_create`, `deleted`, `date_start`, `date_end`, `date_deadline`, `title`, `description`, `status`, `id_company`) VALUES
(1, 1246982959, 1246982959, 1, 0, 1246917600, 1264719600, 1264719600, 'My First Project', '<p>This is the first todoyu project.</p>', 3, 1),
(4, 1264668890, 1264779125, 1, 0, 1264633200, 1267225200, 1267484400, 'Relaunch Website', '<p>Whole process of relaunching the website</p>', 1, 17),
(5, 1264675342, 1264779633, 1, 0, 1264633200, 1267225200, 1267225200, 'Relaunch Website', '<p>Relaunch Website</p>', 5, 11),
(6, 1264675382, 1264681654, 1, 0, 1264633200, 1267225200, 1267225200, 'Relaunch Website', '<p>Relaunch Website</p>', 1, 13),
(7, 1264675419, 1264681911, 1, 0, 1264633200, 1267225200, 1267225200, 'Relaunch', '<p>Relaunch</p>', 3, 8),
(8, 1264678888, 1264780404, 1, 0, 1264633200, 1265324400, 1265324400, 'Hosting', '<p>Hosting</p>', 3, 16),
(9, 1264679088, 1264681991, 1, 0, 1264719600, 1267225200, 1267225200, 'Hosting', '<p>Hosting</p>', 5, 10),
(10, 1264679315, 1264681835, 1, 0, 1230764400, 1262214000, 1262214000, 'Monthly support', '<p>Monthly support</p>', 9, 14),
(11, 1264679825, 1264681779, 1, 0, 1264633200, 1267225200, 1267225200, 'Monthly support', '<p>Monthly support</p>', 8, 9),
(12, 1264680252, 1264681686, 1, 0, 1231110000, 1234479600, 1235084400, 'Redesign', '<p>Redesign</p>', 8, 12),
(13, 1264680885, 1264681744, 1, 0, 1243461600, 1248645600, 1248645600, 'Redesign', '<p>Redesign</p>', 9, 15),
(14, 1264777540, 1264777661, 12, 0, 1266793200, 1269558000, 1269558000, 'New Opensource Server', '<p>Build a new server for opensource projects</p>', 3, 15);

-- --------------------------------------------------------

--
-- Data for Table `ext_project_task`
--
INSERT INTO `ext_project_task` (`id`, `date_create`, `date_update`, `id_person_create`, `deleted`, `type`, `id_project`, `id_parenttask`, `title`, `description`, `id_person_assigned`, `id_person_owner`, `date_deadline`, `date_start`, `date_end`, `tasknumber`, `status`, `id_activity`, `estimated_workload`, `is_acknowledged`, `is_public`, `sorting`) VALUES
(8, 1264669127, 1264682012, 1, 0, 2, 4, 0, 'Hosting-Move', '<p>Hosting-Move</p>', 0, 14, 0, 0, 0, 1, 2, 0, 0, 1, 0, 0),
(9, 1264669127, 1264669958, 1, 0, 2, 4, 0, 'Conception', '<p>Conception</p>', 0, 14, 0, 0, 0, 2, 2, 0, 0, 1, 0, 1),
(10, 1264669127, 1264669965, 1, 0, 2, 4, 0, 'Design', '<p>Design</p>', 0, 14, 0, 0, 0, 3, 2, 0, 0, 1, 0, 2),
(11, 1264669127, 1264672090, 1, 0, 2, 4, 0, 'Developpment', '<p>Developpment</p>', 0, 14, 0, 0, 0, 4, 2, 0, 0, 1, 0, 3),
(12, 1264669127, 1264669891, 1, 0, 2, 4, 0, 'Testing & Quality Managmenet', '<p>Testing And Quality Managmenet</p>', 0, 14, 0, 0, 0, 5, 2, 0, 0, 1, 0, 3),
(13, 1264669127, 1264669896, 1, 0, 2, 4, 0, 'Bugs', '<p>Bugs</p>', 0, 14, 0, 0, 0, 6, 2, 0, 0, 1, 0, 4),
(14, 1264669127, 1264669916, 1, 0, 2, 4, 0, 'Change Requests', '<p>Change Requests</p>', 0, 14, 0, 0, 0, 7, 2, 0, 0, 1, 0, 5),
(15, 1264669127, 1264669975, 1, 0, 2, 4, 0, 'Short documentations', '<p>Short documentations</p>', 0, 14, 0, 0, 0, 8, 2, 0, 0, 1, 0, 6),
(17, 1264670270, 1264697332, 1, 0, 1, 4, 8, 'Copy the website', '<p>https://www.domain.com</p><p>User: administrators</p><p>PW: ******</p>', 3, 3, 1265361420, 1264670220, 1265275020, 10, 2, 1, 28800, 1, 0, 0),
(18, 1264670270, 1264670446, 1, 0, 1, 4, 8, 'Change DNS', '<p>Localisation of installation:</p><p>Domain name:</p>', 14, 14, 1265361420, 1265275020, 1265275020, 11, 2, 1, 3600, 1, 0, 1),
(20, 1264670578, 1264670578, 1, 0, 1, 4, 8, 'Checking the new website / migrate adjustments', '<p>Checking the new website / migrate adjustments</p>', 3, 3, 1265361720, 1265016120, 1265361720, 13, 2, 1, 14400, 0, 0, 3),
(21, 1264670728, 1264670737, 1, 0, 1, 4, 8, 'Documentation of adjustments', '<p>- moving again</p><p>- list with files, templates, sites, configs</p><p>- notice the necessary changes for working on our sever</p>', 3, 3, 1265361900, 1265361840, 1265361900, 14, 2, 1, 3600, 1, 0, 4),
(22, 1264670849, 1264670849, 1, 0, 1, 4, 9, 'Analysis', '<p>analysis of the necessary menu points</p>', 14, 14, 1265621220, 1265621160, 1265621160, 15, 2, 3, 21600, 0, 0, 0),
(23, 1264670952, 1264670952, 1, 0, 1, 4, 9, 'Technical Workshop', '<p>Basic is task 4.15</p><p>- specifications</p><p>- planing implementation</p>', 14, 14, 1265966880, 1265707680, 1265966880, 16, 2, 3, 28800, 0, 0, 1),
(24, 1264671036, 1264671036, 1, 0, 1, 4, 9, 'Designing Workshop', '<p>- Navigation</p><p>- Target audiance</p><p>- Design</p>', 14, 14, 1265967000, 1265707800, 1265967000, 17, 2, 3, 18000, 0, 0, 2),
(25, 1264671334, 1264672573, 1, 0, 1, 4, 10, 'Screendesign', '<p>- Two proposals</p><p>- Extensions</p><p>- Corrections</p>', 14, 14, 1266571860, 1265967060, 1266571860, 18, 2, 3, 72000, 1, 0, 0),
(26, 1264672051, 1264672051, 1, 0, 1, 4, 10, 'Header Images', '<p>Chosse some fitting images for the header, one per section</p>', 14, 14, 1265968020, 1265622420, 1265968020, 19, 2, 3, 7200, 0, 0, 1),
(28, 1264672232, 1264672588, 1, 0, 1, 4, 11, 'Picture gallery', '<p>Installation of pic-gallery</p>', 14, 14, 1266573000, 1266227400, 1266573000, 21, 2, 2, 14400, 1, 0, 1),
(29, 1264672492, 1264672583, 1, 0, 1, 4, 11, 'Calendar', '<p>iplementing the calendar</p>', 14, 14, 1266573240, 1266227640, 1266573240, 22, 2, 2, 10800, 1, 0, 2),
(30, 1264672719, 1264672719, 1, 0, 1, 4, 11, 'Rootline', '<p>Installation of the rootline and adjusting to the design</p>', 14, 14, 1266573480, 1266227880, 1266573480, 23, 2, 2, 1800, 0, 0, 3),
(31, 1264672812, 1264672812, 1, 0, 1, 4, 11, 'Multilingualism', '<p>- German</p><p>- French</p><p>- English</p><p>- Italian</p>', 14, 14, 1266573540, 1266227940, 1266573540, 24, 2, 2, 7200, 0, 0, 4),
(32, 1264672895, 1264672895, 1, 0, 1, 4, 11, 'Search', '<p>- Full search</p><p>- Indexed search</p>', 14, 14, 1266573660, 1266228060, 1266573660, 25, 2, 2, 3600, 0, 0, 5),
(33, 1264673012, 1264699549, 1, 0, 1, 4, 12, 'Testing I', '<p>Testing I</p>', 1, 1, 1266919320, 1266832920, 1266919320, 26, 2, 4, 18000, 0, 0, 0),
(34, 1264673012, 1264779932, 1, 0, 1, 4, 12, 'Testing II', '<p>Testing II</p>', 1, 1, 1266919320, 1266832920, 1266919320, 27, 2, 4, 10800, 1, 0, 1),
(35, 1264673012, 1264699622, 1, 0, 1, 4, 12, 'Testing Design', '<p>Testing Design</p>', 1, 1, 1266919320, 1266832920, 1266919320, 28, 2, 4, 10800, 1, 0, 2),
(36, 1264673156, 1264779902, 1, 0, 1, 4, 13, 'Bugfixing', '<p>Bugfixing</p>', 1, 1, 1267178700, 1267005900, 1267178700, 29, 3, 4, 36000, 1, 0, 0),
(37, 1264675482, 1264675511, 1, 0, 2, 7, 0, 'Conception', '<p>Concpetion</p>', 0, 14, 0, 0, 0, 1, 2, 0, 0, 1, 0, 0),
(38, 1264675522, 1264676282, 1, 0, 2, 7, 0, 'Production', '<p>Production</p>', 0, 14, 0, 0, 0, 2, 2, 0, 0, 1, 0, 1),
(42, 1264675605, 1264678451, 1, 0, 2, 7, 0, 'Individual extensions', '<p>Individual extensions</p>', 0, 14, 0, 0, 0, 6, 2, 0, 0, 1, 0, 5),
(43, 1264675618, 1264678455, 1, 0, 2, 7, 0, 'Change Requests', '<p>Change Requests</p>', 0, 2, 0, 0, 0, 7, 2, 0, 0, 1, 0, 6),
(44, 1264675632, 1264678551, 1, 0, 2, 7, 0, 'Bugs', '<p>Bugs</p>', 0, 14, 0, 0, 0, 8, 2, 0, 0, 1, 0, 7),
(45, 1264675644, 1264675644, 1, 0, 2, 7, 0, 'Coaching', '<p>Coaching</p>', 0, 14, 0, 0, 0, 9, 2, 0, 0, 0, 0, 8),
(46, 1264675660, 1264675660, 1, 0, 2, 7, 0, 'Hosting', '<p>Hosting</p>', 0, 14, 0, 0, 0, 10, 2, 0, 0, 0, 0, 9),
(47, 1264676172, 1264681920, 1, 0, 1, 7, 37, 'Workshop', '<p>Zurich, 01.02.2011</p>', 2, 2, 1265108100, 1265021760, 1265108100, 11, 5, 1, 16200, 0, 0, 0),
(48, 1264676261, 1264681923, 1, 0, 1, 7, 37, 'Conception', '<p>Conception</p>', 2, 2, 1265108220, 1264676220, 1265108220, 12, 5, 1, 14400, 0, 0, 1),
(49, 1264676411, 1264681929, 1, 0, 1, 7, 38, 'Production', '<p>Production</p>', 2, 2, 1266404280, 1265194680, 1266404280, 13, 3, 2, 72000, 0, 0, 0),
(50, 1264676484, 1264676484, 1, 0, 1, 7, 38, 'Testing', '<p>Testing</p>', 14, 14, 1266577200, 1266404400, 1266577200, 14, 2, 4, 18000, 0, 0, 1),
(51, 1264678450, 1264678450, 1, 0, 1, 7, 42, 'Search', '<p>Search functions</p>', 2, 2, 1266579180, 1266233580, 1266579180, 15, 2, 2, 7200, 0, 0, 0),
(52, 1264678488, 1264678488, 1, 0, 1, 7, 43, 'Reserver CR Template', '<p>Reserver CR Template</p>', 14, 14, 1266579240, 1266233640, 1266579240, 16, 2, 2, 7200, 0, 0, 0),
(53, 1264678541, 1264678541, 1, 0, 1, 7, 43, 'Reserver CR Extensions', '<p>Reserver CR Extensions</p>', 14, 14, 1266579300, 1266233700, 1266579300, 17, 2, 2, 7200, 0, 0, 1),
(54, 1264678591, 1264678591, 1, 0, 1, 7, 44, 'Template Bugs', '<p>Template Bugs</p>', 2, 2, 1267011360, 1266838560, 1267011360, 18, 2, 4, 7200, 0, 0, 0),
(55, 1264678634, 1264678634, 1, 0, 1, 7, 44, 'Application Bugs', '<p>Application Bugs</p>', 14, 14, 1267011420, 1266838560, 1267011360, 19, 2, 4, 7200, 0, 0, 1),
(56, 1264678685, 1264678685, 1, 0, 1, 7, 45, 'Coaching', '<p>Coaching</p>', 14, 14, 1267789020, 1267443420, 1267789020, 20, 2, 1, 28800, 0, 0, 0),
(57, 1264678726, 1264678726, 1, 0, 1, 7, 46, 'Domain registration', '<p>Domain registration</p>', 14, 14, 1266579480, 1266579480, 1266579480, 21, 2, 2, 1800, 0, 0, 0),
(58, 1264678766, 1264678783, 1, 0, 1, 7, 46, 'Transfer', '<p>Transfer</p>', 2, 2, 1266579540, 1266579540, 1266579540, 22, 2, 2, 7200, 0, 0, 0),
(59, 1264678939, 1269513466, 1, 0, 1, 8, 0, 'Domain Registration', '<p>Domain Registration</p>', 14, 14, 1265024460, 1265024460, 1265024460, 1, 3, 2, 1800, 1, 0, 0),
(60, 1264678987, 1264780327, 1, 0, 1, 8, 0, 'Data transfer', '<p>Data transfer</p>', 14, 14, 1265370120, 1265110920, 1265197320, 2, 3, 2, 14400, 1, 0, 0),
(61, 1264679429, 1264692273, 1, 0, 1, 10, 0, 'C&C 01/09', '<p>Communication and clarification</p>', 14, 14, 1233402600, 1230810600, 1233402600, 1, 8, 1, 7200, 1, 0, 0),
(62, 1264679429, 1264681846, 1, 0, 1, 10, 0, 'C&C 02/09', '<p>Communication and clarification</p>', 14, 14, 1235821800, 1233575400, 1235821800, 2, 8, 1, 7200, 1, 0, 1),
(64, 1264679429, 1264681850, 1, 0, 1, 10, 0, 'C&C 03/09', '<p>Communication and clarification</p>', 14, 14, 1238496600, 1235908200, 1238496600, 4, 8, 1, 7200, 0, 0, 2),
(65, 1264679429, 1264681855, 1, 0, 1, 10, 0, 'C&C 04/09', '<p>Communication and clarification</p>', 14, 14, 1241088600, 1238583000, 1241088600, 5, 8, 1, 7200, 0, 0, 3),
(66, 1264679429, 1264681859, 1, 0, 1, 10, 0, 'C&C 05/09', '<p>Communication and clarification</p>', 14, 14, 1243767000, 1241175000, 1243767000, 6, 8, 1, 7200, 0, 0, 4),
(69, 1264679429, 1264681864, 1, 0, 1, 10, 0, 'C&C 08/09', '<p>Communication and clarification</p>', 14, 14, 1251715800, 1249123800, 1251715800, 9, 8, 1, 7200, 0, 0, 4),
(73, 1264680349, 1264681691, 1, 0, 1, 12, 0, 'Proposal work out', '<p>Proposal work out</p>', 14, 14, 1232021100, 1231110000, 1232021100, 1, 8, 3, 28800, 1, 0, 0),
(74, 1264680403, 1264681695, 1, 0, 1, 12, 0, 'Adjust template', '<p>adjusting</p>', 14, 14, 1233144360, 1232021160, 1233144360, 2, 8, 3, 14400, 0, 0, 1),
(75, 1264680642, 1264680667, 1, 0, 2, 11, 0, 'Monthly support', '<p>Monthly support</p>', 0, 14, 0, 0, 0, 1, 2, 0, 0, 1, 0, 0),
(76, 1264679429, 1264681792, 1, 0, 1, 11, 75, 'C&C 01/09', '<p>Communication and clarification</p>', 14, 14, 1233402600, 1230810600, 1233402600, 2, 8, 1, 7200, 1, 0, 0),
(77, 1264679429, 1264681795, 1, 0, 1, 11, 75, 'C&C 02/09', '<p>Communication and clarification</p>', 14, 14, 1235821800, 1233575400, 1235821800, 3, 8, 1, 7200, 1, 0, 1),
(78, 1264679429, 1264681810, 1, 0, 1, 11, 75, 'C&C 03/09', '<p>Communication and clarification</p>', 14, 14, 1238496600, 1235908200, 1238496600, 4, 8, 1, 7200, 0, 0, 2),
(79, 1264679429, 1264681812, 1, 0, 1, 11, 75, 'C&C 04/09', '<p>Communication and clarification</p>', 14, 14, 1241088600, 1238583000, 1241088600, 5, 8, 1, 7200, 0, 0, 3),
(80, 1264679429, 1264681815, 1, 0, 1, 11, 75, 'C&C 05/09', '<p>Communication and clarification</p>', 14, 14, 1243767000, 1241175000, 1243767000, 6, 8, 1, 7200, 0, 0, 4),
(81, 1264679429, 1264681818, 1, 0, 1, 11, 75, 'C&C 08/09', '<p>Communication and clarification</p>', 14, 14, 1251715800, 1249123800, 1251715800, 7, 8, 1, 7200, 0, 0, 4),
(82, 1264680947, 1264680947, 1, 0, 2, 13, 0, 'Redesign', '<p>Redesign</p>', 0, 14, 0, 0, 0, 1, 2, 0, 0, 0, 0, 0),
(83, 1264680349, 1264681749, 1, 0, 1, 13, 82, 'Proposal work out', '<p>Proposal work out</p>', 14, 14, 1232021100, 1231110000, 1232021100, 2, 6, 3, 28800, 0, 0, 0),
(84, 1264680403, 1264681754, 1, 0, 1, 13, 82, 'Adjust template', '<p>adjusting</p>', 14, 14, 1233144360, 1232021160, 1233144360, 3, 6, 3, 14400, 0, 0, 1),
(85, 1264680985, 1264680985, 1, 0, 2, 9, 0, 'Hosting', '<p>Hosting</p>', 0, 14, 0, 0, 0, 1, 2, 0, 0, 0, 0, 1),
(86, 1264678939, 1264681988, 1, 0, 1, 9, 85, 'Domain Registration', '<p>Domain Registration</p>', 14, 14, 1265024460, 1265024460, 1265024460, 2, 5, 2, 1800, 0, 0, 0),
(87, 1264678987, 1264779863, 1, 0, 1, 9, 85, 'Data transfer', '<p>Data transfer</p>', 17, 17, 1265370120, 1265110920, 1265197320, 3, 3, 2, 14400, 1, 0, 1),
(88, 1264681137, 1264681486, 1, 0, 2, 5, 0, 'Relaunch 2011', '<p>Relaunch 2011</p>', 0, 3, 0, 0, 0, 1, 2, 0, 0, 1, 0, 0),
(89, 1264669127, 1264681395, 1, 0, 2, 5, 88, 'Hosting-Move', '<p>Hosting-Move</p>', 0, 14, 0, 0, 0, 2, 2, 0, 0, 1, 0, 0),
(90, 1264670270, 1264697772, 1, 0, 1, 5, 89, 'Copy the website', '<p>https://www.domain.com</p><p>User: administrators</p><p>PW: ******</p>', 3, 3, 1265361420, 1264670220, 1265275020, 3, 5, 1, 28800, 1, 0, 0),
(91, 1264670270, 1264681459, 1, 0, 1, 5, 89, 'Change DNS', '<p>Localisation of installation:</p><p>Domain name:</p>', 14, 14, 1265361420, 1265275020, 1265275020, 4, 5, 1, 3600, 1, 0, 1),
(92, 1264670578, 1264681537, 1, 0, 1, 5, 89, 'Checking the new website / migrate adjustments', '<p>Checking the new website / migrate adjustments</p>', 3, 3, 1265361720, 1265016120, 1265361720, 5, 5, 1, 14400, 1, 0, 3),
(93, 1264670728, 1264681533, 1, 0, 1, 5, 89, 'Documentation of adjustments', '<p>- moving again</p><p>- list with files, templates, sites, configs</p><p>- notice the necessary changes for working on our sever</p>', 3, 3, 1265361900, 1265361840, 1265361900, 6, 5, 1, 3600, 1, 0, 4),
(94, 1264669127, 1264681171, 1, 0, 2, 5, 88, 'Conception', '<p>Conception</p>', 0, 14, 0, 0, 0, 7, 2, 0, 0, 1, 0, 1),
(95, 1264670849, 1264778151, 1, 0, 1, 5, 94, 'Analysis', '<p>analysis of the necessary menu points</p>', 14, 14, 1265621220, 1265621160, 1265621160, 8, 5, 3, 21600, 1, 0, 0),
(96, 1264670952, 1264681510, 1, 0, 1, 5, 94, 'Technical Workshop', '<p>Basic is task 4.15</p><p>- specifications</p><p>- planing implementation</p>', 14, 14, 1265966880, 1265707680, 1265966880, 9, 5, 3, 28800, 0, 0, 1),
(97, 1264671036, 1264778299, 1, 0, 1, 5, 94, 'Designing Workshop', '<p>- Navigation</p><p>- Target audiance</p><p>- Design</p>', 14, 14, 1265967000, 1265707800, 1265967000, 10, 5, 3, 18000, 1, 0, 2),
(98, 1264669127, 1264681184, 1, 0, 2, 5, 88, 'Design', '<p>Design</p>', 0, 14, 0, 0, 0, 11, 2, 0, 0, 1, 0, 2),
(99, 1264671334, 1264681524, 1, 0, 1, 5, 98, 'Screendesign', '<p>- Two proposals</p><p>- Extensions</p><p>- Corrections</p>', 14, 14, 1266571860, 1265967060, 1266571860, 12, 5, 3, 72000, 1, 0, 0),
(100, 1264672051, 1264681529, 1, 0, 1, 5, 98, 'Header Images', '<p>Chosse some fitting images for the header, one per section</p>', 14, 14, 1265968020, 1265622420, 1265968020, 13, 5, 3, 7200, 0, 0, 1),
(101, 1264669127, 1264681194, 1, 0, 2, 5, 88, 'Developpment', '<p>Developpment</p>', 0, 14, 0, 0, 0, 14, 2, 0, 0, 1, 0, 3),
(102, 1264672232, 1264681600, 1, 0, 1, 5, 101, 'Picture gallery', '<p>Installation of pic-gallery</p>', 14, 14, 1266573000, 1266227400, 1266573000, 15, 5, 2, 14400, 1, 0, 1),
(103, 1264672492, 1264778302, 1, 0, 1, 5, 101, 'Calendar', '<p>iplementing the calendar</p>', 17, 17, 1266573240, 1266227640, 1266573240, 16, 5, 2, 10800, 1, 0, 2),
(104, 1264672719, 1264681606, 1, 0, 1, 5, 101, 'Rootline', '<p>Installation of the rootline and adjusting to the design</p>', 14, 14, 1266573480, 1266227880, 1266573480, 17, 5, 2, 1800, 0, 0, 3),
(105, 1264672812, 1264681611, 1, 0, 1, 5, 101, 'Multilingualism', '<p>- German</p><p>- French</p><p>- English</p><p>- Italian</p>', 14, 14, 1266573540, 1266227940, 1266573540, 18, 5, 2, 7200, 0, 0, 4),
(106, 1264672895, 1264780124, 1, 0, 1, 5, 101, 'Search', '<p>- Full search</p><p>- Indexed search</p>', 14, 14, 1266573660, 1266228060, 1266573660, 19, 5, 2, 3600, 1, 0, 5),
(107, 1264669127, 1264681215, 1, 0, 2, 5, 88, 'Testing & Quality Managmenet', '<p>Testing And Quality Managmenet</p>', 0, 14, 0, 0, 0, 20, 2, 0, 0, 1, 0, 3),
(108, 1264673012, 1264681620, 1, 0, 1, 5, 107, 'Testing I', '<p>Testing I</p>', 14, 14, 1266919320, 1266832920, 1266919320, 21, 5, 4, 18000, 0, 0, 0),
(109, 1264673012, 1264681624, 1, 0, 1, 5, 107, 'Testing II', '<p>Testing II</p>', 14, 14, 1266919320, 1266832920, 1266919320, 22, 5, 4, 10800, 0, 0, 1),
(110, 1264673012, 1264681630, 1, 0, 1, 5, 107, 'Testing Design', '<p>Testing Design</p>', 14, 14, 1266919320, 1266832920, 1266919320, 23, 5, 4, 10800, 0, 0, 2),
(111, 1264669127, 1264681229, 1, 0, 2, 5, 88, 'Bugs', '<p>Bugs</p>', 0, 14, 0, 0, 0, 24, 2, 0, 0, 1, 0, 4),
(112, 1264673156, 1264681634, 1, 0, 1, 5, 111, 'Bugfixing', '<p>Bugfixing</p>', 14, 14, 1267178700, 1267005900, 1267178700, 25, 5, 4, 36000, 0, 0, 0),
(113, 1264681255, 1264681255, 1, 0, 2, 6, 0, 'Relaunch 2011', '<p>Relaunch 2011</p>', 0, 14, 0, 0, 0, 1, 2, 0, 0, 0, 0, 0),
(114, 1264675482, 1264681277, 1, 0, 2, 6, 113, 'Conception', '<p>Concpetion</p>', 0, 14, 0, 0, 0, 2, 2, 0, 0, 1, 0, 0),
(115, 1264676172, 1264768631, 1, 0, 1, 6, 114, 'Workshop', '<p>Zurich, 01.02.2011</p>', 2, 2, 1265108100, 1265021760, 1265108100, 3, 3, 1, 16200, 0, 0, 0),
(116, 1264676261, 1264780320, 1, 0, 1, 6, 114, 'Conception', '<p>Conception</p>', 2, 2, 1265108220, 1264676220, 1265108220, 4, 2, 1, 14400, 1, 0, 1),
(117, 1264675522, 1264681286, 1, 0, 2, 6, 113, 'Production', '<p>Production</p>', 0, 14, 0, 0, 0, 5, 2, 0, 0, 1, 0, 1),
(118, 1264676411, 1264681286, 1, 0, 1, 6, 117, 'Production', '<p>Production</p>', 2, 2, 1266404280, 1265194680, 1266404280, 6, 2, 2, 72000, 0, 0, 0),
(119, 1264676484, 1264681286, 1, 0, 1, 6, 117, 'Testing', '<p>Testing</p>', 14, 14, 1266577200, 1266404400, 1266577200, 7, 2, 4, 18000, 0, 0, 1),
(120, 1264675605, 1264681296, 1, 0, 2, 6, 113, 'Individual extensions', '<p>Individual extensions</p>', 0, 14, 0, 0, 0, 8, 2, 0, 0, 1, 0, 5),
(121, 1264678450, 1264681296, 1, 0, 1, 6, 120, 'Search', '<p>Search functions</p>', 2, 2, 1266579180, 1266233580, 1266579180, 9, 2, 2, 7200, 0, 0, 0),
(122, 1264675618, 1264681310, 1, 0, 2, 6, 113, 'Change Requests', '<p>Change Requests</p>', 0, 2, 0, 0, 0, 10, 2, 0, 0, 1, 0, 6),
(123, 1264678488, 1264681310, 1, 0, 1, 6, 122, 'Reserver CR Template', '<p>Reserver CR Template</p>', 14, 14, 1266579240, 1266233640, 1266579240, 11, 2, 2, 7200, 0, 0, 0),
(124, 1264678541, 1264681310, 1, 0, 1, 6, 122, 'Reserver CR Extensions', '<p>Reserver CR Extensions</p>', 14, 14, 1266579300, 1266233700, 1266579300, 12, 2, 2, 7200, 0, 0, 1),
(125, 1264675632, 1264681324, 1, 0, 2, 6, 113, 'Bugs', '<p>Bugs</p>', 0, 14, 0, 0, 0, 13, 2, 0, 0, 1, 0, 7),
(126, 1264678591, 1264681324, 1, 0, 1, 6, 125, 'Template Bugs', '<p>Template Bugs</p>', 2, 2, 1267011360, 1266838560, 1267011360, 14, 2, 4, 7200, 0, 0, 0),
(127, 1264678634, 1264681324, 1, 0, 1, 6, 125, 'Application Bugs', '<p>Application Bugs</p>', 14, 14, 1267011420, 1266838560, 1267011360, 15, 2, 4, 7200, 0, 0, 1),
(128, 1264675644, 1264681337, 1, 0, 2, 6, 113, 'Coaching', '<p>Coaching</p>', 0, 14, 0, 0, 0, 16, 2, 0, 0, 0, 0, 8),
(129, 1264678685, 1264681337, 1, 0, 1, 6, 128, 'Coaching', '<p>Coaching</p>', 14, 14, 1267789020, 1267443420, 1267789020, 17, 2, 1, 28800, 0, 0, 0),
(130, 1264675660, 1264681348, 1, 0, 2, 6, 113, 'Hosting', '<p>Hosting</p>', 0, 14, 0, 0, 0, 18, 2, 0, 0, 0, 0, 9),
(131, 1264678726, 1264681348, 1, 0, 1, 6, 130, 'Domain registration', '<p>Domain registration</p>', 14, 14, 1266579480, 1266579480, 1266579480, 19, 2, 2, 1800, 0, 0, 0),
(132, 1264678766, 1264681348, 1, 0, 1, 6, 130, 'Transfer', '<p>Transfer</p>', 2, 2, 1266579540, 1266579540, 1266579540, 20, 2, 2, 7200, 0, 0, 0),
(133, 1264777846, 1264779482, 12, 0, 2, 14, 0, 'Todo before development', '<p>Things we have to check before we start writing any code</p>', 0, 14, 0, 0, 0, 1, 2, 0, 0, 1, 0, 0),
(134, 1264777926, 1264779814, 12, 0, 1, 14, 133, 'Check existing products', '<p>Try to find other solutions which may have a solution for the same problem</p>', 18, 14, 1267197060, 1266851460, 0, 2, 3, 1, 25200, 1, 0, 0),
(135, 1264777926, 1264779846, 12, 0, 1, 14, 133, 'Analyse other solutions', '<p>Compare the pros and cons of the other products</p>', 18, 17, 1267715460, 1267456260, 0, 3, 3, 1, 18900, 1, 0, 1),
(136, 1264777926, 1264779818, 12, 0, 1, 14, 133, 'Find valuable market segment', '<p>Who would buy this product?</p>', 18, 1, 1268061060, 1267456260, 0, 4, 3, 1, 25200, 1, 0, 2),
(137, 1264778378, 1264779502, 12, 0, 2, 14, 0, 'Production', '<p>Coding</p>', 0, 12, 0, 0, 0, 5, 2, 0, 0, 1, 0, 1),
(138, 1264678987, 1264778482, 1, 0, 1, 9, 87, 'Data preparation for transfer', '<p>Data transfer preps</p><ol><li>Prepare</li><li>Check</li><li>Fix</li><li>Go!</li></ol>', 17, 17, 1265370120, 1265110920, 1265197320, 4, 3, 2, 14400, 1, 0, 1),
(139, 1264675618, 1264778531, 1, 0, 2, 9, 0, 'Change Requests', '<p>Change Requests</p>', 0, 2, 0, 0, 0, 5, 2, 0, 0, 1, 0, 0),
(140, 1264678488, 1264778531, 1, 0, 1, 9, 139, 'Reserver CR Template', '<p>Reserver CR Template</p>', 14, 14, 1266579240, 1266233640, 1266579240, 6, 2, 2, 7200, 0, 0, 0),
(141, 1264678541, 1264778531, 1, 0, 1, 9, 139, 'Reserver CR Extensions', '<p>Reserver CR Extensions</p>', 14, 14, 1266579300, 1266233700, 1266579300, 7, 2, 2, 7200, 0, 0, 1),
(142, 1264778614, 1264778614, 17, 0, 1, 4, 15, 'Create Docu in wiki', '<p>Just do it...</p>', 3, 3, 1267197780, 1265037720, 1267197720, 30, 2, 6, 8400, 0, 0, 0),
(143, 1264778792, 1264778792, 17, 0, 1, 4, 142, 'Another documentation for the customer', '<p>use OpenOffice</p>', 3, 3, 1275056760, 1269617160, 1272637560, 31, 2, 6, 9000, 0, 0, 0),
(144, 1264779082, 1264779096, 12, 0, 1, 5, 0, 'This task was added really quick', '<p>A very quick task</p>', 12, 12, 1265383882, 1264779082, 1265383882, 26, 3, 7, 3600, 1, 0, 1),
(145, 1264779147, 1264779561, 12, 0, 1, 5, 0, 'Buy milk and bread', '<p>And some sugar</p>', 12, 12, 1265383947, 1264779147, 1265383947, 27, 3, 3, 3600, 1, 0, 2),
(146, 1264779341, 1264779751, 12, 0, 1, 14, 137, 'Build Framework', '<p>Write framework code and structure</p>', 14, 14, 1270045200, 1268666400, 0, 6, 3, 6, 7200, 1, 0, 0),
(147, 1264779341, 1264779595, 12, 0, 1, 5, 145, 'Build Framework', '<p>Write framework code and structure</p>', 14, 14, 1270045200, 1268666400, 0, 28, 2, 6, 7200, 0, 0, 1),
(148, 1264779631, 1264779817, 18, 0, 1, 14, 0, 'Write enduser documentation', '<p>after finishing bugfixing the application, start planning the documentation roadmap and timeline</p>', 18, 18, 1265384431, 1264779631, 1265384431, 7, 3, 7, 3600, 1, 0, 2),
(149, 1264780692, 1264780717, 1, 0, 1, 8, 0, 'What?', '<p>Whaaaaat</p>', 14, 14, 1264867080, 1263311820, 1264953420, 3, 2, 1, 72000, 1, 0, 1),
(150, 1264780744, 1264785492, 1, 0, 1, 8, 149, 'Time estimated', '<p>Time estimated</p>', 14, 14, 1264953480, 1264780680, 1264953480, 4, 2, 1, 38, 1, 0, 0),
(151, 1264678939, 1269364277, 1, 0, 1, 8, 0, 'Demo task with status: "in planning"', '<p>no description further description</p>', 14, 14, 1265024460, 1265024460, 1265024460, 5, 1, 2, 1800, 1, 0, 1),
(152, 1264678939, 1269364303, 1, 0, 1, 8, 0, 'Demo task with status: "open"', '<p>no description further description</p>', 14, 14, 1265024460, 1265024460, 1265024460, 6, 2, 2, 1800, 1, 0, 3),
(153, 1264678939, 1269364327, 1, 0, 1, 8, 0, 'Demo task with status: "in progress"', '<p>no description further description</p>', 14, 14, 1265024460, 1265024460, 1265024460, 7, 3, 2, 1800, 1, 0, 4),
(154, 1264678939, 1269364352, 1, 0, 1, 8, 0, 'Demo task with status: "awaiting confirmation"', '<p>no description further description</p>', 14, 14, 1265024460, 1265024460, 1265024460, 8, 4, 2, 1800, 1, 0, 5),
(155, 1264678939, 1269364377, 1, 0, 1, 8, 0, 'Demo task with status: "done"', '<p>no description further description</p>', 14, 14, 1265024460, 1265024460, 1265024460, 9, 5, 2, 1800, 1, 0, 6),
(156, 1264678939, 1269364403, 1, 0, 1, 8, 0, 'Demo task with status: "accepted"', '<p>no description further description</p>', 14, 14, 1265024460, 1265024460, 1265024460, 10, 6, 2, 1800, 1, 0, 7),
(157, 1264678939, 1269364425, 1, 0, 1, 8, 0, 'Demo task with status: "rejected"', '<p>no description further description</p>', 14, 14, 1265024460, 1265024460, 1265024460, 11, 7, 2, 1800, 1, 0, 8),
(158, 1264678939, 1269364453, 1, 0, 1, 8, 0, 'Demo task with status: "cleared"', '<p>no description further description</p>', 14, 14, 1265024460, 1265024460, 1265024460, 12, 8, 2, 1800, 1, 0, 9),
(159, 1269364506, 1269364506, 1, 0, 2, 8, 150, 'Publicly visible tasks', '<p>Container with public tasks</p>', 0, 1, 0, 0, 0, 13, 2, 0, 0, 0, 0, 0),
(160, 1264678939, 1269364566, 1, 0, 1, 8, 159, 'Demo task with status: "open"', '<p>no description further description</p>', 14, 14, 1265024460, 1265024460, 1265024460, 14, 2, 2, 1800, 1, 0, 3),
(161, 1264678939, 1269364580, 1, 0, 1, 8, 159, 'Demo task with status: "in progress"', '<p>no description further description</p>', 14, 14, 1265024460, 1265024460, 1265024460, 15, 3, 2, 1800, 1, 0, 9),
(162, 1264678939, 1269364594, 1, 0, 1, 8, 159, 'Demo task with status: "in planning"', '<p>no description further description</p>', 14, 14, 1265024460, 1265024460, 1265024460, 16, 1, 2, 1800, 1, 0, 2),
(163, 1264678939, 1269364618, 1, 0, 1, 8, 159, 'Demo task with status: "awaiting confirmation"', '<p>no description further description</p>', 14, 14, 1265024460, 1265024460, 1265024460, 17, 4, 2, 1800, 1, 0, 3),
(164, 1264678939, 1269364632, 1, 0, 1, 8, 159, 'Demo task with status: "done"', '<p>no description further description</p>', 14, 14, 1265024460, 1265024460, 1265024460, 18, 5, 2, 1800, 1, 0, 4),
(165, 1264678939, 1269364644, 1, 0, 1, 8, 159, 'Demo task with status: "accepted"', '<p>no description further description</p>', 14, 14, 1265024460, 1265024460, 1265024460, 19, 6, 2, 1800, 1, 0, 6),
(166, 1264678939, 1269364664, 1, 0, 1, 8, 159, 'Demo task with status: "rejected"', '<p>no description further description</p>', 14, 14, 1265024460, 1265024460, 1265024460, 20, 7, 2, 1800, 1, 0, 7),
(167, 1264678939, 1269364682, 1, 0, 1, 8, 159, 'Demo task with status: "cleared"', '<p>no description further description</p>', 14, 14, 1265024460, 1265024460, 1265024460, 21, 8, 2, 1800, 1, 0, 4),
(168, 1269364506, 1269365385, 1, 0, 2, 8, 0, 'Publicly visible tasks in container in project root', '<p>Container with public tasks</p>', 0, 1, 0, 0, 0, 22, 2, 0, 0, 0, 0, 3),
(169, 1264678939, 1269365292, 1, 0, 1, 8, 168, 'Demo task with status: "open"', '<p>no description further description</p>', 14, 14, 1265024460, 1265024460, 1265024460, 23, 2, 2, 1800, 1, 1, 3),
(170, 1264678939, 1269365347, 1, 0, 1, 8, 168, 'Demo task with status: "in progress"', '<p>no description further description</p>', 14, 14, 1265024460, 1265024460, 1265024460, 24, 3, 2, 1800, 1, 1, 9),
(171, 1264678939, 1269365284, 1, 0, 1, 8, 168, 'Demo task with status: "in planning"', '<p>no description further description</p>', 14, 14, 1265024460, 1265024460, 1265024460, 25, 1, 2, 1800, 1, 1, 2),
(172, 1264678939, 1269365300, 1, 0, 1, 8, 168, 'Demo task with status: "awaiting confirmation"', '<p>no description further description</p>', 14, 14, 1265024460, 1265024460, 1265024460, 26, 4, 2, 1800, 1, 1, 3),
(173, 1264678939, 1269365310, 1, 0, 1, 8, 168, 'Demo task with status: "done"', '<p>no description further description</p>', 14, 14, 1265024460, 1265024460, 1265024460, 27, 5, 2, 1800, 1, 1, 4),
(174, 1264678939, 1269365332, 1, 0, 1, 8, 168, 'Demo task with status: "accepted"', '<p>no description further description</p>', 14, 14, 1265024460, 1265024460, 1265024460, 28, 6, 2, 1800, 1, 1, 6),
(175, 1264678939, 1269365339, 1, 0, 1, 8, 168, 'Demo task with status: "rejected"', '<p>no description further description</p>', 14, 14, 1265024460, 1265024460, 1265024460, 29, 6, 2, 1800, 1, 1, 7),
(176, 1264678939, 1269365317, 1, 0, 1, 8, 168, 'Demo task with status: "cleared"', '<p>no description further description</p>', 14, 14, 1265024460, 1265024460, 1265024460, 30, 8, 2, 1800, 1, 1, 4);

-- --------------------------------------------------------

--
-- Data for Table `ext_project_role`
--

INSERT INTO `ext_project_role` (`id`, `date_create`, `id_person_create`, `date_update`, `title`, `deleted`) VALUES
(1, 0, 0, 1264696219, 'Project Manager', 0),
(2, 0, 0, 1264696225, 'Developer', 0),
(3, 0, 0, 0, 'Designer', 0),
(4, 0, 0, 1264696237, 'External Project Manager', 0),
(5, 0, 0, 1264696246, 'Customer', 0);

-- --------------------------------------------------------

--
-- Data for Table `ext_project_activity`
--

INSERT INTO `ext_project_activity` (`id`, `date_update`, `id_person_create`, `date_create`, `deleted`, `title`) VALUES
(1, 1254232852, 1, 1254232852, 0, 'Consulting'),
(2, 1254232863, 1, 1254232863, 0, 'Java-Development'),
(3, 1254232867, 1, 1254232867, 0, 'Design'),
(4, 1254232876, 1, 1254232876, 0, 'Testing'),
(5, 0, 1, 1264696282, 0, 'Frontend Engineering'),
(6, 0, 1, 1264696297, 0, 'Database Engineering'),
(7, 0, 17, 1264778822, 0, 'Documentation');

-- --------------------------------------------------------

--
-- Data for Table `ext_search_filtercondition`
--

INSERT INTO `ext_search_filtercondition` (`id`, `date_update`, `date_create`, `id_person_create`, `deleted`, `id_set`, `filter`, `value`, `is_negated`) VALUES
(7, 0, 0, 0, 0, 1, 'currentPersonAssigned', '1', 0),
(8, 0, 0, 0, 0, 1, 'status', '2,3', 0),
(317, 1264779500, 1264779500, 1, 0, 3, 'owner', '17', 0),
(263, 1264697139, 1264697139, 1, 0, 4, 'currentPersonIsCreator', '', 0),
(262, 1264697139, 1264697139, 1, 0, 4, 'status', '2,3', 0),
(264, 1264697139, 1264697139, 1, 0, 4, 'deadlineDyn', 'nextweek', 0),
(265, 1264697267, 1264697267, 1, 0, 5, 'currentPersonAssigned', '', 0),
(266, 1264697267, 1264697267, 1, 0, 5, 'creator', '17', 0),
(267, 1264697267, 1264697267, 1, 0, 5, 'project', '5', 0),
(285, 1264776776, 1264776776, 17, 0, 6, 'deadlineDyn', 'currentweek', 0),
(284, 1264776776, 1264776776, 17, 0, 6, 'status', '2,3,7', 0),
(299, 1264777478, 1264777478, 17, 0, 7, 'deadlineDyn', '', 0),
(298, 1264777478, 1264777478, 17, 0, 7, 'status', '2,3,7', 0),
(283, 1264776776, 1264776776, 17, 0, 6, 'currentPersonAssigned', '', 0),
(277, 1264776619, 1264776619, 17, 0, 8, 'projectleader', '15', 0),
(278, 1264776619, 1264776619, 17, 0, 8, 'status', '2', 0),
(286, 1264776776, 1264776776, 17, 0, 6, 'type', '1', 0),
(300, 1264777478, 1264777478, 17, 0, 7, 'type', '1', 0),
(297, 1264777478, 1264777478, 17, 0, 7, 'currentPersonAssigned', '', 0),
(306, 1264778981, 1264778981, 12, 0, 11, 'status', '2,3', 0),
(303, 1264778957, 1264778957, 1, 0, 12, 'deadlineDyn', 'today', 0),
(304, 1264778957, 1264778957, 1, 0, 12, 'status', '2,3', 0),
(305, 1264778957, 1264778957, 1, 0, 12, 'currentPersonAssigned', '', 0),
(307, 1264778981, 1264778981, 12, 0, 11, 'type', '1', 0),
(308, 1264778981, 1264778981, 12, 0, 11, 'currentPersonAssigned', '', 0),
(309, 1264779006, 1264779006, 12, 0, 13, 'status', '2,3', 0),
(310, 1264779006, 1264779006, 12, 0, 13, 'type', '1', 0),
(311, 1264779025, 1264779025, 1, 0, 14, 'status', '5,6,8', 0),
(315, 1264779153, 1264779153, 1, 0, 15, 'projectleader', '1', 0),
(314, 1264779153, 1264779153, 1, 0, 15, 'status', '8', 1),
(318, 1264779500, 1264779500, 1, 0, 3, 'status', '2', 0);

-- --------------------------------------------------------

--
-- Data for Table `ext_search_filterset`
--

INSERT INTO `ext_search_filterset` (`id`, `date_update`, `date_create`, `id_person_create`, `deleted`, `sorting`, `is_hidden`, `roles`, `type`, `title`, `conjunction`) VALUES
(1, 1246637647, 1246547545, 474, 0, 0, 1, '', 'task', 'Feedback erwartet', 'AND'),
(3, 1264779500, 1264697040, 1, 0, 3, 0, '', 'task', 'Open Task', 'OR'),
(4, 1264697272, 1264697139, 1, 0, 1, 0, '', 'task', 'Next things to do', 'AND'),
(5, 1264697267, 1264697267, 1, 0, 2, 0, '', 'task', 'ABCT: Theodor assigned to me', 'AND'),
(6, 1264776776, 1264776401, 17, 0, 1, 0, '', 'task', 'MyTasks this Week', 'AND'),
(7, 1264777478, 1264776486, 17, 0, 0, 0, '', 'task', 'Tasks Today', 'AND'),
(8, 1264777131, 1264776619, 17, 0, 0, 0, '', 'project', 'I am Projectleader', 'AND'),
(12, 1264778957, 1264778957, 1, 0, 0, 0, '', 'task', 'Important (Deadline today)', 'AND'),
(11, 1264778981, 1264778514, 12, 0, 0, 0, '', 'task', 'My Open Tasks', 'AND'),
(13, 1264779006, 1264779006, 12, 0, 0, 0, '', 'task', 'All open/running tasks', 'AND'),
(14, 1264779033, 1264779025, 1, 0, 4, 1, '', 'task', 'My Done Tasks (hidden on portal)', 'AND'),
(15, 1264779157, 1264779095, 1, 0, 0, 0, '', 'project', 'To manage', 'AND');

-- --------------------------------------------------------

--
-- Data for Table `ext_timetracking_track`
--

INSERT INTO `ext_timetracking_track` (`id`, `date_create`, `date_update`, `id_person_create`, `date_track`, `id_task`, `workload_tracked`, `workload_chargeable`, `comment`) VALUES
(1, 1254233726, 1254233739, 1, 1254233726, 4, 16, 0, ''),
(2, 1254233883, 1254233883, 1, 1254233883, 7, 6, 0, ''),
(3, 1264768637, 1264768637, 1, 1264768637, 115, 6, 0, ''),
(4, 1264777333, 1264779849, 18, 1264779849, 60, 138, 0, ''),
(5, 1264778773, 1264779814, 18, 1264779814, 134, 860, 0, ''),
(6, 1264779082, 1264779098, 12, 1264779098, 144, 3611, 0, ''),
(7, 1264779441, 1264779560, 12, 1264633200, 145, 9600, 0, ''),
(8, 1264779442, 1264779820, 18, 1264779820, 136, 5, 0, ''),
(9, 1264779447, 1264779847, 18, 1264779847, 135, 29, 0, ''),
(10, 1264779532, 1264779752, 18, 1264779752, 146, 3, 0, ''),
(11, 1264779566, 1264779566, 12, 1264779566, 145, 5, 0, ''),
(12, 1264779631, 1264779818, 18, 1264779818, 148, 304, 0, ''),
(13, 1264780868, 1264780868, 1, 1264780868, 87, 1005, 0, ''),
(14, 1264784771, 1264784771, 1, 1264784771, 59, 348, 0, '');

-- --------------------------------------------------------

--
-- Data for Table `ext_timetracking_active`
--

INSERT INTO `ext_timetracking_active` (`id`, `date_create`, `date_update`, `id_person_create`, `id_task`) VALUES
(4, 1264778433, 1264778433, 17, 138);

-- --------------------------------------------------------

--
-- Data for Table `ext_contact_address`
--

INSERT INTO `ext_contact_address` (`id`, `date_create`, `date_update`, `id_person_create`, `deleted`, `id_addresstype`, `id_country`, `id_holidayset`, `street`, `postbox`, `city`, `region`, `zip`, `comment`, `is_preferred`) VALUES
(1, 1264581712, 1269508233, 1, 0, 1, 41, 1, 'Schneestrasse 23', '', 'Zürich', 'Zürich', '8000', '', 0),
(2, 1264581965, 1269508901, 1, 0, 1, 41, 1, 'Winterthurerstrasse 3000', '', 'Zürich', 'Zürich', '8052', '', 0),
(3, 1264581965, 1269508901, 1, 0, 2, 41, 1, 'Winterthurerstrasse 2080', '', 'Zürich', '', '8052', '', 0),
(4, 1264581965, 1269508901, 1, 0, 3, 41, 1, 'Winterthurerstrasse 1917', '', 'Zürich', 'Zürich', '8052', '', 0),
(5, 1264582231, 1269509705, 1, 0, 2, 107, 4, '26-1 Sakuragaoka-cho ', '', 'Tokyo ', '0', '150-8512', '', 1),
(6, 1264582333, 1269508400, 1, 0, 1, 74, 3, 'Lakeside View 22', 'PO Box189', 'Santa Rosa CA', '0', '29334', '', 1),
(7, 1264582503, 1269508373, 1, 0, 1, 0, 1, 'Heimpfad 18', '', 'Bern', '', '3001', '', 0),
(8, 1264583324, 1269509036, 1, 0, 1, 220, 1, 'Hillside Park ', '', 'Streetalete', '0', '25886', '', 1),
(9, 1264583514, 1269508492, 1, 0, 1, 220, 2, 'Swansea', '', 'Al aware', '0', 'MA 02777', '', 0),
(10, 1264583707, 1269509362, 1, 0, 1, 107, 1, 'アベニュー77ヒルサイド', '', '幕張', '0', '"階B10010"', '', 0),
(11, 1264583917, 1269508153, 1, 0, 1, 41, 1, 'Stadelhofferstrasse 30', '', 'Zug', 'Zug', '6341', '', 0),
(12, 1264584243, 1269508609, 1, 0, 2, 220, 2, 'Many Hills, 10th Street NE', '', 'Bolter', '0', 'CO 85302', '', 0),
(13, 1264587666, 1269513255, 1, 0, 2, 74, 0, 'Prince of Wales Passage 57', '', 'Camden', '', '12345', '', 1),
(14, 1264587852, 1269509105, 1, 0, 1, 41, 1, 'Dreierstrasse 35', '', 'Zürich', '0', '8000', '', 0),
(15, 1264696524, 1269509610, 1, 0, 2, 41, 1, 'Zweierstrasse 35', '', 'Zürich', 'Zürich', '8004', '', 0),
(16, 1264780312, 1269510278, 18, 0, 1, 0, 0, 'Mullnetherland Drive', '1234', 'Scarborough', '', '80042', 'find out and add the region', 1);

-- --------------------------------------------------------

--
-- Data for Table `ext_contact_company`
--

INSERT INTO `ext_contact_company` (`id`, `date_create`, `date_update`, `id_person_create`, `deleted`, `title`, `shortname`, `date_enter`, `is_internal`) VALUES
(8, 1264581712, 1269508233, 1, 0, 'Eisbergen GmbH', 'Eisbergen', 1262300400, 0),
(9, 1264581965, 1269508901, 1, 0, 'Gripgrap Communications', 'Gripgrap', 1262300400, 0),
(10, 1264582231, 1269509705, 1, 0, 'Transmetric Measure Co.', 'TransMeas', 1217541600, 0),
(11, 1264582333, 1269508400, 1, 0, 'ABC-Tec Holding', 'ABCT', 1262300400, 0),
(12, 1264582503, 1269508373, 1, 0, 'World Watches Fedaration', 'WWF', 1262300400, 0),
(13, 1264583324, 1269509036, 1, 0, 'Innovacation Inc.', 'InnoVac', 978303600, 0),
(14, 1264583514, 1269508492, 1, 0, 'Goodmonth', 'Goodmonth', 1262300400, 0),
(15, 1264583707, 1269509362, 1, 0, 'Sugarion', 'sugarion', 1262300400, 0),
(16, 1264583917, 1269508153, 1, 0, 'Customers ACME', 'Customers', 1262300400, 0),
(17, 1264584243, 1269508609, 1, 0, 'Rainbowflag Ltd.', 'RBF', 0, 0),
(18, 1264587852, 1269509105, 1, 0, 'snowman production AG', 'smp', 1262300400, 1);

-- --------------------------------------------------------

--
-- Data for Table `ext_contact_contactinfo`
--

INSERT INTO `ext_contact_contactinfo` (`id`, `date_create`, `date_update`, `id_person_create`, `deleted`, `id_contactinfotype`, `info`, `is_preferred`) VALUES
(1, 1259845750, 1259848722, 1, 0, '11', 'http://www.snowflake.ch/', 1),
(2, 1259845750, 1259848722, 1, 0, '4', '+41 44 455 80 80', 1),
(3, 1259845750, 1259848722, 1, 0, '1', 'info@snowflake.ch', 1),
(4, 1259845750, 1259848722, 1, 0, '8', '+41 44 455 80 87', 1),
(5, 1264581204, 1269513298, 1, 0, '4', '555 325 48 45', 0),
(6, 1264581712, 1269508233, 1, 0, '1', 'info@eisberggmbh.example.org', 0),
(7, 1264581965, 1269508901, 1, 0, '1', 'info@gripgrap.example.com', 0),
(8, 1264581965, 1269508901, 1, 0, '8', '555 400 12 12', 0),
(9, 1264581965, 1269508901, 1, 0, '4', '555 400 11 11', 0),
(10, 1264582231, 1269509705, 1, 0, '1', 'admin@transmeas.example.org', 0),
(11, 1264582231, 1269509705, 1, 0, '4', '555 3456789', 1),
(12, 1264582231, 1269509705, 1, 0, '11', 'transmeas.example.org', 0),
(13, 1264582333, 1269508400, 1, 0, '1', 'abc@abc-tec.example..com', 0),
(14, 1264582333, 1269508400, 1, 0, '4', '555-111-111-1', 1),
(15, 1264582333, 1269508400, 1, 0, '11', 'abc-tec.example.com', 0),
(16, 1264582503, 1269508373, 1, 0, '1', 'info@wwf.example.com', 0),
(17, 1264582503, 1269508373, 1, 0, '4', '555 414 85 21', 0),
(18, 1264582503, 1269508373, 1, 0, '11', 'wwf.example.com', 0),
(19, 1264583324, 1269509036, 1, 0, '1', 'info@inoinc.example.com', 0),
(20, 1264583324, 1269509036, 1, 0, '4', '555 1.433.1980', 0),
(21, 1264583514, 1269508492, 1, 0, '1', 'admin@goodmonth.example.com', 0),
(22, 1264583514, 1269508492, 1, 0, '4', '555 3063 085', 1),
(23, 1264583707, 1264583707, 1, 0, '1', 'sugarion@sugarino.ch', 0),
(24, 1264583707, 1269509362, 1, 0, '4', '555 70982200', 0),
(25, 1264583917, 1269508153, 1, 0, '1', 'trava@trava.example..com', 1),
(26, 1264583917, 1269508153, 1, 0, '8', '555 211 01 46', 0),
(27, 1264583917, 1269508153, 1, 0, '6', '555 211 01 45', 0),
(28, 1264583917, 1269508153, 1, 0, '4', '555 211 01 45', 0),
(29, 1264583917, 1269508153, 1, 0, '11', 'www.trava.example..com', 0),
(30, 1264584243, 1269508609, 1, 0, '1', 'contact@rainbow.example.org', 0),
(31, 1264584243, 1269508609, 1, 0, '4', '555-2154832322', 0),
(32, 1264584243, 1269508609, 1, 0, '11', 'rainbow.example.org', 0),
(33, 1264584484, 1269513353, 1, 0, '4', '555 714 21 15', 0),
(37, 1264584644, 1269510452, 1, 0, '4', '555 211 01 50', 0),
(38, 1264584644, 1269510452, 1, 0, '7', '555 211 01 51', 0),
(39, 1264584718, 1269509362, 1, 0, '1', 'sugarion@sugarion.example.com', 1),
(40, 1264584718, 1269509362, 1, 0, '11', 'www.sugarion.example.com', 0),
(42, 1264584779, 1269513272, 1, 0, '4', '555 455 77 25', 0),
(43, 1264585014, 1269513365, 1, 0, '4', '555 284 45 80', 0),
(45, 1264585252, 1269513237, 1, 0, '4', '555 7654321', 1),
(46, 1264585252, 1269513237, 1, 0, '6', '555 34567899', 0),
(49, 1264585479, 1269513406, 1, 0, '8', '555 722 98 99', 0),
(51, 1264585479, 1269513406, 1, 0, '7', '555 720 98 99', 0),
(52, 1264585479, 1269513406, 1, 0, '6', '555 720 98 98', 0),
(53, 1264585479, 1269513406, 1, 0, '9', '555 322 12 52', 0),
(55, 1264585479, 1269513406, 1, 0, '4', '555 722 98 98', 0),
(56, 1264585479, 1269513406, 1, 0, '2', '555 720 98 98', 0),
(57, 1264585573, 1269513430, 1, 0, '4', '555 414 85 21', 1),
(58, 1264585673, 1269511275, 1, 0, '4', '555 400 12 14', 0),
(59, 1264585840, 1269513340, 1, 0, '4', '555 454 11 45', 0),
(60, 1264587666, 1269513255, 1, 0, '4', '555 455 00 12', 1),
(61, 1264587852, 1269509105, 1, 0, '1', 'info@snowman.example.com', 0),
(62, 1264587852, 1269509105, 1, 0, '4', '555 455 00 00', 0),
(63, 1264587852, 1269509105, 1, 0, '8', '555 455 00 01', 0),
(65, 1264696524, 1269509610, 1, 0, '1', 'team@todoyu.com', 0);

-- --------------------------------------------------------

--
-- Data for Table `ext_contact_jobtype`
--

INSERT INTO `ext_contact_jobtype` (`id`, `date_create`, `date_update`, `id_person_create`, `deleted`, `title`) VALUES
(1, 1264761659, 0, 1, 0, 'Project Manager'),
(2, 1264761659, 0, 1, 0, 'CEO'),
(3, 1264761659, 0, 1, 0, 'Receptionist'),
(4, 1264761659, 0, 1, 0, 'Developer'),
(5, 1264761659, 0, 1, 0, 'IT-Manager'),
(6, 1264761659, 0, 1, 0, 'Test Engineer');

-- --------------------------------------------------------

--
-- Data for Table `ext_contact_mm_company_address`
--

INSERT INTO `ext_contact_mm_company_address` (`id`, `id_company`, `id_address`) VALUES
(1, 8, 1),
(7, 12, 7),
(12, 16, 11),
(14, 9, 2),
(15, 9, 3),
(16, 9, 4),
(24, 14, 9),
(26, 15, 10),
(27, 11, 6),
(28, 13, 8),
(29, 17, 12),
(30, 10, 5),
(35, 18, 14),
(36, 1, 15);

-- --------------------------------------------------------

--
-- Data for Table `ext_contact_mm_company_contactinfo`
--

INSERT INTO `ext_contact_mm_company_contactinfo` (`id`, `id_company`, `id_contactinfo`) VALUES
(1, 5, 1),
(2, 5, 2),
(3, 5, 3),
(4, 5, 4),
(5, 8, 6),
(39, 9, 9),
(38, 9, 8),
(37, 9, 7),
(78, 10, 12),
(77, 10, 11),
(76, 10, 10),
(70, 11, 15),
(69, 11, 14),
(68, 11, 13),
(15, 12, 16),
(16, 12, 17),
(17, 12, 18),
(72, 13, 20),
(71, 13, 19),
(61, 14, 21),
(60, 14, 22),
(67, 15, 39),
(66, 15, 24),
(33, 16, 29),
(32, 16, 28),
(31, 16, 27),
(30, 16, 26),
(29, 16, 25),
(75, 17, 30),
(74, 17, 31),
(73, 17, 32),
(65, 15, 40),
(89, 18, 63),
(88, 18, 62),
(87, 18, 61),
(83, 1, 65);

-- --------------------------------------------------------

--
-- Data for Table `ext_contact_mm_company_person`
--

INSERT INTO `ext_contact_mm_company_person` (`id`, `id_company`, `id_person`, `id_workaddress`, `id_jobtype`) VALUES
(2, 6, 2, 0, 0),
(4, 5, 2, 0, 0),
(5, 5, 3, 0, 0),
(11, 8, 4, 0, 0),
(41, 14, 8, 9, 0),
(32, 11, 5, 0, 0),
(43, 15, 7, 10, 0),
(50, 16, 6, 11, 1),
(70, 13, 9, 0, 0),
(73, 1, 18, 15, 4),
(37, 10, 16, 0, 0),
(18, 12, 11, 0, 0),
(19, 9, 12, 0, 0),
(34, 17, 13, 0, 0),
(46, 15, 15, 10, 0),
(39, 12, 10, 7, 0),
(67, 18, 12, 14, 2),
(74, 18, 14, 14, 0),
(55, 1, 17, 15, 1),
(71, 1, 1, 15, 4);

-- --------------------------------------------------------

--
-- Data for Table `ext_contact_mm_person_address`
--

INSERT INTO `ext_contact_mm_person_address` (`id`, `id_person`, `id_address`) VALUES
(4, 14, 13),
(3, 1, 16);

-- --------------------------------------------------------

--
-- Data for Table `ext_contact_mm_person_contactinfo`
--

INSERT INTO `ext_contact_mm_person_contactinfo` (`id`, `id_person`, `id_contactinfo`) VALUES
(5, 4, 5),
(17, 5, 35),
(16, 5, 34),
(15, 5, 33),
(6, 4, 36),
(84, 6, 37),
(83, 6, 38),
(70, 7, 42),
(69, 7, 41),
(66, 8, 44),
(65, 8, 43),
(88, 9, 45),
(87, 9, 46),
(86, 9, 47),
(85, 9, 48),
(62, 10, 56),
(61, 10, 55),
(60, 10, 54),
(59, 10, 53),
(58, 10, 52),
(57, 10, 51),
(56, 10, 50),
(55, 10, 49),
(30, 11, 57),
(31, 12, 58),
(33, 13, 59),
(89, 14, 60),
(38, 16, 64);

-- --------------------------------------------------------

--
-- Data for Table `ext_contact_mm_person_role`
--

INSERT INTO `ext_contact_mm_person_role` (`id`, `id_person`, `id_role`) VALUES
(1, 18, 3),
(2, 12, 2),
(3, 6, 1);

-- --------------------------------------------------------

--
-- Data for Table `system_preference`
--

INSERT INTO `system_preference` (`id`, `id_person`, `ext`, `area`, `preference`, `item`, `value`) VALUES
(1, 1, 112, 0, 'detailsexpanded', 3, '1'),
(2, 1, 112, 0, 'detailsexpanded', 35, '1'),
(3, 1, 112, 0, 'tasktree-task-exp', 0, '16'),
(4, 1, 112, 0, 'tasktree-task-exp', 0, '19'),
(5, 1, 112, 0, 'tasktree-subtasks', 0, '8'),
(6, 1, 112, 0, 'tasktree-subtasks', 0, '9'),
(7, 1, 112, 0, 'tasktree-task-exp', 0, '27'),
(8, 1, 112, 0, 'tasktree-subtasks', 0, '10'),
(9, 1, 112, 0, 'tasktree-subtasks', 0, '13'),
(10, 1, 112, 0, 'tasktree-subtasks', 0, '43'),
(11, 1, 112, 0, 'tasktree-subtasks', 0, '44'),
(12, 1, 112, 0, 'tasktree-subtasks', 0, '45'),
(13, 1, 112, 0, 'tasktree-subtasks', 0, '57'),
(14, 1, 112, 0, 'tasktree-subtasks', 0, '46'),
(15, 1, 112, 0, 'detailsexpanded', 6, '1'),
(16, 1, 112, 0, 'detailsexpanded', 7, '1'),
(17, 1, 112, 0, 'detailsexpanded', 9, '1'),
(18, 1, 112, 0, 'detailsexpanded', 11, '1'),
(19, 1, 112, 112, 'tasktree-subtasks', 0, '75'),
(20, 1, 112, 0, 'detailsexpanded', 13, '1'),
(21, 1, 112, 112, 'tasktree-subtasks', 0, '82'),
(22, 1, 112, 112, 'tasktree-subtasks', 0, '85'),
(23, 1, 112, 112, 'tasktree-subtasks', 0, '94'),
(24, 1, 112, 112, 'tasktree-subtasks', 0, '98'),
(25, 1, 112, 112, 'tasktree-subtasks', 0, '101'),
(26, 1, 112, 112, 'tasktree-subtasks', 0, '107'),
(27, 1, 112, 112, 'tasktree-subtasks', 0, '111'),
(28, 1, 112, 112, 'tasktree-subtasks', 0, '89'),
(29, 1, 112, 112, 'tasktree-subtasks', 0, '37'),
(30, 1, 112, 112, 'tasktree-subtasks', 0, '38'),
(31, 1, 112, 112, 'tasktree-subtasks', 0, '88'),
(32, 1, 120, 0, 'locale', 0, 'en_GB'),
(33, 1, 112, 112, 'task-tab', 61, 'comment'),
(34, 1, 112, 112, 'task-tab', 60, 'comment'),
(35, 17, 112, 112, 'panelwidget-projectlist-filter', 0, '[{"filter":"status","value":["1","3","5","8","9"]}]'),
(36, 17, 112, 112, 'panelwidget-projectstatusfilter', 0, '1,3,5,8,9'),
(37, 1, 104, 0, 'portal-event-expanded', 1, '1'),
(38, 17, 112, 112, 'tasktree-subtasks', 0, '13'),
(39, 17, 112, 0, 'tasktree-subtasks', 0, '13'),
(40, 1, 112, 112, 'tasktree-subtasks', 0, '12'),
(41, 1, 112, 0, 'tasktree-subtasks', 0, '12'),
(42, 1, 112, 112, 'task-tab', 35, 'comment'),
(43, 1, 112, 111, 'task-tab', 35, 'comment'),
(44, 17, 112, 112, 'task-tab', 60, 'assets'),
(45, 17, 112, 112, 'tasktree-subtasks', 0, '88'),
(46, 17, 112, 112, 'tasktree-subtasks', 0, '94'),
(47, 17, 112, 112, 'tasktree-subtasks', 0, '101'),
(48, 17, 112, 112, 'task-tab', 106, 'assets'),
(49, 17, 112, 112, 'task-tab', 95, 'comment'),
(50, 1, 112, 111, 'task-tab', 95, 'assets'),
(51, 17, 120, 100, 'panelwidget-userselector-filter', 0, 'test'),
(52, 17, 120, 0, 'admintab', 0, 'user'),
(53, 18, 126, 0, 'pwidget-profilemodules', 0, '0'),
(54, 18, 112, 111, 'task-tab', 60, 'assets'),
(55, 17, 115, 0, 'filterset-task', 0, '7'),
(56, 17, 115, 0, 'filterset-project', 0, '8'),
(57, 17, 115, 0, 'tab', 0, 'task'),
(58, 12, 112, 0, 'detailsexpanded', 14, '1'),
(59, 17, 112, 111, 'task-tab', 60, 'comment'),
(60, 17, 112, 0, 'tasktree-subtasks', 0, '101'),
(61, 17, 112, 0, 'tasktree-task-exp', 0, '101'),
(62, 18, 112, 111, 'task-tab', 0, 'comment'),
(63, 12, 112, 0, 'tasktree-subtasks', 0, '133'),
(64, 12, 106, 0, 'tab', 0, 'person'),
(65, 17, 126, 0, 'tab-general', 0, 'password'),
(66, 17, 126, 0, 'module', 0, 'general'),
(67, 17, 106, 0, 'tab', 0, 'person'),
(68, 17, 104, 104, 'panelwidget-holidaysetselector', 0, '2,4,3,1'),
(69, 17, 112, 0, 'tasktree-subtasks', 0, '85'),
(70, 17, 112, 0, 'tasktree-task-exp', 0, '85'),
(71, 17, 104, 0, 'fulldayview', 0, '0'),
(72, 17, 112, 0, 'tasktree-subtasks', 0, '87'),
(73, 17, 112, 0, 'tasktree-task-exp', 0, '87'),
(74, 17, 112, 112, 'tasktree-subtasks', 0, '87'),
(75, 1, 112, 0, 'tasktree-subtasks', 0, '11'),
(76, 17, 120, 104, 'panelwidget-staffselector', 0, '{"multiple":false,"jobtypes":["0"],"users":["14","18","17","1"]}'),
(77, 17, 112, 112, 'tasktree-subtasks', 0, '43'),
(78, 12, 112, 111, 'task-tab', 134, 'assets'),
(79, 12, 112, 111, 'task-tab', 0, 'comment'),
(80, 17, 112, 112, 'panelwidget-taskstatusfilter', 0, '1,2,3,4,5,6,7,8'),
(81, 17, 112, 0, 'tasktree-subtasks', 0, '15'),
(82, 17, 112, 0, 'tasktree-task-exp', 0, '15'),
(83, 17, 104, 0, 'tab', 0, 'month'),
(84, 17, 0, 17, 'tabsubmenu_planning', 0, 'calendar'),
(85, 17, 104, 104, 'date', 0, '1265616000'),
(86, 17, 112, 0, 'tasktree-subtasks', 0, '142'),
(87, 17, 112, 0, 'tasktree-task-exp', 0, '142'),
(88, 17, 111, 0, 'filtersets', 0, '6'),
(89, 17, 111, 0, 'tab', 0, 'todo'),
(90, 17, 112, 0, 'project', 0, '4'),
(91, 17, 112, 0, 'projecttabs', 0, '4,7,9'),
(92, 17, 100, 100, 'module', 0, 'records'),
(93, 17, 0, 0, 'tab', 0, 'portal'),
(94, 12, 115, 0, 'filterset-task', 0, '13'),
(95, 12, 111, 0, 'filtersets', 0, '13'),
(96, 12, 111, 0, 'tab', 0, 'todo'),
(97, 1, 112, 112, 'panelwidget-projectlist-filter', 0, '[{"filter":"fulltext","value":""},{"filter":"status","value":["3","5","8","9"]}]'),
(98, 1, 112, 112, 'panelwidget-projectstatusfilter', 0, '3,5,8,9'),
(99, 1, 112, 112, 'panelwidget-taskstatusfilter', 0, '1,2,3,4,5,6,7,8'),
(100, 12, 112, 0, 'tasktree-task-exp', 0, '144'),
(101, 1, 115, 0, 'filterset-project', 0, '15'),
(102, 1, 118, 0, 'ext', 0, 'bookmark'),
(103, 1, 120, 0, 'admintab', 0, 'user'),
(104, 1, 106, 0, 'tab', 0, 'person'),
(105, 18, 112, 111, 'task-tab', 135, 'timetracking'),
(106, 18, 112, 111, 'task-tab', 136, 'timetracking'),
(107, 18, 112, 111, 'task-tab', 134, 'timetracking'),
(108, 12, 112, 0, 'tasktree-subtasks', 0, '137'),
(109, 12, 112, 0, 'tasktree-task-exp', 0, '137'),
(110, 6, 0, 6, 'tabsubmenu_planning', 0, 'calendar'),
(111, 1, 115, 0, 'filterset-task', 0, '3'),
(112, 18, 112, 112, 'tasktree-subtasks', 0, '137'),
(113, 12, 112, 112, 'tasktree-subtasks', 0, '137'),
(114, 12, 112, 112, 'tasktree-subtasks', 0, '145'),
(115, 18, 112, 111, 'task-tab', 148, 'comment'),
(116, 18, 120, 0, 'locale', 0, 'en_GB'),
(117, 18, 112, 0, 'project', 0, '14'),
(118, 18, 112, 0, 'projecttabs', 0, ''),
(119, 12, 107, 0, 'pwidget-daytracks', 0, '1'),
(120, 6, 115, 0, 'tab', 0, 'project'),
(121, 1, 112, 112, 'tasktree-subtasks', 0, '113'),
(122, 1, 112, 112, 'tasktree-subtasks', 0, '13'),
(123, 1, 112, 112, 'tasktree-subtasks', 0, '15'),
(124, 1, 112, 112, 'tasktree-subtasks', 0, '142'),
(125, 12, 100, 100, 'module', 0, 'user'),
(126, 12, 120, 100, 'panelwidget-userselector-filter', 0, ''),
(127, 18, 111, 0, 'tab', 0, 'todo'),
(128, 18, 106, 0, 'tab', 0, 'person'),
(129, 12, 120, 0, 'admintab', 0, 'usergroup'),
(130, 1, 112, 0, 'tasktree-subtasks', 0, '37'),
(131, 1, 112, 111, 'task-tab', 106, 'assets'),
(132, 12, 112, 0, 'pwidget-projectlist', 0, '0'),
(133, 12, 112, 0, 'pwidget-quickproject', 0, '0'),
(134, 12, 112, 0, 'pwidget-taskstatusfilter', 0, '0'),
(135, 12, 103, 0, 'pwidget-taskbookmarks', 0, '0'),
(136, 12, 112, 112, 'panelwidget-projectstatusfilter', 0, '1,3,5'),
(137, 12, 112, 0, 'pwidget-projectstatusfilter', 0, '1'),
(138, 12, 112, 112, 'panelwidget-projectlist-filter', 0, '[{"filter":"fulltext","value":"web"}]'),
(139, 12, 0, 0, 'tab', 0, 'project'),
(140, 1, 120, 100, 'panelwidget-userselector-filter', 0, 'eisen'),
(141, 12, 112, 0, 'project', 0, '6'),
(142, 12, 112, 0, 'projecttabs', 0, '6,4,12'),
(143, 12, 112, 112, 'tasktree-subtasks', 0, '113'),
(144, 12, 112, 112, 'tasktree-subtasks', 0, '114'),
(145, 12, 112, 0, 'tasktree-task-exp', 0, '116'),
(146, 18, 100, 100, 'module', 0, 'extensions'),
(148, 18, 0, 0, 'tab', 0, 'contact'),
(149, 1, 112, 0, 'tasktree-subtasks', 0, '38'),
(150, 12, 112, 112, 'task-tab', 116, 'assets'),
(151, 6, 112, 0, 'pwidget-quickproject', 0, '0'),
(152, 1, 100, 100, 'module', 0, 'extensions'),
(153, 1, 112, 0, 'pwidget-quickproject', 0, '1'),
(154, 1, 103, 0, 'pwidget-taskbookmarks', 0, '0'),
(155, 1, 112, 112, 'task-tab', 149, 'comment'),
(156, 1, 112, 0, 'tasktree-subtasks', 0, '149'),
(157, 1, 112, 0, 'tasktree-task-exp', 0, '149'),
(158, 1, 112, 0, 'project', 0, '10'),
(159, 1, 126, 0, 'tab-general', 0, 'password'),
(160, 1, 126, 0, 'module', 0, 'general'),
(161, 1, 104, 0, 'portal-event-expanded', 1, '0'),
(162, 1, 111, 0, 'filtersets', 0, '12,4,5,3'),
(163, 1, 112, 0, 'tasktree-subtasks', 0, '42'),
(164, 1, 120, 104, 'panelwidget-staffselector', 0, '{"multiple":false,"jobtypes":["-1"],"users":["12"]}'),
(165, 1, 104, 104, 'panelwidget-eventtypeselector', 0, '1,2,7,3,4,5,6,8,11,12,13'),
(166, 1, 104, 104, 'panelwidget-holidaysetselector', 0, '4'),
(167, 1, 104, 104, 'date', 0, '1295218800'),
(168, 1, 104, 0, 'tab', 0, 'month'),
(169, 1, 0, 1, 'tabsubmenu_planning', 0, 'calendar'),
(170, 1, 111, 0, 'tab', 0, 'todo'),
(171, 1, 107, 0, 'pwidget-daytracks', 0, '0'),
(172, 6, 112, 112, 'panelwidget-projectlist-filter', 0, '[{"filter":"status","value":["3","5","8","9"]}]'),
(173, 6, 112, 112, 'panelwidget-projectstatusfilter', 0, '3,5,8,9'),
(174, 6, 111, 0, 'tab', 0, 'todo'),
(175, 1, 112, 0, 'projecttabs', 0, '10,4,8'),
(176, 1, 112, 0, 'pwidget-taskstatusfilter', 0, '0'),
(177, 1, 112, 0, 'pwidget-projectstatusfilter', 0, '0'),
(178, 1, 112, 0, 'pwidget-projectlist', 0, '0'),
(179, 6, 112, 0, 'projecttabs', 0, '8'),
(180, 6, 112, 0, 'tasktree-filters', 0, 'a:1:{s:6:"status";s:7:"1,5,6,8";}'),
(181, 6, 112, 112, 'panelwidget-taskstatusfilter', 0, '1,5,6,8'),
(182, 6, 0, 0, 'tab', 0, 'portal'),
(183, 1, 0, 0, 'tab', 0, 'portal'),
(184, 1, 112, 0, 'task-expanded', 0, '35'),
(185, 1, 112, 0, 'task-expanded', 0, '36');

-- --------------------------------------------------------

--
-- Data for Table `ext_contact_person`
--

INSERT INTO `ext_contact_person` (`id`, `date_create`, `date_update`, `id_person_create`, `deleted`, `username`, `password`, `email`, `is_admin`, `is_active`, `firstname`, `lastname`, `shortname`, `salutation`, `title`, `birthday`) VALUES
(2, 1264581214, 1269513208, 1, 0, '', '', '', 0, 0, 'Neil', 'Aaron', 'NEAA', 'm', '', '1973-15-03'),
(3, 1264581224, 1269513218, 1, 0, '', '', '', 0, 0, 'Seth', 'Acuna', 'SEAC', 'm', '', '1963-03-02'),
(4, 1264581204, 1269513298, 1, 0, '', '', '', 0, 0, 'James', 'Brown', 'JABR', 'm', '', '1933-05-03'),
(5, 1264584484, 1269513353, 1, 0, '', '', '', 0, 0, 'Ludwig', 'van Beethoven', 'LUVA', 'm', '', '1770-12-17'),
(6, 1264584644, 1269510452, 1, 0, 'customer', '5f4dcc3b5aa765d61d8327deb882cf99', 'curt@trava.example.com', 0, 1, 'Curt', 'Customer', 'CUCU', 'm', '', '1980-04-04'),
(7, 1264584779, 1269513272, 1, 0, '', '', '', 0, 0, 'Giuseppe', 'Verdi', 'GIVE', 'm', '', '1813-10-10'),
(8, 1264585014, 1269513365, 1, 0, '', '', '', 0, 0, 'Marilyn', 'Monroe', 'MAMO', 'w', '', '1926-06-01'),
(9, 1264585252, 1269513237, 1, 0, '', '', '', 0, 0, 'Ella', 'Fitzgerald', 'ELFI', 'w', '', '1917-04-25'),
(10, 1264585479, 1269513406, 1, 0, '', '', '', 0, 0, 'Wolfgang Amadeus', 'Mozart', 'WOMO', 'm', '', '1756-01-27'),
(11, 1264585573, 1269513430, 1, 0, '', '', '', 0, 0, 'Marlene', 'Dietrich', 'MADI', 'w', '', '1901-12-27'),
(12, 1264585673, 1269511275, 1, 0, 'projectmanager', '5f4dcc3b5aa765d61d8327deb882cf99', 'paul@gripgrap.example.com', 0, 1, 'Paul', 'Projectmanager', 'PaPr', 'm', '', '1974-02-25'),
(13, 1264585840, 1269513340, 1, 0, '', '', '', 0, 0, 'Joseph', 'Haydn', 'JOHA', 'm', '', '1732-03-31'),
(14, 1264587666, 1269513255, 1, 0, '', '', '', 0, 0, 'Elvis', 'Presley', 'ELPR', 'm', '', '1935-01-08'),
(15, 1264694201, 1269513308, 1, 0, '', '', '', 0, 0, 'Johann Sebastian', 'Bach', 'JOBA', 'm', '', '1685-03-21'),
(16, 1264694923, 1269513285, 1, 0, '', '', '', 0, 0, 'Irino', 'Yoshirō', 'IRYO', 'm', '', '1921-11-13'),
(17, 1264696599, 1269513378, 1, 0, '', '098f6bcd4621d373cade4e832627b4f6', '', 0, 0, 'Richard', 'Wagner', 'RIWA', 'm', '', '1813-05-22'),
(18, 1264696672, 1269513070, 1, 0, 'staff', '5f4dcc3b5aa765d61d8327deb882cf99', 'williworker@todoyu.example.com', 0, 1, 'Willi', 'Worker', 'WiWo', 'm', '', '1970-01-09'),
(19, 1264696709, 1269513322, 1, 0, '', '', '', 0, 0, 'Johannes', 'Brahms', 'JOBR2', 'm', '', '1833-05-07');

-- --------------------------------------------------------

--
-- Raise sample data timestamps
--

-- events
UPDATE ext_calendar_event SET date_create	= date_create + 3715200;
UPDATE ext_calendar_event SET date_update	= date_update + 3715200;
UPDATE ext_calendar_event SET date_start	= date_start + 3715200;
UPDATE ext_calendar_event SET date_end		= date_end + 3715200;

-- projects
UPDATE ext_project_project SET date_create		= date_create + 3715200;
UPDATE ext_project_project SET date_update		= date_update + 3715200;
UPDATE ext_project_project SET date_start		= date_start + 3715200;
UPDATE ext_project_project SET date_end			= date_end + 3715200;
UPDATE ext_project_project SET date_deadline	= date_deadline + 3715200;

-- tasks
UPDATE ext_project_task SET date_create		= date_create + 3715200;
UPDATE ext_project_task SET date_update		= date_update + 3715200;
UPDATE ext_project_task SET date_start		= date_start + 3715200;
UPDATE ext_project_task SET date_end		= date_end + 3715200;
UPDATE ext_project_task SET date_deadline	= date_deadline + 3715200;

-- comments, feedbacks
UPDATE ext_comment_comment SET date_create	= date_create + 3715200;
UPDATE ext_comment_comment SET date_update	= date_update + 3715200;
UPDATE ext_comment_mm_comment_feedback SET date_create	= date_create + 3715200;
UPDATE ext_comment_mm_comment_feedback SET date_update	= date_update + 3715200;

-- timetracks
UPDATE ext_timetracking_track SET date_create		= date_create + 3715200;
UPDATE ext_timetracking_track SET date_update		= date_update + 3715200;
UPDATE ext_timetracking_track SET date_track		= date_track + 3715200;