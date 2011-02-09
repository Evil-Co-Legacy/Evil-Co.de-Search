<?php
// wcf imports
require_once(WCF_DIR.'lib/data/search/SearchType.class.php');
require_once(WCF_DIR.'lib/data/search/SearchResult.class.php');

// www imports
require_once(WWW_DIR.'lib/data/search/PackageResult.class.php');

/**
 * Provides methods for searching the package database
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class PackageType extends SearchType {

	/**
	 * @see	SearchType::$searchableFields
	 */
	protected $searchableFields = array('packageLanguage.name', 'packageLanguage.description', 'version.plugin', 'version.licenseName', 'version.licenseUrl', 'version.author', 'version.authorUrl');

	/**
	 * @see	SearchType::$advancedSearchFields
	 */
	protected $advancedSearchFields = array('name');

	/**
	 * @see	SearchType::$searchResultClass
	 */
	protected $searchResultClass = 'PackageResult';

	/**
	 * @see SearchType::executeSearchQuery()
	 */
	protected function executeSearchQuery($sqlConditions, $additionalSelects, $itemsPerPage, $page) {
		// wuhahahaha ... monster query from hell :-D
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
				".(!empty($additionalSelects) ? ','.$additionalSelects : "")."
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
				".$sqlConditions."
			ORDER BY
				searchScore DESC";
		$result = WCF::getDB()->sendQuery($sql, $itemsPerPage, (($page - 1) * $itemsPerPage));

		// create needed array
		$resultList = array();
		$tempList = array();
		$bestValues = array();
		$fallbacks = array();
		
		// loop while fetching rows
		while ($row = WCF::getDB()->fetchArray($result)) {
			$resultList[] = new $this->searchResultClass($row, true);
		}
		
		foreach($resultList as $key => $result) {
			if ($result->isFallback)
				$fallbacks[$result->getResultID()] = $key;
			elseif ($result->languageID == WCF::getLanguage()->getLanguageID())
				$bestValues[$result->getResultID()] = $key;
		}
		
		foreach($fallbacks as $resultID => $key) {
			if (isset($bestValues[$resultID]))
				$tempList[] = $resultList[$bestValues[$resultID]];
			else
				$tempList[] = $resultList[$key];
		}
		
		$resultList = $tempList;

		// get count of all matching results
		$sql = "SELECT
				COUNT(*) AS count
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
			WHERE
				(
						packageLanguage.languageID = 1
					OR
						packageLanguage.isFallback = 1
				)
			AND
				".$sqlConditions;
		$count = WCF::getDB()->getFirstRow($sql);

		// write count to class property
		$this->lastSearchCount = intval($count['count']);

		// return list of results
		return $resultList;
	}
	
	/**
	 * @see SearchType::readGlobalInformation()
	 */
	public function readGlobalInformation($resultList) {
		$resultIDs = array();
		$versionIDs = array();
		
		foreach($resultList as $key => $result) {
			$resultIDs[$result->getResultID()] = $key;
			$versionIDs[$result->versionID] = $key;
		}
		
		// get requirements
		$sql = "SELECT
				targetPackageID AS packageID,
				targetVersionID AS versionID,
				packageLanguage.name AS name,
				packageLanguage.description,
				version.version,
				requirement.packageID AS parentPackageID
			FROM
				www".WWW_N."_package_version_requirement requirement
			LEFT JOIN
				www".WWW_N."_package_version version
			ON
				requirement.targetVersionID = version.versionID
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
				version.version,
				optional.packageID AS parentPackageID
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
		
		foreach($instructions as $packageID => $versionList) {
			$resultList[$resultIDs[$packageID]]->versions = $versionList;
		}
		
		return $resultList;
	}
}
?>