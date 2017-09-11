-- --------------------------------------------------------------
--
-- Table structure for table `#__hierarchy_users`
--
CREATE TABLE IF NOT EXISTS `#__hierarchy_users` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`user_id` INT(11)  NOT NULL,
`reports_to` INT(11)  NOT NULL,
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
