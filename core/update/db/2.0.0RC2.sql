ALTER TABLE `ext_user_company` DROP `is_ngo`;

ALTER TABLE `ext_project_task`
DROP `offered_accesslevel` ,
DROP `is_offered` ,
DROP `clearance_state` ,
DROP `is_private` ,
DROP `is_estimatedworkload_public`;

ALTER TABLE `ext_calendar_event` DROP `is_public`;


UPDATE `ext_user_contactinfotype` SET `title` = 'LLL:contact.contactinfo.type.email_business' WHERE `title` = 'user.contactinfo.email_business' ;
UPDATE `ext_user_contactinfotype` SET `title` = 'LLL:contact.contactinfo.type.tel_private' WHERE `title` = 'user.contactinfo.tel_private' ;
UPDATE `ext_user_contactinfotype` SET `title` = 'LLL:contact.contactinfo.type.tel_exchange' WHERE `title` = 'user.contactinfo.tel_exchange' ;
UPDATE `ext_user_contactinfotype` SET `title` = 'LLL:contact.contactinfo.type.tel_business' WHERE `title` = 'user.contactinfo.tel_business' ;
UPDATE `ext_user_contactinfotype` SET `title` = 'LLL:contact.contactinfo.type.email_private' WHERE `title` = 'user.contactinfo.email_private' ;
UPDATE `ext_user_contactinfotype` SET `title` = 'LLL:contact.contactinfo.type.mobile_business' WHERE `title` = 'user.contactinfo.mobile_business' ;
UPDATE `ext_user_contactinfotype` SET `title` = 'LLL:contact.contactinfo.type.fax_private' WHERE `title` = 'user.contactinfo.fax_private' ;
UPDATE `ext_user_contactinfotype` SET `title` = 'LLL:contact.contactinfo.type.fax_business' WHERE `title` = 'user.contactinfo.fax_business' ;
UPDATE `ext_user_contactinfotype` SET `title` = 'LLL:contact.contactinfo.type.mobile_private' WHERE `title` = 'user.contactinfo.mobile_private' ;
UPDATE `ext_user_contactinfotype` SET `title` = 'LLL:contact.contactinfo.type.fax_exchange' WHERE `title` = 'user.contactinfo.fax_exchange' ;
UPDATE `ext_user_contactinfotype` SET `title` = 'LLL:contact.contactinfo.type.website' WHERE `title` = 'user.contactinfo.website' ;
UPDATE `ext_user_contactinfotype` SET `title` = 'LLL:contact.contactinfo.type.skype' WHERE `title` = 'user.contactinfo.skype' ;

ALTER TABLE `ext_user_contactinfotype` ADD `category` smallint(5) unsigned NOT NULL ;

UPDATE `ext_user_contactinfotype` SET `category` = '1' WHERE `title` = 'LLL:contact.contactinfo.type.email_business' ;
UPDATE `ext_user_contactinfotype` SET `category` = '2' WHERE `title` = 'LLL:contact.contactinfo.type.tel_private' ;
UPDATE `ext_user_contactinfotype` SET `category` = '2' WHERE `title` = 'LLL:contact.contactinfo.type.tel_exchange' ;
UPDATE `ext_user_contactinfotype` SET `category` = '2' WHERE `title` = 'LLL:contact.contactinfo.type.tel_business' ;
UPDATE `ext_user_contactinfotype` SET `category` = '1' WHERE `title` = 'LLL:contact.contactinfo.type.email_private' ;
UPDATE `ext_user_contactinfotype` SET `category` = '2' WHERE `title` = 'LLL:contact.contactinfo.type.mobile_business' ;
UPDATE `ext_user_contactinfotype` SET `category` = '2' WHERE `title` = 'LLL:contact.contactinfo.type.fax_private' ;
UPDATE `ext_user_contactinfotype` SET `category` = '2' WHERE `title` = 'LLL:contact.contactinfo.type.fax_business' ;
UPDATE `ext_user_contactinfotype` SET `category` = '2' WHERE `title` = 'LLL:contact.contactinfo.type.mobile_private' ;
UPDATE `ext_user_contactinfotype` SET `category` = '2' WHERE `title` = 'LLL:contact.contactinfo.type.fax_exchange' ;
UPDATE `ext_user_contactinfotype` SET `category` = '3' WHERE `title` = 'LLL:contact.contactinfo.type.website' ;
UPDATE `ext_user_contactinfotype` SET `category` = '3' WHERE `title` = 'LLL:contact.contactinfo.type.skype' ;

