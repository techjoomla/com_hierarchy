-- Adding default value for all the columns

ALTER TABLE `#__hierarchy_users`
	CHANGE `context` `context` VARCHAR(255)  NOT NULL DEFAULT '',
	CHANGE `context_id` `context_id` INT(11)  NOT NULL DEFAULT 0,
	CHANGE `created_by` `created_by` INT(11)  NOT NULL DEFAULT 0,
	CHANGE `modified_by` `modified_by` INT(11)  NOT NULL DEFAULT 0,
	CHANGE `state` `state` INT(11)  NOT NULL DEFAULT 0,
	CHANGE `note` `note` TEXT NOT NULL DEFAULT '';
