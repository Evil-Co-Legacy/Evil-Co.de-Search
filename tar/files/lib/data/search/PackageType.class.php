<?php
// wcf imports
require_once(WCF_DIR.'lib/data/search/SearchType.class.php');


class PackageType extends SearchType {
	protected $searchableFields = array('packageName', 'description', 'plugin', 'licence', 'licenceUrl');
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