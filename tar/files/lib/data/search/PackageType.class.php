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
	protected function executeSearchQuery($sqlConditions) {
		$sql = "SELECT
					package.packageID AS packageID,
					package.packageName AS packageName,
					language.name AS name,
					language.description AS description
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