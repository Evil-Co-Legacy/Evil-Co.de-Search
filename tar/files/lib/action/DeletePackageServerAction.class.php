<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');

/**
 * Deletes a server
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class DeletePackageServerAction extends AbstractAction {
	
	/**
	 * Contains the object ID of the server
	 * @var integer
	 */
	public $serverID = 0;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		WCF::getUser()->checkPermission('mod.search.canModerate');
		
		if (isset($_REQUEST['serverID'])) $this->serverID = intval($_REQUEST['serverID']);
	}
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		$sql = "DELETE FROM
				www".WWW_N."_package_server
			WHERE
				serverID = ".$this->serverID;
		WCF::getDB()->sendQuery($sql);
		
		HeaderUtil::redirect('index.php?page=PackageServerList'.SID_ARG_2ND_NOT_ENCODED.'#server'.$this->serverID);
		
		$this->executed();
	}
}
?>