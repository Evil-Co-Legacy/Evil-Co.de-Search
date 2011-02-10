<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

// www imports
require_once(WWW_DIR.'lib/data/search/PackageResult.class.php');

/**
 * Displays a meta package server
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class GetPackageUpdateXMLPage extends AbstractPage {
	
	/**
	 * @see AbstractPage::$templateName
	 */
	public $templateName = 'getPackageUpdateXML';
	
	/**
	 * Contains all packages
	 * @var array
	 */
	public $resultList = array();
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		$sql = "SELECT
				package.packageID,
				package.packageName,
				packageLanguage.name AS name,
				packageLanguage.description,
				server.serverID,
				server.serverAlias,
				server.serverUrl,
				version.versionID,
				version.version,
				version.isUnique,
				version.standalone,
				version.plugin,
				version.packageUrl,
				version.author,
				version.authorUrl,
				version.licenseName,
				version.licenseUrl,
				version.downloadUrl,
				mirror.isEnabled AS mirrorEnabled,
				packageLanguage.isFallback,
				packageLanguage.languageID
			FROM
				www".WWW_N."_package package
			LEFT JOIN
				www".WWW_N."_package_version version
			ON
				package.lastVersionID = version.versionID
			LEFT JOIN
				www".WWW_N."_package_version_to_language packageLanguage
			ON
				version.versionID = packageLanguage.versionID
			LEFT JOIN
				www".WWW_N."_package_server server
			ON
				version.serverID = server.serverID
			LEFT JOIN
				www".WWW_N."_package_mirror AS mirror
			ON
				(package.packageID = package.packageID AND version.versionID = mirror.versionID)
			WHERE
				(
						packageLanguage.languageID = ".WCF::getLanguage()->getLanguageID()."
					OR
						packageLanguage.isFallback = 1
				)
			AND
				version.licenseUrl NOT IN ('')
			AND
				version.licenseName NOT IN ('')
			ORDER BY
				package.packageName ASC";
		$result = WCF::getDB()->sendQuery($sql);
		
		$resultList = array();
		
		while($row = WCF::getDB()->fetchArray($result)) {
			$resultList[] = new PackageResult($row, true);
		}
		
		$resultIDs = array();
		$versionIDs = array();
		
		foreach($resultList as $key => $result) {
			$resultIDs[$result->getResultID()] = $key;
			$versionIDs[$result->versionID] = $key;
		}
		
		// get requirements
		$sql = "SELECT
				requirement.packageName AS packageName,
				requirement.packageID AS parentPackageID
			FROM
				www".WWW_N."_package_version_requirement requirement
			WHERE
				requirement.versionID IN (".implode(',', array_keys($versionIDs)).")";
		$result = WCF::getDB()->sendQuery($sql);
		
		$requirements = array();
		while($row = WCF::getDB()->fetchArray($result)) {
			if (!isset($requirements[$row['parentPackageID']])) $requirements[$row['parentPackageID']] = array();
			$requirements[$row['parentPackageID']][] = new PackageResult($row, true);
		}
		
		// write arrays
		foreach($requirements as $packageID => $requirementList) {
			$resultList[$resultIDs[$packageID]]->requirements = $requirementList;
		}
		
		// get optionals
		$sql = "SELECT
				targetPackageID AS packageID,
				targetVersionID AS versionID,
				packageLanguage.name AS name,
				packageLanguage.description,
				optional.version,
				optional.packageID AS parentPackageID,
				optional.packageName AS packageName
			FROM
				www".WWW_N."_package_version_optional optional
			LEFT JOIN
				www".WWW_N."_package_version version
			ON
				optional.targetVersionID = version.versionID
			LEFT JOIN
				www".WWW_N."_package_version_to_language packageLanguage
			ON
				version.versionID = packageLanguage.versionID
			WHERE
				(
						packageLanguage.languageID = ".WCF::getLanguage()->getLanguageID()."
					OR
						packageLanguage.isFallback = 1
				)
			AND
				optional.versionID IN (".implode(',', array_keys($versionIDs)).")";
		$result = WCF::getDB()->sendQuery($sql);
		
		$optionals = array();
		while($row = WCF::getDB()->fetchArray($result)) {
			if (!isset($optionals[$row['parentPackageID']])) $optionals[$row['parentPackageID']] = array();
			$optionals[$row['parentPackageID']][] = new PackageResult($row, true);
		}
		
		// write arrays
		foreach($optionals as $packageID => $optionalsList) {
			$resultList[$resultIDs[$packageID]]->optionals = $optionalsList;
		}
		
		// get instructions
		$instructions = array();
		$sql = "SELECT
				pipList,
				packageID AS parentPackageID
			FROM
				www".WWW_N."_package_version_instruction
			WHERE
				versionID IN (".implode(',', array_keys($versionIDs)).")
			AND
				instructionType = 'install'"; // TODO: we should add support for all types
		$result = WCF::getDB()->sendQuery($sql);
		
		while($row = WCF::getDB()->fetchArray($result)) {
			$instructions[$row['parentPackageID']] = explode(',', $row['pipList']);
		}
		
		foreach($instructions as $packageID => $pipList) {
			$resultList[$resultIDs[$packageID]]->instructions = $pipList;
		}
		
		// get update instructions
		$updateInstructions = array();
		$sql = "SELECT
				pipList,
				packageID AS parentPackageID,
				fromVersion
			FROM
				www".WWW_N."_package_version_instruction
			WHERE
				versionID IN (".implode(',', array_keys($versionIDs)).")
			AND
				instructionType = 'update'";
		$result = WCF::getDB()->sendQuery($sql);
		
		while($row = WCF::getDB()->fetchArray($result)) {
			$updateInstructions[$row['parentPackageID']][$row['fromVersion']] = explode(',', $row['pipList']);
		}
		
		foreach($updateInstructions as $packageID => $pipList) {
			$resultList[$resultIDs[$packageID]]->updateInstructions = $pipList;
		}
		
		// get versions
		$versions = array();
		$sql = "SELECT
				version.*,
				mirror.isEnabled AS mirrorEnabled
			FROM
				www".WWW_N."_package_version version
			LEFT JOIN
				www".WWW_N."_package_mirror mirror
			ON
				version.versionID = mirror.versionID
			WHERE
				version.packageID IN (".implode(',', array_keys($resultIDs)).")
			ORDER BY
				version.version DESC";
		$result = WCF::getDB()->sendQuery($sql);
		
		while($row = WCF::getDB()->fetchArray($result)) {
			if (!isset($versions[$row['packageID']])) $versions[$row['packageID']] = array();
			$versions[$row['packageID']][] = $row;
		}
		
		foreach($versions as $packageID => $versionList) {
			$resultList[$resultIDs[$packageID]]->versions = $versionList;
		}
		
		$this->resultList = $resultList;
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		$this->readParameters();
		$this->readData();
		$this->assignVariables();
		
		// send header
		header('Content-Type: application/xml');
		
		// display template
		echo WCF::getTPL()->fetch($this->templateName);
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'resultList'	=>	$this->resultList
		));
	}
}
?>