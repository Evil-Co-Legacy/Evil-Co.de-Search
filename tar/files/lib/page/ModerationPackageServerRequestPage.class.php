<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Displays a list of server requests
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class ModerationPackageServerRequestPage extends AbstractPage {
	
	/**
	 * @see AbstractPage::$templateName
	 */
	public $templateName = 'moderationPackageServerRequest';
	
	/**
	 * @see AbstractPage::$neededPermissions
	 */
	public $neededPermissions = 'mod.search.canModerate';
	
	/**
	 * Contains waiting requests
	 * @var array
	 */
	public $requests = array();
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		$sql = "SELECT
				*
			FROM
				www".WWW_N."_package_server_request
			".(!isset($_REQUEST['showOthers']) ? "WHERE state = 'waiting'" : "");
		$result = WCF::getDB()->sendQuery($sql);
		
		while($row = WCF::getDB()->sendQuery($sql)) {
			$this->requests[] = $row;
		}
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// activate usercpmenu
		require_once(WCF_DIR.'lib/page/util/menu/UserCPMenu.class.php');
		UserCPMenu::getInstance()->setActiveMenuItem('www.user.usercp.menu.link.modcp.packageServerRequests');
		
		parent::show();
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'requests'		=>	$this->requests
		));
	}
}
?>