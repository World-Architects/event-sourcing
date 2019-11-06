DROP TABLE IF EXISTS `event_store_snapshots`;

CREATE TABLE `event_store_snapshots` (
	`aggregate_id` CHAR(36) NOT NULL,
	`aggregate_type` VARCHAR(255) NOT NULL,
	`aggregate_root` MEDIUMTEXT NOT NULL,
	`aggregate_version` INT(11) NOT NULL,
	`created_at` DATETIME NOT NULL,
	PRIMARY KEY (`aggregate_id`)
)
ENGINE=InnoDB;

INSERT INTO `event_store_snapshots` (`aggregate_id`, `aggregate_type`, `aggregate_root`, `aggregate_version`, `created_at`)
VALUES ('2a14717c-3c8f-43ee-8cfb-1ad1cc04d9d6', 'Psa\\EventSourcing\\Test\\TestApp\\Domain\\Account', '{"id":"2a14717c-3c8f-43ee-8cfb-1ad1cc04d9d6","name":"test","description":"test"}', 1, '2019-10-17 12:48:31');
