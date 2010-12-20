<?php
// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');

/**
 * Provides default methods for update crons
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class CronUpdateType extends DatabaseObject {

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
	 * Reads a search type row from database
	 * @param	integer	$typeID
	 * @param	array	$row
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
						wcf".WCF_N."_cron_update_type type
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
	 * Creates or updates the search index for this type
	 */
	public function execute() {
		// nothing to do here
	}
}
?>