<?php
// wcf imports
require_once(WCF_DIR.'lib/data/cron/update/type/CronUpdateType.class.php');

/**
 * Manages all search crons
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class WWWUpdateCronManager {

	/**
	 * Contains all cron update types
	 * @var	array<CronUpdateType>
	 */
	protected $updateCronTypes = array();

	/**
	 * Creates a new instance of WWWUpdateCronManager
	 */
	public function __construct() {
		$this->readCache();
	}

	/**
	 * Loads the cron type cache
	 * @TODO This will NOT read a cache file yet.
	 */
	protected function readCache() {
		$this->updateCronTypes = CronUpdateType::getUpdateCronTypes();
	}

	/**
	 * Runs all cron types
	 */
	public function startUpdate() {
		foreach($this->updateCronTypes as $type) {
			$type->execute();
		}
	}
}
?>