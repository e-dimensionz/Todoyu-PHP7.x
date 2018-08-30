-- Rename tables
RENAME TABLE `ext_comment_feedback` TO `ext_comment_mm_comment_feedback` ;
RENAME TABLE `system_errorlog` TO `system_log_error` ;

-- Remove tables
DROP TABLE `system_log`;

-- Rename worktype to activity --
RENAME TABLE `ext_project_worktype` TO `ext_project_activity` ;
ALTER TABLE `ext_project_task` CHANGE `id_worktype` `id_activity` SMALLINT( 6 ) NOT NULL DEFAULT '0';
-- ALTER TABLE `ext_projectbilling_rate` CHANGE `id_worktype` `id_activity` SMALLINT( 6 ) UNSIGNED NOT NULL;

-- container status --- (open)
UPDATE `ext_project_task` SET status = 2 WHERE `type` = 2;

-- locale path ---
UPDATE `ext_contact_contactinfotype` SET `title` = REPLACE(`title`, 'LLL:contact.contactinfo.', 'LLL:contact.ext.contactinfo.');


-- system_panelwidget --
DROP TABLE `system_panelwidget`;

-- Change field type to varchar
ALTER TABLE `ext_timetracking_track` CHANGE `comment` `comment` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

-- Rename preferred to is_preferred
ALTER TABLE `ext_contact_contactinfo` CHANGE `preferred` `is_preferred` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0';

-- Rename active to is_active
ALTER TABLE `ext_contact_person` CHANGE `active` `is_active` TINYINT( 1 ) NOT NULL DEFAULT '0';

-- Rename course to rate
-- ALTER TABLE `ext_currency_exchange` CHANGE `course` `rate` FLOAT UNSIGNED NOT NULL DEFAULT '0';

-- Public flag of contactinfotype
ALTER TABLE `ext_contact_contactinfotype` ADD `is_public` TINYINT( 1 ) NOT NULL DEFAULT '0';
UPDATE `ext_contact_contactinfotype` SET `is_public` = 1 WHERE id IN (1, 3, 4, 6, 8, 10, 11);

-- Replace old class names in freeze table
UPDATE `system_freeze` SET `data` = REPLACE(`data`, 'O:13:"TodoyuAddress"', 'O:20:"TodoyuContactAddress"') WHERE `data` LIKE '%TodoyuAddress%';
UPDATE `system_freeze` SET `data` = REPLACE(`data`, 'O:12:"TodoyuPerson"', 'O:19:"TodoyuContactPerson"') WHERE `data` LIKE '%TodoyuPerson%';
UPDATE `system_freeze` SET `element_type` = REPLACE(`element_type`, 'TodoyuAddress', 'TodoyuContactAddress') WHERE `element_type` = 'TodoyuAddress';
UPDATE `system_freeze` SET `element_type` = REPLACE(`element_type`, 'TodoyuPerson', 'TodoyuContactPerson') WHERE `element_type` = 'TodoyuPerson';
UPDATE `system_freeze` SET `hash` = MD5(`data`);

-- rename is_negated
ALTER TABLE `ext_search_filtercondition` CHANGE `negate` `is_negated` TINYINT( 1 ) NOT NULL DEFAULT '0';