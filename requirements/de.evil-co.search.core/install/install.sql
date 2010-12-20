DROP TABLE IF EXISTS `wcf1_search_type`;
CREATE TABLE `wcf1_search_type` (
	`typeID` INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
	`packageID` INT NOT NULL DEFAULT '0',
	`typeName` VARCHAR (255) NOT NULL,
	`searchTable` VARCHAR (255) NOT NULL,
	`isDefault` TINYINT (1) NOT NULL DEFAULT '0',
	`isInDevelelopment` TINYINT (1) NOT NULL DEFAULT '0',
	`isDisabled` TINYINT (1) NOT NULL DEFAULT '0'
);

DROP TABLE IF EXISTS `wcf1_cron_update_type`;
CREATE TABLE `wcf1_cron_update_type` (
	`typeID` INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
	`packageID` INT NOT NULL DEFAULT '0',
	`file` TEXT NOT NULL
);