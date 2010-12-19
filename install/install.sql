DROP TABLE IF EXISTS `package`;
CREATE TABLE `package` (
  `packageID` varchar(255) NOT NULL,
  `name` text NOT NULL,
  `serverID` int(11) NOT NULL,
  `packageName` text NOT NULL,
  `description` text NOT NULL,
  `isPlugin` tinyint(1) NOT NULL,
  `plugin` text NOT NULL,
  `isStandalone` tinyint(1) NOT NULL,
  `isPackage` tinyint(1) NOT NULL,
  `licence` text NOT NULL,
  `licenceUrl` text NOT NULL,
  `versions` text NOT NULL,
  `isDeleted` tinyint(4) NOT NULL DEFAULT '0',
  `token` text NOT NULL,
  PRIMARY KEY (`packageID`),
  FULLTEXT KEY `name` (`name`),
  FULLTEXT KEY `packageName` (`packageName`),
  FULLTEXT KEY `description` (`description`),
  FULLTEXT KEY `plugin` (`plugin`),
  FULLTEXT KEY `licence` (`licence`),
  FULLTEXT KEY `licenceUrl` (`licenceUrl`)
);

DROP TABLE IF EXISTS `server`;
CREATE TABLE `server` (
  `serverID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `serverHomepage` varchar(255) NOT NULL,
  PRIMARY KEY (`serverID`),
  UNIQUE KEY `url` (`url`)
);