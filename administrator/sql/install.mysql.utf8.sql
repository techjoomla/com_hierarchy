CREATE TABLE IF NOT EXISTS `#__hierarchy_users` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
`user_id` INT(11)  NOT NULL,
`subuser_id` INT(11)  NOT NULL ,
`client` VARCHAR(255)  NOT NULL ,
`client_id` INT(11)  NOT NULL,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

