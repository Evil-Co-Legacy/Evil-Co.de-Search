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

	/**
	 * Returnes all available types
	 */
	public static function getUpdateCronTypes() {
		$types = array();

		$sql = "SELECT
				type.*,
				package.packageDir
			FROM
				wcf".WCF_N."_cron_update_type type
			LEFT JOIN
				wcf".WCF_N."_package package
			ON
				type.packageID = package.packageID";
		$result = WCF::getDB()->sendQuery($sql);

		while($row = WCF::getDB()->fetchArray($result)) {
			$types[] = new CronUpdateType(null, $row);
		}
		
		foreach($types as $key => $type) {
			if (!file_exists(WCF_DIR.$type->packageDir.$type->file)) throw new SystemException("Could not find class file ".WCF_DIR.$type->packageDir.$type->file);
			require_once(WCF_DIR.$type->packageDir.$type->file);
			
			$className = basename($type->file, '.class.php');
			$types[$key] = new $className(null, $type->getData());
		}

		return $types;
	}
	
	/**
	 * Returnes the complete dara array
	 */
	public function getData() {
		return $this->data;
	}
}
?>