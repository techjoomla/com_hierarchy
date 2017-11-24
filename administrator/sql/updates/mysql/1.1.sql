ALTER TABLE `#__hierarchy_users` CHANGE `reports_to` `user_id` INT(11);
ALTER TABLE `#__hierarchy_users` CHANGE `subuser_id` `user_id` INT(11);
ALTER TABLE `#__hierarchy_users` CHANGE `client` `context` varchar(255);
ALTER TABLE `#__hierarchy_users` CHANGE `client_id` `context_id` varchar(255);
ALTER TABLE `#__hierarchy_users` add column `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `#__hierarchy_users` add column `created_by` int(11) NULL DEFAULT '0' AFTER `created_date`;
ALTER TABLE `#__hierarchy_users` add column `modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `created_by`;
ALTER TABLE `#__hierarchy_users` add column `modified_by` int(11) NULL DEFAULT '0' AFTER `modified_date`;

