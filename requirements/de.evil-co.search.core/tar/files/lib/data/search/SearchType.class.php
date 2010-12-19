<?php
// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');

class SearchType extends DatabaseObject {
	protected $sqlJoins = '';
	protected $sqlSelects = '';
	protected $sqlGroupBy = '';
	
	protected $lastSearchCount = 0;
	
	protected $searchableFields = array();
	protected $advancedSearchFields = array();
	
	/**
	 * Reads a search type row from database
	 * @param	integer	$userID
	 * @param	array	$row
	 * @param	integer	$isleID
	 */
	public function __construct($typeID, $row = null) {
		$this->sqlSelects .= 'type.*'; 
		
		// create sql conditions
		$sqlCondition = '';
		
		if ($typeID !== null) {
			$sqlCondition .=  "type.typeID = ".$typeID;
		}
		
		// execute sql statement
		if (!empty($sqlCondition)) {
			$sql = "SELECT 	".$this->sqlSelects."
				FROM 	wcf".WCF_N."_search_type type
					".$this->sqlJoins."
				WHERE 	".$sqlCondition.
					$this->sqlGroupBy;
			$row = WCF::getDB()->getFirstRow($sql);
		}
		
		// handle result set
		parent::__construct($row);
	}
	
	/**
	 * @see DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		parent::handleData($data);
		
		if (!$this->typeID)
			$this->data['typeID'] = 0;
	}
	
	/**
	 * Executes a search query for this search type
	 * @param	string	$query
	 * @param	integer	$page
	 * @param	integer	$itemsPerPage
	 */
	public function search($query, $page = 1, $itemsPerPage = 20) {
		$sqlConditions = "";
		
		foreach($this->searchableFields as $field) {
			if (!empty($sqlConditions)) $sqlConditions .= " OR ";
			$sqlConditions .= "MATCH(`".$field."`) AGAINST('".escapeString($query)."' WITH QUERY EXPANSION)";
		}
		
		$sql = "SELECT
					*
				FROM
					`".$this->searchTable."`
				WHERE
					".$sqlConditions;
		$result = WCF::getDB()->sendQuery($sql, $itemsPerPage, (($page - 1) * $itemsPerPage));
		
		$resultList = array();
		
		while ($row = WCF::getDB()->fetchArray($result)) {
			$resultList[] = $this->formatResult($row);
		}
		
		$sql = "SELECT
					COUNT(*) AS count
				FROM
					`".$this->searchTable."`
				WHERE
					".$sqlConditions;
		$count = WCF::getDB()->getFirstRow($sql);
		
		$this->lastSearchCount = intval($count['count']);
		
		return $resultList;
	}
	
	/**
	 * Executes a advanced search
	 * The $fields arra should build like this:
	 * Array(	[field] => value
	 * 			[field2] => value2 )
	 * 
	 * @param	string	$query
	 * @param	array	$fields
	 * @param	integer	$page
	 * @param	integer	$itemsPerPage
	 */
	public function advancedSearch($query, $fields, $page = 1, $itemsPerPage = 20) {
		$sqlConditions = "";
		
		if (!empty($query)) {
			foreach($this->searchableFields as $field) {
				if (!empty($sqlConditions)) $sqlConditions .= " OR ";
				$sqlConditions .= "MATCH(`".$field."`) AGAINST('".escapeString($query)."' WITH QUERY EXPANSION)";
			}
			
			$sqlConditions = "( ".$sqlConditions." )";
		}
		
		foreach($fields as $fieldName => $value) {
			if (!empty($sqlConditions)) $sqlConditions .= " AND ";
			$sqlConditions = "`".$fieldName."` = '".escapeString($value)."'";
		}
		
		$sql = "SELECT
					*
				FROM
					`".$this->searchTable."`
				WHERE
					".$sqlConditions;
		$result = WCF::getDB()->sendQuery($sql, $itemsPerPage, (($page - 1) * $itemsPerPage));
		
		$resultList = array();
		
		while ($row = WCF::getDB()->fetchArray($result)) {
			$resultList[] = $this->formatResult($row);
		}
		
		$sql = "SELECT
					COUNT(*) AS count
				FROM
					`".$this->searchTable."`
				WHERE
					".$sqlConditions;
		$count = WCF::getDB()->getFirstRow($sql);
		
		$this->lastSearchCount = intval($count['count']);
		
		return $resultList;
	}
	
	/**
	 * Returnes all advanced 
	 */
	public function getAdvancedSearchFields() {
		return $this->advancedSearchFields;
	}
	
	/**
	 * Returnes the count of the last search
	 */
	public function getResultCount() {
		return $this->lastSearchCount;
	}
	
	/**
	 * Formats the result for our output (so no errors should occour ;-))
	 * @param	array	$row
	 */
	protected function formatResult($row) {
		return array('ID' => '', 'title' => '', 'description' => '', 'additionalButtons' => '');
	}
}
?>