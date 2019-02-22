-- --------------------------------------------------------------
--
-- Table structure for table `#__hierarchy_users`
--
CREATE TABLE IF NOT EXISTS `#__hierarchy_users` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`user_id` INT(11)  NOT NULL COMMENT 'This field is to store user id of user',
`reports_to` INT(11)  NOT NULL COMMENT 'This field is to select to whom all current user reports to',
`context` VARCHAR(255)  NOT NULL ,
`context_id` INT(11)  NOT NULL,
`created_by` INT(11)  NOT NULL,
`modified_by` INT(11)  NOT NULL,
`created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
`state` INT(11)  NOT NULL,
`note` TEXT NOT NULL,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

ALTER TABLE `#__hierarchy_users` CHANGE `user_id` `reports_to`  INT(11);
ALTER TABLE `#__hierarchy_users` CHANGE `subuser_id` `user_id` INT(11);
ALTER TABLE `#__hierarchy_users` CHANGE `client` `context` varchar(255);
ALTER TABLE `#__hierarchy_users` CHANGE `client_id` `context_id` varchar(255);
ALTER TABLE `#__hierarchy_users` add column `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `#__hierarchy_users` add column `created_by` int(11) NULL DEFAULT '0' AFTER `created_date`;
ALTER TABLE `#__hierarchy_users` add column `modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `created_by`;
ALTER TABLE `#__hierarchy_users` add column `modified_by` int(11) NULL DEFAULT '0' AFTER `modified_date`;

