<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');

/**
 * Deletes specified reports
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class PackageReportDeleteAction extends AbstractAction {
	
	/**
	 * Contains the ID of the report that should deleted
	 */
	public $reportID = 0;
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// permission check
		WCF::getUser()->checkPermission('mod.search.canModerate');
		
		// get value of packageID query argument
		if (isset($_REQUEST['reportID'])) $this->reportID = intval($_REQUEST['reportID']);
	}
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		$sql = "DELETE FROM
				www".WWW_N."_package_report
			WHERE
				reportID = ".$this->reportID;
		WCF::getDB()->sendQuery($sql);
		
		// send redirect header
		HeaderUtil::redirect('index.php?page=PackageReports'.SID_ARG_2ND_NOT_ENCODED);
		
		// fire event
		$this->executed();
	}
}
?>