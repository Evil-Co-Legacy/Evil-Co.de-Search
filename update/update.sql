ALTER TABLE `www1_1_package` ADD `isDisabled` TINYINT (1) NOT NULL DEFAULT '0';
ALTER TABLE `www1_1_package` ADD `disableReason` TEXT NULL;
ALTER TABLE `www1_1_package` ADD `moderatorID` INT NULL;
ALTER TABLE `www1_1_package` ADD `moderatorName` VARCHAR (255) NULL;

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

DROP TABLE IF EXISTS `www1_1_package_license_blacklist`;
CREATE TABLE `www1_1_package_license_blacklist` (
	`licenseID` INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
	`licenseRegex` TEXT NOT NULL,
	`banReason` VARCHAR (255) NOT NULL,
	`isEnabled` TINYINT (1) NOT NULL
);

DROP TABLE IF EXISTS `www1_1_package_license_blacklist_request`;
CREATE TABLE `www1_1_package_license_blacklist_request` (
	`requestID` INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
	`licenseRegex` TEXT NOT NULL,
	`banReason` VARCHAR (255) NOT NULL,
	`state` ENUM ('accepted', 'waiting', 'rejected'),
	`authorID` INT NOT NULL,
	`authorName` VARCHAR (255) NOT NULL,
	`moderatorID` INT NULL,
	`moderatorName` VARCHAR (255) NULL
);

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
	`expire` INT NOT NULL,
	`banEnabled` TINYINT (1) NOT NULL,
	`badLoginCount` INT NOT NULL DEFAULT '1'
);