DROP TABLE IF EXISTS `www1_1_package`;
CREATE TABLE `www1_1_package` (
	`packageID` INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
	`packageName` VARCHAR (255) NOT NULL,
	`serverID` INT NOT NULL,
	`lastVersionID` INT NULL,
	`isDisabled` TINYINT (1) NOT NULL DEFAULT '0',
	`disableReason` TEXT NULL,
	`moderatorID` INT NULL,
	`moderatorName` VARCHAR (255) NULL
);

DROP TABLE IF EXISTS `www1_1_package_report`;
CREATE TABLE `www1_1_package_report` (
	`reportID` INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
	`packageID` INT NOT NULL,
	`authorID` INT NOT NULL,
	`authorName` VARCHAR (255) NOT NULL,
	`reason` TEXT NOT NULL,
	`state` ENUM ('read', 'new') DEFAULT 'new'
);

DROP TABLE IF EXISTS `www1_1_package_mirror`;
CREATE TABLE `www1_1_package_mirror` (
	`packageID` INT NOT NULL,
	`versionID` INT NOT NULL,
	`isEnabled` TINYINT (1) NOT NULL
);

DROP TABLE IF EXISTS `www1_1_package_version`;
CREATE TABLE `www1_1_package_version` (
	`versionID` INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
	`packageID` INT NOT NULL,
	`version` VARCHAR (255) NOT NULL,
	`isUnique` TINYINT (1) NOT NULL,
	`standalone` TINYINT (1) NOT NULL,
	`plugin` VARCHAR (255) NOT NULL,
	`packageUrl` TEXT NOT NULL,
	`author` VARCHAR (255) NOT NULL,
	`authorUrl` TEXT NULL,
	`serverID` INT NOT NULL,
	`licenseName` VARCHAR (255) NOT NULL,
	`licenseUrl` TEXT NOT NULL,
	`downloadUrl` TEXT NOT NULL,
	`timestamp` INT NOT NULL,
	FULLTEXT (plugin),
	FULLTEXT (licenseName),
	FULLTEXT (licenseUrl),
	FULLTEXT (author),
	FULLTEXT (authorUrl)
);

DROP TABLE IF EXISTS `www1_1_package_version_to_language`;
CREATE TABLE `www1_1_package_version_to_language` (
	`versionID` INT NOT NULL,
	`packageID` INT NOT NULL,
	`languageID` INT NOT NULL,
	`name` VARCHAR (255) NOT NULL,
	`description` VARCHAR (255) NOT NULL,
	`isFallback` TINYINT (1) NOT NULL DEFAULT '0',
	FULLTEXT (name),
	FULLTEXT (description)
);

DROP TABLE IF EXISTS `www1_1_package_version_requirement`;
CREATE TABLE `www1_1_package_version_requirement` (
	`requirementID` INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
	`versionID` INT NOT NULL,
	`packageID` INT NOT NULL,
	`targetPackageID` INT NOT NULL,
	`targetVersionID` INT NOT NULL DEFAULT '0',
	`packageName` VARCHAR (255) NOT NULL,
	`version` VARCHAR (255) NOT NULL
);

DROP TABLE IF EXISTS `www1_1_package_version_optional`;
CREATE TABLE `www1_1_package_version_optional` (
	`optionalID` INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
	`versionID` INT NOT NULL,
	`packageID` INT NOT NULL,
	`targetPackageID` INT NOT NULL,
	`targetVersionID` INT NOT NULL DEFAULT '0',
	`packageName` VARCHAR (255) NOT NULL,
	`version` VARCHAR (255) NOT NULL
);

DROP TABLE IF EXISTS `www1_1_package_version_instruction`;
CREATE TABLE `www1_1_package_version_instruction` (
	`instructionID` INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
	`versionID` INT NOT NULL,
	`packageID` INT NOT NULL,
	`instructionType` ENUM ('install', 'update'),
	`fromVersion` VARCHAR (255) NULL,
	`pipList` TEXT NOT NULL
);

DROP TABLE IF EXISTS `www1_1_package_server`;
CREATE TABLE `www1_1_package_server` (
	`serverID` INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
	`serverAlias` VARCHAR (255) NOT NULL,
	`serverUrl` TEXT NOT NULL,
	`homepage` TEXT NULL,
	`description` TEXT NOT NULL,
	`lastUpdate` INT NOT NULL,
	`lastError` TEXT NOT NULL,
	`isDisabled` TINYINT (1) NOT NULL DEFAULT '0'
);

DROP TABLE IF EXISTS `www1_1_package_server_request`;
CREATE TABLE `www1_1_package_server_request` (
	`requestID` INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
	`serverAlias` VARCHAR (255) NOT NULL,
	`serverUrl` TEXT NOT NULL,
	`homepage` TEXT NOT NULL,
	`description` TEXT NOT NULL,
	`authorID` INT NOT NULL,
	`authorName` VARCHAR (255) NOT NULL,
	`moderatorID` INT NULL,
	`moderatorName` VARCHAR (255) NULL,
	`state` ENUM ('accepted', 'pending', 'rejected', 'waiting')
);

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