DROP TABLE IF EXISTS `www1_1_package_report`;
CREATE TABLE `www1_1_package_report` (
	`reportID` INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
	`packageID` INT NOT NULL,
	`authorID` INT NOT NULL,
	`authorName` VARCHAR (255) NOT NULL,
	`reason` TEXT NOT NULL
);

ALTER TABLE `www1_1_package_version` ADD FULLTEXT (author);
ALTER TABLE `www1_1_package_version` ADD FULLTEXT (authorUrl);
ALTER TABLE `www1_1_package_version` ADD `timestamp` INT NOT NULL;

DROP TABLE IF EXISTS `www1_1_api_key`;
CREATE TABLE `www1_1_api_key` (
	`keyID` INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
	`publicKey` VARCHAR (255) NOT NULL,
	`secretKey` VARCHAR (255) NOT NULL,
	`ownerID` INT NOT NULL,
	`ownerName` VARCHAR (255) NOT NULL
);

DROP TABLE IF EXISTS `www1_1_api_key_whitelist`;
CREATE TABLE `www1_1_api_key_whitelist` (
	`keyID` INT NOT NULL,
	`ipAddress` TEXT NULL,
	`hostname` TEXT NULL,
	`isEnabled` TINYINT (1) NOT NULL DEFAULT '1'
);

DROP TABLE IF EXISTS `www1_1_api_key_blacklist`;
CREATE TABLE `www1_1_api_key_blacklist` (
	`banID` INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
	`ipAddress` TEXT NULL,
	`hostname` TEXT NULL,
	`timestamp` INT NOT NULL,
	`banEnabled` TINYINT (1) NOT NULL,
	`badLoginCount` INT NOT NULL DEFAULT '1'
);