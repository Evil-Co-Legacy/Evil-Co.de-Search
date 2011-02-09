DROP TABLE IF EXISTS `www1_1_package_report`;
CREATE TABLE `www1_1_package_report` (
	`reportID` INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
	`packageID` INT NOT NULL,
	`authorID` INT NOT NULL,
	`authorName` VARCHAR (255) NOT NULL,
	`reason` TEXT NOT NULL
);