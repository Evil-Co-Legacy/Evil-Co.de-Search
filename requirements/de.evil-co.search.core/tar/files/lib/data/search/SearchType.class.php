<?php
// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');
require_once(WCF_DIR.'lib/data/search/SearchResult.class.php');

/**
 * Provides default methods for searchable types
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class SearchType extends DatabaseObject {

	/**
	 * Contains additional SQL code for our JOIN clouse
	 * @var	string
	 */
	protected $sqlJoins = '';

	/**
	 * Contains additional SQL code for selects
	 * @var	string
	 */
	protected $sqlSelects = '';

	/**
	 * Contains additional SQL code for our GROUP BY clouse
	 * @var	string
	 */
	protected $sqlGroupBy = '';

	/**
	 * Contains the number of results in last search query
	 * @var	integer
	 */
	protected $lastSearchCount = 0;

	/**
	 * Contains a list of searchable fields
	 * @var	array<string>
	 */
	protected $searchableFields = array();

	/**
	 * Contains a list of advanced search fields
	 * @var	array<string>
	 */
	protected $advancedSearchFields = array();

	/**
	 * Contains selects for search query
	 * @var	string
	 */
	protected $searchQuerySelects = "*";

	/**
	 * Contains joins for search query
	 * @var	string
	 */
	protected $searchQueryJoins = "";

	/**
	 * Contains the name of result class
	 * Note: The given class should extend the predefined SearchResult class
	 * @var	SearchResult
	 */
	protected $searchResultClass = 'SearchResult';

	/**
	 * Contains the default count of results that should read from database
	 * @var	integer
	 */
	const DEFAULT_ITEMS_PER_PAGE = 20;

	/**
	 * Contains the default page no that should appear
	 * @var	integer
	 */
	const DEFAULT_PAGE_NO = 1;

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
			$sql = "SELECT
						".$this->sqlSelects."
					FROM
						wcf".WCF_N."_search_type type
						".$this->sqlJoins."
					WHERE
						".$sqlCondition.
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

		if (!$this->typeID) $this->data['typeID'] = 0;
	}

	/**
	 * Executes a search query for this search type
	 * @param	string	$query
	 * @param	integer	$page
	 * @param	integer	$itemsPerPage
	 */
	public function search($query, $page = self::DEFAULT_PAGE_NO, $itemsPerPage = self::DEFAULT_ITEMS_PER_PAGE) {
		// create needed variables
		$sqlConditions = "";

		// loop trhoug searchableFields and add them to query
		foreach($this->searchableFields as $field) {
			if (!empty($sqlConditions)) $sqlConditions .= " OR ";
			$sqlConditions .= "MATCH(`".$field."`) AGAINST('".escapeString($query)."' WITH QUERY EXPANSION)";
		}

		// execute query
		return $this->executeSearchQuery($sqlConditions);
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
	public function advancedSearch($query, $fields, $page = self::DEFAULT_PAGE_NO, $itemsPerPage = self::DEFAULT_ITEMS_PER_PAGE) {
		// create needed variables
		$sqlConditions = "";
		$additionalSqlSelects = "(";

		// catch empty queries (Yes we'll allow a search for packageNames and other things)
		if (!empty($query)) {
			// loop through searchable fields and add them to query
			foreach($this->searchableFields as $field) {
				if (!empty($sqlConditions)) $sqlConditions .= " OR ";
				$sqlConditions .= "MATCH(`".$field."`) AGAINST('".escapeString($query)."' WITH QUERY EXPANSION)";
				$additionalSqlSelects .= (strlen($additionalSqlSelects) > 1 ? ' + ' : '')."MATCH(`".$field."`) AGAINST('".escapeString($query)."' WITH QUERY EXPANSION)";
			}

			$sqlConditions = "( ".$sqlConditions." )";
		}
		$additionalSqlSelects .= ") AS searchScore";

		// add additional fields to query
		foreach($fields as $fieldName => $value) {
			if (!empty($sqlConditions)) $sqlConditions .= " AND ";
			$sqlConditions = "`".$fieldName."` = '".escapeString($value)."'";
		}

		// execute search query
		return $this->executeSearchQuery($sqlConditions, $additionalSqlSelects);
	}

	/**
	 * Executes a search query
	 * Note: This helps to unify search queries
	 * @param	string	$sqlConditions
	 */
	protected function executeSearchQuery($sqlConditions, $additionalSelects) {
		// get resultList
		$sql = "SELECT
					".$this->searchQuerySelects."
				FROM
					`".$this->searchTable."`
					".$this->searchQueryJoins."
				WHERE
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
					`".$this->searchTable."`
				WHERE
					".$sqlConditions;
		$count = WCF::getDB()->getFirstRow($sql);

		// write count to class property
		$this->lastSearchCount = intval($count['count']);

		// return list of results
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
}
?>