--
-- Rename user extension tables
--
RENAME TABLE `ext_user_address`  TO `ext_contact_address` ;
RENAME TABLE `ext_user_company`  TO `ext_contact_company` ;
RENAME TABLE `ext_user_contactinfo`  TO `ext_contact_contactinfo` ;
RENAME TABLE `ext_user_contactinfotype`  TO `ext_contact_contactinfotype` ;
RENAME TABLE `ext_user_jobtype`  TO `ext_contact_jobtype` ;
RENAME TABLE `ext_user_group`  TO `system_role` ;
RENAME TABLE `ext_user_mm_company_address`  TO `ext_contact_mm_company_address` ;
RENAME TABLE `ext_user_mm_company_contactinfo`  TO `ext_contact_mm_company_contactinfo` ;
RENAME TABLE `ext_user_mm_company_user`  TO `ext_contact_mm_company_person` ;
RENAME TABLE `ext_user_mm_user_address`  TO `ext_contact_mm_person_address` ;
RENAME TABLE `ext_user_panelwidget`  TO `system_panelwidget` ;
RENAME TABLE `ext_user_mm_user_contactinfo`  TO `ext_contact_mm_person_contactinfo` ;
RENAME TABLE `ext_user_mm_user_group`  TO `ext_contact_mm_person_role` ;
RENAME TABLE `ext_user_preference`  TO `system_preference` ;
RENAME TABLE `ext_user_right`  TO `system_right` ;
RENAME TABLE `ext_user_user`  TO `ext_contact_person` ;


--
-- Rename other tables with user or group in name
--
RENAME TABLE `ext_calendar_mm_event_user`  TO `ext_calendar_mm_event_person` ;
RENAME TABLE `ext_project_mm_project_user`  TO `ext_project_mm_project_person` ;
RENAME TABLE `ext_project_userrole`  TO `ext_project_role` ;


--
-- Rename all id_user_create
--
ALTER TABLE `ext_assets_asset` CHANGE `id_user_create` `id_person_create` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_bookmark_bookmark` CHANGE `id_user_create` `id_person_create` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_calendar_event` CHANGE `id_user_create` `id_person_create` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_calendar_holiday` CHANGE `id_user_create` `id_person_create` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_calendar_holidayset` CHANGE `id_user_create` `id_person_create` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_comment_comment` CHANGE `id_user_create` `id_person_create` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_comment_feedback` CHANGE `id_user_create` `id_person_create` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_comment_mailed` CHANGE `id_user_create` `id_person_create` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_contact_address` CHANGE `id_user_create` `id_person_create` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_contact_company` CHANGE `id_user_create` `id_person_create` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_contact_contactinfo` CHANGE `id_user_create` `id_person_create` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_contact_person` CHANGE `id_user_create` `id_person_create` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_project_project` CHANGE `id_user_create` `id_person_create` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_project_task` CHANGE `id_user_create` `id_person_create` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_project_role` CHANGE `id_user_create` `id_person_create` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_project_worktype` CHANGE `id_user_create` `id_person_create` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_search_filtercondition` CHANGE `id_user_create` `id_person_create` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_search_filterset` CHANGE `id_user_create` `id_person_create` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_timetracking_track` CHANGE `id_user_create` `id_person_create` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_timetracking_tracking` CHANGE `id_user_create` `id_person_create` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `system_log` CHANGE `id_user_create` `id_person_create` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `system_role` CHANGE `id_user_create` `id_person_create` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';


--
-- Rename all id_user and id_group
--
ALTER TABLE `ext_calendar_mm_event_person` CHANGE `id_user` `id_person` INT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_comment_feedback` CHANGE `id_user_feedback` `id_person_feedback` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_comment_mailed` CHANGE `id_user_mailed` `id_person_mailed` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_contact_mm_company_person` CHANGE `id_user` `id_person` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_contact_mm_person_address` CHANGE `id_user` `id_person` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_contact_mm_person_contactinfo` CHANGE `id_user` `id_person` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_contact_mm_person_role` CHANGE `id_user` `id_person` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_contact_mm_person_role` CHANGE `id_group` `id_role` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_project_mm_project_person` CHANGE `id_user` `id_person` INT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_project_mm_project_person` CHANGE `id_userrole` `id_role` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_search_filterset` CHANGE `usergroups` `roles` VARCHAR( 16 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `system_errorlog` CHANGE `id_user` `id_person` SMALLINT( 5 ) UNSIGNED NOT NULL;
ALTER TABLE `system_panelwidget` CHANGE `id_user` `id_person` SMALLINT( 5 ) UNSIGNED NOT NULL;
ALTER TABLE `system_preference` CHANGE `id_user` `id_person` SMALLINT( 5 ) UNSIGNED NOT NULL;
ALTER TABLE `system_right` CHANGE `id_group` `id_role` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_project_task` CHANGE `id_user_assigned` `id_person_assigned` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `ext_project_task` CHANGE `id_user_owner` `id_person_owner` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';


--
-- Other changes
--
ALTER TABLE `system_role` CHANGE `is_active` `active` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `ext_contact_person` CHANGE `gender` `salutation` VARCHAR(1) NOT NULL;
ALTER TABLE `ext_contact_jobtype` ADD `date_create` int(10) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `ext_contact_jobtype` ADD `date_update` int(10) unsigned NOT NULL;
ALTER TABLE `ext_contact_jobtype` ADD `id_person_create` smallint(5) unsigned NOT NULL;
ALTER TABLE `ext_project_project` DROP `date_finish`;
ALTER TABLE `ext_project_task` DROP `date_finish`;
