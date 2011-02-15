<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');

/**
 * Toggles (Sets isDisabled field) packages
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class TogglePackageAction extends AbstractAction {
	
	/**
	 * Contains the ID of the package that should toggled
	 */
	public $packageID = 0;
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// permission check
		WCF::getUser()->checkPermission('mod.search.canModerate');
		
		// get value of packageID query argument
		if (isset($_REQUEST['packageID'])) $this->packageID = intval($_REQUEST['packageID']);
	}
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		$sql = "UPDATE
				www".WWW_N."_package
			SET
				isDisabled = IF(isDisabled = 1, 0, 1),
				moderatorID = ".WCF::getUser()->userID.",
				moderatorName = '".escapeString(WCF::getUser()->username)."'
			WHERE
				packageID = ".$this->packageID;
		WCF::getDB()->sendQuery($sql);
		
		// send redirect header
		HeaderUtil::redirect('index.php?page=ResultDetail&resultID='.$this->packageID.'&searchTypeName=PackageType'.SID_ARG_2ND_NOT_ENCODED);
		
		// fire event
		$this->executed();
	}
}
?>