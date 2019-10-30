CREATE TABLE `event_store_snapshots` (
	`aggregate_id` CHAR(36) NOT NULL,
	`aggregate_type` VARCHAR(255) NOT NULL,
	`aggregate_root` MEDIUMTEXT NOT NULL,
	`aggregate_version` INT(11) NOT NULL,
	`created_at` DATETIME NOT NULL,
	PRIMARY KEY (`aggregate_id`)
)
ENGINE=InnoDB;
