<?php
// wcf imports
require_once(WCF_DIR.'lib/data/search/SearchType.class.php');
require_once(WCF_DIR.'lib/data/search/SearchResult.class.php');

/**
 * Provides methods for searching the package database
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class PackageType extends SearchType {

	/**
	 * @see	SearchType::$searchableFields
	 */
	protected $searchableFields = array('language.packageName', 'language.description', 'version.plugin', 'version.licence', 'version.licenceUrl');

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
				package.packageID AS packageID,
				package.packageName AS packageName,
				language.name AS name,
				language.description AS description,
				server.serverID AS serverID,
				server.serverAlias AS serverAlias,
				server.serverUrl AS serverUrl
				version.version AS version,
				version.isUnique AS isUnique,
				version.standalone AS standalone,
				version.plugin AS plugin,
				version.packageUrl AS packageUrl,
				version.author AS author,
				version.authorUrl AS authorUrl
				".(!empty($additionalSelects) ? ','.$additionalSelects : "")."
			FROM
				www".WWW_N."_package package
			LEFT JOIN
				www".WWW_N."_package_version version
			ON
				package.lastVersionID = version.versionID
			LEFT JOIN
				www".WWW_N."_package_version_to_language language
			ON
				version.versionID = language.versionID
			LEFT JOIN
				www".WWW_N."_package_server server
			ON
				version.serverID = server.serverID
			WHERE
				(
						language.languageID = 1
					OR
						language.isFallback = 1
				)
			AND
				".$sqlConditions."
			ORDER BY
				INET_ATON(SUBSTRING_INDEX(CONCAT(version.version,'.0.0.0'),'.',4)) DESC,
				searchScore ASC";
		$result = WCF::getDB()->sendQuery($sql, $itemsPerPage, (($page - 1) * $itemsPerPage));

		// create needed array
		$resultList = array();

		// loop while fetching rows
		while ($row = WCF::getDB()->fetchArray($result)) {
			$resultList[] = new $this->searchResultClass($row);
		}

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
				".WWW_N."_package_version_to_language language
			ON
				version.versionID = language.versionID
			WHERE
				(
						language.languageID = 1
					OR
						language.isFallback = 1
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