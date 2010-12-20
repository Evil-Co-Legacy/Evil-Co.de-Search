<?php
// wcf imports
require_once(WCF_DIR.'lib/data/search/SearchType.class.php');

/**
 * Provides methods for searching the package database
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class PackageType extends SearchType {

	/**
	 * @see	SearchType::$searchableFields
	 */
	protected $searchableFields = array('packageName', 'description', 'plugin', 'licence', 'licenceUrl');

	/**
	 * @see	SearchType::$advancedSearchFields
	 */
	protected $advancedSearchFields = array('name');

	/**
	 * @see SearchType::formatResult()
	 */
	protected function formatResult($row) {
		$row['versions'] = unserialize($row['versions']);

		$result = array('ID' => $row['packageID'], 'title' => $row['packageName']." (".$row['name'].")", 'description' => $row['description'],  'additionalButtons' => '', 'suggestionField' => $row['packageName'], 'result' => $row);

		$result['additionalButtons'] = 'packageSearchTypeButtons';

		return $result;
	}
}
?>