--
-- Alter mailing log table to enable storing multiple types of email receivers instead of only persons, e.g. IMAP address
--

ALTER TABLE `system_log_email` ADD `receiver_type` varchar(32) NOT NULL DEFAULT 'contactperson';
ALTER TABLE `system_log_email` CHANGE `id_person_email` `id_receiver` int(10) unsigned NOT NULL;