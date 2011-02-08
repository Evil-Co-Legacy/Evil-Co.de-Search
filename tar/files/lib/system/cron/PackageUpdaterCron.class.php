<?php
// wcf imports
require_once(WCF_DIR.'lib/data/cron/update/type/CronUpdateType.class.php');

// www imports
require_once(WWW_DIR.'lib/acp/package/WWWPackageUpdate.class.php');

/**
 * Updates the package indexes
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class PackageUpdaterCron extends CronUpdateType {

	/**
	 * @see CronUpdateType::execute()
	 */
	public function execute() {
		WWWPackageUpdate::refreshPackageDatabaseAutomatically();
	}
}
?>