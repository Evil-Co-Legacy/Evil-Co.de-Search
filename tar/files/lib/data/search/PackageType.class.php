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
	protected $searchableFields = array('packageLanguage.name', 'packageLanguage.description', 'version.plugin', 'version.licenseName', 'version.licenseUrl');

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
				searchScore ASC";
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
}
?>